<?php

namespace G28\VistasoftMonitor\Core;

use G28\VistasoftMonitor\VistaSoft\PropertyDAO;

class OptionManager
{
    const OPTIONS_NAME      = 'g28-integrajetvsoft_options';
	const OPTIONS_CRON      = 'g28-integrajetvsoft_g28-cron-next-page';

	private $options;
	private $cronOptions;

	public function __construct()
	{
		$this->normalizeOptions();
		$this->options          = get_option(self::OPTIONS_NAME);
		$this->cronOptions      = get_option(self::OPTIONS_CRON);
	}

	private function normalizeOptions()
	{
		if( is_bool( get_option(self::OPTIONS_NAME) ) ) {
			update_option( self::OPTIONS_NAME, [
				'fields'		=> $this->fieldsOptions(),
				'post_type'		=> PropertyDAO::POSTTYPE,
				'features'		=> $this->featuresOptions(),
			 ] );
		}
		if( is_bool( get_option( self::OPTIONS_CRON ) ) ) {
			update_option(self::OPTIONS_CRON, [
				'next'          => 1,
				'total'         => 1
			]);
		}
	}

	public static function initialize()
	{
		update_option( self::OPTIONS_NAME, [
			'fields'		=> ( new OptionManager )->fieldsOptions(),
			'post_type'		=> PropertyDAO::POSTTYPE,
			'features'		=> ( new OptionManager )->featuresOptions(),
		]);
		update_option(self::OPTIONS_CRON, [
			'next'          => 1,
			'total'         => 1
		]);
	}

	public function saveFieldsMapping( $fields )
    {
        $this->options['fields'] = $fields;
        update_option(self::OPTIONS_NAME, $this->options);
    }

	public function saveFeaturesMapping( $fields )
    {
        $this->options['features'] = $fields;
        update_option(self::OPTIONS_NAME, $this->options);
    }

	public function getFieldsMapping(): array
	{
		//return $this->options['fields'];
		return $this->fieldsOptions();
	}

	public function getFeaturesMapping()
	{
		return $this->options['features'];
	}

	public function getCrmFields(): array
	{
		$crmFields = [];
		$fields = $this->options['fields'];
		foreach($fields as $field)
		{
			$crmFields[] = $field['crm'];
		}
		return $crmFields;
	}

	public function getNextPage()
	{
		return $this->cronOptions['next'];
	}

	public function updateCronOptions( $total )
	{
		if( $this->cronOptions['next'] >= $total ) {
			$this->cronOptions['next'] = 1;
		} else {
			$this->cronOptions['next'] = $this->cronOptions['next'] + 1;
		}
		$this->cronOptions['total'] = $total;
		update_option(self::OPTIONS_CRON, $this->cronOptions);
	}

	private function fieldsOptions(): array
	{
		return [
			[ "crm" => "TituloSite", "jet" => "nome", "required" => "true" ],
			[ "crm" => "DescricaoWeb", "jet" => "descricao-do-imovel", "required" => "true" ],
			[ "crm" => "Codigo", "jet" => "codigo", "required" => "true" ],
			[ "crm" => "ValorVenda", "jet" => "valor-da-venda", "required" => "true" ],
			[ "crm" => "ValorLocacao", "jet" => "valor-da-locacao", "required" => "true" ],
			[ "crm" => "ValorCondominio", "jet" => "valor_condominio", "required" => "true" ],
			[ "crm" => "ValorIptu", "jet" => "valor-iptu", "required" => "true" ],
			[ "crm" => "FotoDestaque", "jet" => "imagem-principal", "required" => "true" ],
			[ "crm" => "Foto", "jet" => "galeria-de-imagens", "required" => "true" ],
			[ "crm" => "CategoriaImovel", "jet" => "categoria", "required" => "true" ],
			[ "crm" => "Lancamento", "jet" => "lancamento", "required" => "true" ],
			[ "crm" => "ExibirNoSite", "jet" => "exibir-no-site", "required" => "true" ],
			[ "crm" => "DestaqueWeb", "jet" => "mostrarnahome", "required" => "true" ],
			[ "crm" => "AreaTotal", "jet" => "areatotal", "required" => "true" ],
			[ "crm" => "AreaPrivativa", "jet" => "area-privativa", "required" => "true" ],
			[ "crm" => "Dormitorios", "jet" => "dormitorios", "required" => "true" ],
			[ "crm" => "Suites", "jet" => "suites", "required" => "true" ],
			[ "crm" => "Vagas", "jet" => "vagas", "required" => "true" ],
			[ "crm" => "Bairro", "jet" => "bairro", "required" => "true" ],
			[ "crm" => "Cidade", "jet" => "cidade", "required" => "true" ],
			[ "crm" => "UF", "jet" => "uf", "required" => "true" ],
			[ "crm" => "Elemento", "jet" => "elemento", "required" => "true" ],
			[ "crm" => "Status", "jet" => "status", "required" => "true" ],
			[ "crm" => "ValorDiaria", "jet" => "valor-diaria", "required" => "true" ],
			[ "crm" => "Caracteristicas", "jet" => "caracteristicas", "required" => "true" ],
			[ "crm" => "InfraEstrutura", "jet" => "infraestrutura", "required" => "true" ],
			[ "crm" => "Empreendimento", "jet" => "empreendimento", "required" => "true" ],
			[ "crm" => "Latitude", "jet" => "latitude", "required" => "true" ],
			[ "crm" => "Longitude", "jet" => "longitude", "required" => "true" ],
			[ "crm" => "Categoria", "jet" => "tipoimovel", "required" => "true" ],
			[ "crm" => "QuantidadeMotor", "jet" => "quantidademotor", "required" => "true" ],
			[ "crm" => "Motor", "jet" => "motor", "required" => "true" ],
			[ "crm" => "ModeloMotor", "jet" => "modelomotor", "required" => "true" ],
			[ "crm" => "Hp", "jet" => "hp", "required" => "true" ],
			[ "crm" => "Combustivel", "jet" => "combustivel", "required" => "true" ],
			[ "crm" => "Pes", "jet" => "pes", "required" => "true" ],
			[ "crm" => "URLVideo", "jet" => "video", "required" => "true" ],
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
