<?php

namespace G28\VistasoftMonitor\VistaSoft;

use Exception;
use G28\VistasoftMonitor\Core\Logger;
use G28\VistasoftMonitor\Core\OptionManager;
use G28\VistasoftMonitor\Core\TermTaxonomies;

class PropertiesManager
{

	const ADD = 'add';
	const UPDATE = 'update';
	const REMOVE = 'remove';

	const POSTTYPE = 'imovel';
	const BOATTYPE = 'embarcacao';
	private array $dbKeys;

	public function __construct()
	{
		$this->dbKeys = $this->getIdsFromDB();
	}

	private function doImports()
	{
		require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		require_once(ABSPATH . '/wp-admin/includes/media.php');
		require_once(ABSPATH . '/wp-admin/includes/file.php');
		require_once(ABSPATH . '/wp-admin/includes/image.php');
	}

	private function getIdsFromDB(): array
	{
		$codes = [];
		global $wpdb;
		$sql = "select meta_value from " . $wpdb->prefix . "postmeta where meta_key = 'codigo' and post_id in (select ID from " . $wpdb->prefix . "posts where post_type = '" . self::POSTTYPE . "')";
		$result = $wpdb->get_results($sql, ARRAY_A);
		foreach ($result as $row) {
			$codes[] = $row['meta_value'];
		}

		return $codes;
	}

	public function run($codes)
	{
		$this->doImports();
		foreach ($codes as $code) {
			sleep(3);
			$exibir 	= $code['exibir'];
			$codigo 	= $code['codigo'];
			try {
				if ( $exibir !== "Sim") {
					Logger::getInstance()->add("Imóvel: " . $codigo . " não deve ser exibido no site");
					$this->remove($codigo);
				} else {
					$client = new Client();
					$data = $client->getRealStateData($codigo);
					[$meta_values, $terms] = $this->mapColumns($data);
					if (in_array($codigo, $this->dbKeys)) {
						Logger::getInstance()->add("Atualizando dados do imóvel: " . $codigo);
					} else {
						Logger::getInstance()->add("Cadastrando o imóvel: " . $codigo);
					}
					$this->updateDatabaseContent($meta_values, $codigo, $terms);
					$key = array_search($codigo, $this->dbKeys);
					if ($key !== false) {
						unset($this->dbKeys[$key]);
					}

				}
			} catch (Exception $e) {
				Logger::getInstance()->add("Erro ao processar o imóvel de código " . $codigo . ": " . $e->getMessage());
			}
		}
		Logger::getInstance()->add("Removendo imóveis presentes no banco, mas não retornados pelo CRM");
		foreach ($this->dbKeys as $key) {
			Logger::getInstance()->add("Removendo o imóvel: " . $key);
			$this->cleanDatabaseAndMediaContent($key);
		}
		Logger::getInstance()->add("Importação dos imóveis finalizada!");
	}

	public function remove($code)
	{
		$this->doImports();
		try {
			Logger::getInstance()->add("Removendo o imóvel: " . $code);
			$this->cleanDatabaseAndMediaContent($code);
		} catch (Exception $e) {
			Logger::getInstance()->add("Erro ao remover o imóvel no BD: " . $e->getMessage());
		}
	}

	private function updateDatabaseContent($values, $code, $terms = [], $isEnterprise = false)
	{
		$post = $this->getPostByMetaCode($code);
		list($title, $type) = $this->checkIfEmbarcacao($values);
		$action = is_null($post) ? "cadastrado" : "atualizado";
		if (is_null($post)) {
			$post = wp_insert_post([
				'post_title' => $title,
				'post_content' => empty($values['descricao-do-imovel']) ? "" : $values['descricao-do-imovel'],
				'post_status' => 'publish',
				'post_type' => $type
			]);
			
			foreach ($values as $key => $value) {
				add_post_meta($post, $key, $value);
				if (count($terms) > 0) {
					wp_set_object_terms($post, $terms, TermTaxonomies::TAXONOMY_STATE_CITY);
				}
			}
		} else {
			Logger::getInstance()->add("Atualizando post " . $post['ID'] . " metadata.");
			wp_update_post([
				'ID' => $post['ID'],
				'post_title' => $title,
				'post_content' => empty($values['descricao-do-imovel']) ? "" : $values['descricao-do-imovel'],
				'post_status' => 'publish',
				'post_type' => $type
			]);
			foreach ($values as $key => $value) {
				update_post_meta($post['ID'], $key, $value);
				if (count($terms) > 0) {
					wp_delete_object_term_relationships($post, TermTaxonomies::TAXONOMY_STATE_CITY);
					wp_set_object_terms($post['ID'], $terms, TermTaxonomies::TAXONOMY_STATE_CITY);
				}
				// if ($isEnterprise) {
				// 	wp_set_object_terms($post['ID'], TermTaxonomies::ENTERPRISE_TERM_ID, TermTaxonomies::TAXONOMY_ENTERPRISE);
				// } else {
				// 	wp_delete_object_term_relationships($post, TermTaxonomies::TAXONOMY_ENTERPRISE);
				// }
			}
		}
		Logger::getInstance()->add("Imóvel " . $code . " " . $action . " com sucesso!");
	}

	private function cleanDatabaseAndMediaContent($code)
	{
		$post = $this->getPostByMetaCode($code);
		Logger::getInstance()->add("pegou post: " . $post['ID']);
		//remove post media
		$medias = explode(",", get_post_meta($post['ID'], 'galeria-de-imagens', true)); //get_attached_media( '', $post->ID );
		foreach ($medias as $media) {
			Logger::getInstance()->add("removendo image ID: " . $media);
			wp_delete_attachment($media, true);
		}
		//remove post
		wp_delete_post($post['ID']);
		Logger::getInstance()->add("Imóvel " . $code . " removido com sucesso!");
	}

	private function mapColumns($data): array
	{
		$fields = [];
		$regions = [];
		$isEnterprise = false;
		foreach ($data as $key => $value) {
			$idx = $this->fieldsMap()[$key];
			if (!empty($idx)) {
				if ("FotoDestaque" === $key) {
					$fields[$idx] = $this->getImageId($value);
				} else if (in_array($key, CRMFields::BOOLEAND_FIELDS)) {
					if ($key === "Lancamento") {
						Logger::getInstance()->add("Lançamento: " . $value);
						$isEnterprise = $value === "Sim";
						Logger::getInstance()->add("Empreendimento: " . $isEnterprise);
					}

					$fields[$idx] = $value === "Sim" ? "true" : "false";
				} else if (in_array($key, ["UF", "Cidade", "Bairro"])) {
					$fields[$idx] = $value;
					$regions[$key] = $value;
				} else if ("Elemento" === $key) {
					$fields[$idx] = $this->elementArray($value);
				} else if ("Status" === $key) {
					$fields[$idx] = $value;
					$fields['finalidade'] = CRMFields::mapFinalidades($value);
				} else if (is_array($value)) {
					$fields[$idx] = implode(",", $value);
				} else if (is_object($value)) {
					if ("Caracteristicas" === $key) {
						$features = [];
						foreach ($value as $j => $z) {
							if (!is_object($z)) {
								if ("Sim" === trim($z)) {
									$features[] = $j;
								}
							}
						}
						$fields[$idx] = $features;
					}
					if ("Infraestrutura" === $key) {
						$infrastructure = [];
						foreach ($value as $j => $z) {
							if (!is_object($z)) {
								if ("Sim" === trim($z)) {
									$infrastructure[] = $j;
								}
							}
						}
						$fields[$idx] = $infrastructure;
					}
					if ("Foto" === $key) {
						$photos = [];
						foreach ($value as $item) {
							$photos[] = $this->getImageId($item->Foto);
						}
						$fields[$idx] = implode(",", $photos);
					}
					if ("Video" === $key) {
						$fields['video2'] = $value['Video'] ?? "";
					}
				} else {
					$fields[$idx] = $value === "0" ? "" : $value;
				}
			}
		}
		$terms = new TermTaxonomies($regions);

		return [$fields, $terms->getTermsIds(), $isEnterprise];
	}

	private function fieldsMap(): array
	{
		$map = [];
		$options = new OptionManager();
		$fields = $options->getFieldsMapping();
		foreach ($fields as $field) {
			$map[$field['crm']] = $field['jet'];
		}

		return $map;
	}

	private function getImageId($imageUrl)
	{
		$fullName = basename($imageUrl);
		$imageName = explode(".", $fullName)[0];
		$post = $this->getPostIdByNameLike($imageName);
		if (is_null($post)) {
			return media_sideload_image($imageUrl, 0, null, 'id');
		} else {
			return $post['ID'];
		}
	}

	private function getPostIdByNameLike($name)
	{
		global $wpdb;
		$sql = "select ID from " . $wpdb->prefix . "posts where post_name like '%" . $name . "%'";
		$result = $wpdb->get_results($sql, ARRAY_A);
		if (is_null($result)) {
			return null;
		} else {
			return $result[0];
		}
	}

	private function elementArray($val): array
	{
		$elements = [
			"Campo" => "nocampo",
			"Cidade" => "nacidade",
			"Praia" => "napraia",
			"Embarcações" => "Embarcacoes",
			"Ar" => "Ar"
		];

		return [$elements[$val]];
	}

	private function getPostByMetaCode($code)
	{
		global $wpdb;
		$sql = "select post_id as ID from " . $wpdb->prefix . "postmeta where meta_value = '" . $code . "'";
		$result = $wpdb->get_results($sql, ARRAY_A);
		if (is_null($result)) {
			return null;
		} else {
			return $result[0];
		}
	}

	private function checkIfEmbarcacao($values): array
	{
		if ($values['categoria'] === "Embarcação") {
			$title = $values['categoria'] . " em " . $values['cidade'];
			return [$title, self::BOATTYPE];
		} else {
			return [$values['nome'], self::POSTTYPE];
		}
	}


}