<?php

namespace G28\VistasoftMonitor\Core;

use G28\VistasoftMonitor\VistaSoft\PropertiesManager;

class OptionManager
{
    const OPTIONS_NAME      = 'g28-vistasoft-monitor_options';
	const OPTIONS_SUMMARY   = 'g28-vistasoft-monitor-summary';
	const OPTIONS_CRON      = 'g28-vistasoft-monitor-cron-next-page';

	protected static ?OptionManager $_instance = null;

	private static $options;
	private static $summary;

	private static $cronOptions;

	private function __construct()
	{
		self::normalizeOptions();
		self::$options       = get_option(self::OPTIONS_NAME);
		self::$cronOptions   = get_option(self::OPTIONS_CRON);
		self::$summary		 = get_option(self::OPTIONS_SUMMARY);
	}

	public static function getInstance(): ?OptionManager {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private static function normalizeOptions()
	{
		if( is_bool( get_option(self::OPTIONS_NAME) ) ) {
			update_option( self::OPTIONS_NAME, [
				'enable'		=> false,
				'fields'		=> self::fieldsOptions(),
				'post_type'		=> PropertiesManager::POSTTYPE,
				'features'		=> self::featuresOptions(),
			 ] );
		}
		if( is_bool( get_option( self::OPTIONS_CRON ) ) ) {
			update_option(self::OPTIONS_CRON, [
				'next'          => 1,
				'total'         => 1
			]);
		}
		if( is_bool( get_option(self::OPTIONS_SUMMARY ) ) ) {
			update_option(self::OPTIONS_SUMMARY, self::resumeObject() );
		}
	}

	private static function initialize()
	{
		update_option( self::OPTIONS_NAME, [
			'enable'		=> false,
			'fields'		=> ( new OptionManager )->fieldsOptions(),
			'post_type'		=> PropertiesManager::POSTTYPE,
			'features'		=> ( new OptionManager )->featuresOptions(),
		]);
		update_option(self::OPTIONS_CRON, [
			'next'          => 1,
			'total'         => 1
		]);
		update_option(self::OPTIONS_SUMMARY, self::resumeObject() );
	}

	public function getFieldsMapping(): array
	{
		return self::fieldsOptions();
	}

	public function getNextPage()
	{
		return self::$cronOptions['next'];
	}

	public function updateCronOptions( $total )
	{
		if( self::$cronOptions['next'] >= $total ) {
			self::$cronOptions['next'] = 1;
			update_option(self::OPTIONS_SUMMARY, self::resumeObject() );

		} else {
			self::$cronOptions['next'] = self::$cronOptions['next'] + 1;
		}
		self::$cronOptions['total'] = $total;
		update_option(self::OPTIONS_CRON, self::$cronOptions);
	}

	public function toggleEnable()
	{
		if( isset( self::$options['enable'] )) {
			self::$options['enable'] = !self::$options['enable'];
		} else {
			self::$options['enable'] = false;
			CronEvent::getInstance()->deactivate();
		}
		update_option( self::OPTIONS_NAME, self::$options );
	}

	public function getEnable(): bool
	{
		if( !isset( self::$options['enable'] ) ) {
			self::initialize();
			CronEvent::getInstance()->deactivate();
			return false;
		} else {
			return self::$options['enable'];
		}
	}

	private static function resumeObject(): array
	{
		return [
			'excluidos'		=> [ 'valor' => 0, 'codigos' => [] ],
			'cadastrados' 	=> [ 'valor' => 0, 'codigos' => [] ],
			'atualizados'	=> [ 'valor' => 0, 'codigos' => [] ],
		];
	}

	public function setExcluded( $code )
	{
		self::$summary['excluidos']['valor'] = self::$summary['excluidos']['valor'] + 1;
		self::$summary['excluidos']['codigos'][] = $code;
		update_option( self::OPTIONS_SUMMARY, self::$summary );
	}

	public function setAdded( $code )
	{
		self::$summary['cadastrados']['valor'] = self::$summary['cadastrados']['valor'] + 1;
		self::$summary['cadastrados']['codigos'][] = $code;
		update_option( self::OPTIONS_SUMMARY, self::$options );
	}

	public function setUpdated( $code )
	{
		self::$summary['atualizados']['valor'] = self::$summary['atualizados']['valor'] + 1;
		self::$summary['atualizados']['codigos'][] = $code;
		update_option( self::OPTIONS_SUMMARY, self::$summary );
	}

	public function getSummary(): string
	{
		$html = "<div class='summary-container'>";
		$html .= "<div class='summary-item'>";
		$html .= "<span class='summary-title'>Imóveis excluídos: </span>";
		$html .= "<span class='summary-value'>" . self::$summary['excluidos']['valor'] . "</span>";
		$html .= "</div>";
		$html .= "<div class='summary-item'>";
		$html .= "<span class='summary-title'>Imóveis cadastrados: </span>";
		$html .= "<span class='summary-value'>" . self::$summary['cadastrados']['valor'] . "</span>";
		$html .= "</div>";
		$html .= "<div class='summary-item'>";
		$html .= "<span class='summary-title'>Imóveis atualizados: </span>";
		$html .= "<span class='summary-value'>" . self::$summary['atualizados']['valor'] . "</span>";
		$html .= "</div>";
		$html .= "</div>";
		return $html;
	}

	private static function fieldsOptions(): array
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

	private static function featuresOptions(): array{
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
