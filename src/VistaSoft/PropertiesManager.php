<?php

namespace G28\VistasoftMonitor\VistaSoft;

use Exception;
use G28\VistasoftMonitor\Core\Logger;
use G28\VistasoftMonitor\Core\OptionManager;
use G28\VistasoftMonitor\Core\TermTaxonomies;

class PropertiesManager
{

	const POSTTYPE = 'imovel';
	const BOATTYPE = 'embarcacao';
	private array $dbKeys;
	private OptionManager $options;
	private Client $vistasoft;
	private Logger $logger;

	public function __construct(Client $client)
	{
		$this->dbKeys = $this->getIdsFromDB();
		$this->options = new OptionManager();
		$this->vistasoft = $client;
		$this->logger = $client->getLogger();
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

	public function run()
	{
		$this->doImports();
		foreach ($this->vistasoft->getCodesQueue() as $code) {
			sleep(2);
			$exibir 	= $code['exibir'];
			$codigo 	= $code['codigo'];
			try {
				if ( $exibir === "Sim") {
					$data = $this->vistasoft->getRealStateData($codigo);
					[$meta_values, $terms] = $this->mapColumns($data);
					if (in_array($codigo, $this->dbKeys)) {
						$this->logger->add( "Atualizando dados do imóvel: " . $codigo);

					} else {
						$this->logger->add( "Cadastrando o imóvel: " . $codigo);
					}
					$this->updateDatabaseContent($meta_values, $codigo, $terms);
					$key = array_search($codigo, $this->dbKeys);
					if ($key !== false) {
						unset($this->dbKeys[$key]);
					}
				} else {
					$this->logger->add( "Imóvel: " . $codigo . " não deve ser exibido no site");
					$this->remove($codigo);
					$this->options->setExcluded($codigo);
				}
			} catch (Exception $e) {
				$this->logger->add( "Erro ao processar o imóvel de código " . $codigo . ": " . $e->getMessage());
			}
		}
		
		$this->logger->add("Importação dos imóveis finalizada!");
	}

	public function remove($code)
	{
		try {
			$this->logger->add( "Removendo o imóvel: " . $code);
			$this->cleanDatabaseAndMediaContent($code);
		} catch (Exception $e) {
			$this->logger->add( "Erro ao remover o imóvel no BD: " . $e->getMessage());
		}
	}

	private function updateDatabaseContent($values, $code, $terms = [])
	{
		$post = $this->getPostByMetaCode($code);
		list($title, $type) = $this->checkIfEmbarcacao($values);
		$action = is_null($post) ? "cadastrado" : "atualizado";
		if (is_null($post)) {
			$this->logger->add( "Cadastrando post " . $post['ID'] . " - " . $post['title'] . " metadata.");
			$post = wp_insert_post([
				'post_title' => $title,
				'post_content' => empty($values['descricao-do-imovel']) ? "" : $values['descricao-do-imovel'],
				'post_status' => 'publish',
				'post_type' => $type
			]);
			$this->options->setAdded($code);
			foreach ($values as $key => $value) {
				add_post_meta($post, $key, $value);
				if (count($terms) > 0) {
					wp_set_object_terms($post, $terms, TermTaxonomies::TAXONOMY_STATE_CITY);
				}
			}
		} else {
			$this->logger->add( "Atualizando post " . $post['ID'] . " - " . $post['title'] . " metadata.");
			wp_update_post([
				'ID' => $post['ID'],
				'post_title' => $title,
				'post_content' => empty($values['descricao-do-imovel']) ? "" : $values['descricao-do-imovel'],
				'post_status' => 'publish',
				'post_type' => $type
			]);
			$this->options->setUpdated($code);
			foreach ($values as $key => $value) {
				update_post_meta($post['ID'], $key, $value);
				if (count($terms) > 0) {
					wp_delete_object_term_relationships($post, TermTaxonomies::TAXONOMY_STATE_CITY);
					wp_set_object_terms($post['ID'], $terms, TermTaxonomies::TAXONOMY_STATE_CITY);
				}
			}
		}
		$this->logger->add( "Imóvel " . $code . " - " . $post['title'] . " " . $action . " com sucesso!");
	}

	private function cleanDatabaseAndMediaContent($code)
	{
		$post = $this->getPostByMetaCode($code);
		$this->logger->add( "pegou post: " . $post['ID']);
		//remove post media
		$medias = explode(",", get_post_meta($post['ID'], 'galeria-de-imagens', true)); //get_attached_media( '', $post->ID );
		foreach ($medias as $media) {
			$this->logger->add( "removendo image ID: " . $media);
			wp_delete_attachment($media, true);
		}
		//remove post
		wp_delete_post($post['ID']);
		$this->logger->add( "Imóvel " . $code . " removido com sucesso!");
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
						$this->logger->add( "Lançamento: " . $value);
						$isEnterprise = $value === "Sim";
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
		$sql = "select ID, post_title as title from " . $wpdb->prefix . "posts 
				where ID = (select post_id from " . $wpdb->prefix . "postmeta where meta_value = '" . $code . "')";
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