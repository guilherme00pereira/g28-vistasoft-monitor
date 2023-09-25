<?php

namespace G28\VistasoftMonitor\Core;

use G28\VistasoftMonitor\VistaSoft\PropertiesManager;

class OptionManager
{
    const OPTIONS_NAME      = 'g28-vistasoft-monitor_options';
	const OPTIONS_PROCESS      = 'g28-vistasoft-monitor-process-next-page';

	private $options;
	private $processOptions;

	public function __construct()
	{
		$this->normalizeOptions();
		$this->options        = get_option(self::OPTIONS_NAME);
		$this->processOptions = get_option(self::OPTIONS_PROCESS);
	}

	private function normalizeOptions()
	{
		if( is_bool( get_option(self::OPTIONS_NAME) ) ) {
			update_option( self::OPTIONS_NAME, [
				'auto'			=> false,
				'fields'		=> $this->fieldsOptions(),
				'post_type'		=> PropertiesManager::POSTTYPE,
				'features'		=> $this->featuresOptions(),
			 ] );
		}
		if( is_bool( get_option( self::OPTIONS_PROCESS ) ) ) {
			update_option(self::OPTIONS_PROCESS, [
				'next'          => 1,
				'total'         => 1
			]);
		}
	}

	public static function initialize()
	{
		update_option( self::OPTIONS_NAME, [
			'auto'			=> false,
			'fields'		=> ( new OptionManager )->fieldsOptions(),
			'post_type'		=> PropertiesManager::POSTTYPE,
			'features'		=> ( new OptionManager )->featuresOptions(),
		]);
		update_option(self::OPTIONS_PROCESS, [
			'next'          => 1,
			'total'         => 1
		]);
	}

	public function getFieldsMapping(): array
	{
		return $this->fieldsOptions();
	}

	public function getNextPage()
	{
		return $this->processOptions['next'];
	}

	public function toggleAuto()
	{
		if( isset( $this->options['auto'] )) {
			$this->options['auto'] = ! $this->options['auto'];
		} else {
			$this->options['auto'] = false;
		}
		update_option( self::OPTIONS_NAME, $this->options );
	}

	public function getAuto(): bool
	{
		if( !isset( $this->options['auto'] ) ) {
			self::initialize();
		}
		return $this->options['auto'];
	}

	private function fieldsOptions(): array
	{
		return [
			[ "crm" => "TituloSite", "jet" => "nome" ],
			[ "crm" => "DescricaoWeb", "jet" => "descricao-do-imovel" ],
			[ "crm" => "Codigo", "jet" => "codigo" ],
			[ "crm" => "ValorVenda", "jet" => "valor-da-venda" ],
			[ "crm" => "ValorLocacao", "jet" => "valor-da-locacao" ],
			[ "crm" => "ValorCondominio", "jet" => "valor_condominio" ],
			[ "crm" => "ValorIptu", "jet" => "valor-iptu" ],
			[ "crm" => "FotoDestaque", "jet" => "imagem-principal" ],
			[ "crm" => "Foto", "jet" => "galeria-de-imagens" ],
			[ "crm" => "CategoriaImovel", "jet" => "categoria" ],
			[ "crm" => "Lancamento", "jet" => "lancamento" ],
			[ "crm" => "ExibirNoSite", "jet" => "exibir-no-site" ],
			[ "crm" => "DestaqueWeb", "jet" => "mostrarnahome" ],
			[ "crm" => "AreaTotal", "jet" => "areatotal" ],
			[ "crm" => "AreaPrivativa", "jet" => "area-privativa" ],
			[ "crm" => "Dormitorios", "jet" => "dormitorios" ],
			[ "crm" => "Suites", "jet" => "suites" ],
			[ "crm" => "Vagas", "jet" => "vagas" ],
			[ "crm" => "Bairro", "jet" => "bairro" ],
			[ "crm" => "Cidade", "jet" => "cidade" ],
			[ "crm" => "UF", "jet" => "uf" ],
			[ "crm" => "Elemento", "jet" => "elemento" ],
			[ "crm" => "Status", "jet" => "status" ],
			[ "crm" => "ValorDiaria", "jet" => "valor-diaria" ],
			[ "crm" => "Caracteristicas", "jet" => "caracteristicas" ],
			[ "crm" => "InfraEstrutura", "jet" => "infraestrutura" ],
			[ "crm" => "Empreendimento", "jet" => "empreendimento" ],
			[ "crm" => "Latitude", "jet" => "latitude" ],
			[ "crm" => "Longitude", "jet" => "longitude" ],
			[ "crm" => "Categoria", "jet" => "tipoimovel" ],
			[ "crm" => "QuantidadeMotor", "jet" => "quantidademotor" ],
			[ "crm" => "Motor", "jet" => "motor" ],
			[ "crm" => "ModeloMotor", "jet" => "modelomotor" ],
			[ "crm" => "Hp", "jet" => "hp" ],
			[ "crm" => "Combustivel", "jet" => "combustivel" ],
			[ "crm" => "Pes", "jet" => "pes" ],
			[ "crm" => "URLVideo", "jet" => "video" ],
			[ "crm" => "APartirDe", "jet" => "apartirde", ],
			[ "crm" => "SobConsulta", "jet" => "sobconsulta", ],
			[ "crm" => "AreaUtil", "jet" => "areautil", ],
			[ "crm" => "AreaUtilAte", "jet" => "areautilate", ],
			[ "crm" => "Vagas", "jet" => "vagas1", ],
			[ "crm" => "VagasAte", "jet" => "vagasate", ],
			[ "crm" => "Dormitorios", "jet" => "dormitorios1", ],
			[ "crm" => "DormitoriosAte", "jet" => "dormitoriosate", ],
		];
	}

	private function featuresOptions(): array{
		return [
			[ "crm" => "Agua Quente", "jet" => "Agua Quente" ],
			[ "crm" => "Ar Condicionado", "jet" => "Ar Condicionado" ],
			[ "crm" => "Banheiro Social", "jet" => "Banheiro Social" ],
			[ "crm" => "Churrasqueira", "jet" => "Churrasqueira" ],
			[ "crm" => "Cozinha Planejada", "jet" => "Cozinha Planejada" ],
			[ "crm" => "Despensa", "jet" => "Despensa" ],
			[ "crm" => "Dormitorio Com Armario", "jet" => "Dormitorio Com Armario" ],
			[ "crm" => "Hidromassagem", "jet" => "Hidromassagem" ],
			[ "crm" => "Lavabo", "jet" => "Lavabo" ],
			[ "crm" => "Mobiliado", "jet" => "Mobiliado" ],
			[ "crm" => "Piscina", "jet" => "Piscina" ],
			[ "crm" => "Sacada", "jet" => "Sacada" ],
			[ "crm" => "Sala Estar", "jet" => "Sala Estar" ],
			[ "crm" => "Sala Jantar", "jet" => "Sala Jantar" ],
			[ "crm" => "Split", "jet" => "Split" ],
			[ "crm" => "Vista Mar", "jet" => "Vista Mar" ],
			[ "crm" => "WCEmpregada", "jet" => "WCEmpregada" ],
			[ "crm" => "Area Servico", "jet" => "Area Servico" ],
			[ "crm" => "Armario Embutido", "jet" => "Armario Embutido" ],
			[ "crm" => "Copa", "jet" => "Copa" ],
			[ "crm" => "Copa Cozinha", "jet" => "Copa Cozinha" ],
			[ "crm" => "Cozinha", "jet" => "Cozinha" ],
			[ "crm" => "Dpendenciade Empregada", "jet" => "Dpendenciade Empregada" ],
			[ "crm" => "Jardim Inverno", "jet" => "Jardim Inverno" ],
			[ "crm" => "Living Hall", "jet" => "Living Hall" ],
			[ "crm" => "Sala Armarios", "jet" => "Sala Armarios" ],
			[ "crm" => "Sala TV", "jet" => "Sala TV" ],
			[ "crm" => "Suite Master", "jet" => "Suite Master" ],
			[ "crm" => "Terraco", "jet" => "Terraco" ],
			[ "crm" => "Vista Panoramica", "jet" => "Vista Panoramica" ],
			[ "crm" => "Banheiro Auxiliar", "jet" => "Banheiro Auxiliar" ],
			[ "crm" => "Estar Intimo", "jet" => "Estar Intimo" ],
			[ "crm" => "Piso Elevado", "jet" => "Piso Elevado" ],
			[ "crm" => "TVCabo", "jet" => "TVCabo" ],
		];
	}

}
