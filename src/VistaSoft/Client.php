<?php

namespace G28\VistasoftMonitor\VistaSoft;

use G28\VistasoftMonitor\Core\Logger;
use G28\VistasoftMonitor\Core\OptionManager;

class Client
{
    const CRM_KEY               = "bb4f8f411b1497f17dbfd6eac10f776c";// "8c9b11b4da74d8f96f032ba117219fce";
    const CRM_URL               = "http://cli7490-rest.vistahost.com.br/";
    const CRM_LIST_ENDPOINT     = "imoveis/listar";
    const CRM_DETAILS_ENDPOINT  = "imoveis/detalhes";
	const CRM_AVAILABLE_FIELDS  = "imoveis/listarcampos";
    private array $requestArgs;
    private array $codesQueue;

	private Logger $logger;


    public function __construct($logType)
    {
        $this->requestArgs      = [ 'headers' => ['Accept' => 'application/json'] ];
        $this->codesQueue       = [];
	    $this->logger           = new Logger( $logType );
    }

    private function listRequest( $showtotal = false, $page = 1)
    {
        $queryArgs  = CRMFields::getQueryArgs( $page );
	    $url        = self::CRM_URL . self::CRM_LIST_ENDPOINT . "?key=" . self::CRM_KEY . "&pesquisa=" . $queryArgs;
        if( $showtotal ) {
            $url   .= "&showtotal=1";
        }
        $response   = wp_remote_get( $url, $this->requestArgs );
        if( is_wp_error( $response ) ) {
	        $this->logger->add( "Erro ao importar imóveis da api" . $response->get_error_message());
        }
        return json_decode( wp_remote_retrieve_body( $response ) );
    }

    private function singleRequest( $code )
    {
        $url        = self::CRM_URL . self::CRM_DETAILS_ENDPOINT . "?key=" . self::CRM_KEY . "&imovel=" . $code . "&pesquisa=" . CRMFields::getSearchArgs();
        $response   = wp_remote_get( $url, $this->requestArgs );
        if( is_wp_error( $response ) ) {
            $this->logger->add( "Erro ao importar dados do imóvel: " . $code . " - " . $response->get_error_message());
            return "";
        }
        return json_decode( wp_remote_retrieve_body( $response ) );
    }

    public function listRealStates()
    {
        $page       = OptionManager::getInstance()->getNextPage();
        $this->logger->add( "Listando imóveis do CRM - página: " . $page );
	    $items = $this->listRequest( true, $page );
        if( empty( $items ) ) {
            $this->logger->add("Nenhum imóvel retornado");
        } else {
	        OptionManager::getInstance()->updateCronOptions( $items->paginas );
            $this->walkThrough( $items );
            $manager = new PropertiesManager( $this );
            $manager->run();
        }
    }

    public function getSingleRealState( $code )
    {
        $this->logger->add( "Buscando dados do imóvel: " . $code );
	    $item = $this->singleRequest( $code );
        if( empty( $item ) ) {
            $this->logger->add("Nenhum imóvel retornado");
        } else {
            if ( is_object( $item ) ) {
                $this->codesQueue[] = [
                    "codigo" => $item->Codigo,
                    "exibir" => $item->ExibirNoSite
                ];
            }
            $manager = new PropertiesManager($this);
            $manager->run();
        }
    }

    private function walkThrough( $items )
    {
	    if ( empty( $items ) ) {
		    $this->logger->add( "Nenhum imóvel retornado" );
	    } else {
		    foreach ( $items as $item ) {
			    if ( is_object( $item ) ) {
				    $this->codesQueue[] = [
					    "codigo" => $item->Codigo,
					    "exibir" => $item->ExibirNoSite
				    ];
			    }
		    }
	    }
    }

    public function getRealStateData( $id )
    {
        $this->logger->add( "Importando dados do imóvel código: " . $id);
        $url        = self::CRM_URL . self::CRM_DETAILS_ENDPOINT . "?key=" . self::CRM_KEY . "&imovel=" . $id . "&pesquisa=" . CRMFields::getSearchArgs();
        $response   = wp_remote_get( $url, $this->requestArgs );
        if( is_wp_error( $response ) ) {
            $this->logger->add( "Erro ao importar dados do imóvel: " . $id . " - " . $response->get_error_message());
            return "";
        }
		$content = json_decode( wp_remote_retrieve_body( $response ) );
        return $content;
    }

	public function getCodesQueue(): array {
		return $this->codesQueue;
	}

	public function getLogger(): Logger {
		return $this->logger;
	}
}
