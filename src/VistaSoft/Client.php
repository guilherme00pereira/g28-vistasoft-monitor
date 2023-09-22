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

	private array $codesFromDb;

    public function __construct()
    {
        $this->requestArgs      = [ 'headers' => ['Accept' => 'application/json'] ];
        $this->codesQueue       = [];
    }

    private function makeRequest( $endpoint, $showtotal = false, $page = 1)
    {
        $queryArgs  = CRMFields::getQueryArgs( $page );
	    $url        = self::CRM_URL . $endpoint . "?key=" . self::CRM_KEY . "&pesquisa=" . $queryArgs;
        if( $showtotal ) {
            $url   .= "&showtotal=1";
        }
        $response   = wp_remote_get( $url, $this->requestArgs );
        if( is_wp_error( $response ) ) {
	        Logger::getInstance()->add("Erro ao importar imóveis da api" . $response->get_error_message());
        }
        return json_decode( wp_remote_retrieve_body( $response ) );
    }

    public function processProperty( $code, $action )
    {
        Logger::getInstance()->add("Buscando dados do imóvel: " . $code);
        $manager = new PropertiesManager();
		if( $action === PropertiesManager::REMOVE)
		{
			$manager->remove( $code );
		}
		else
		{
			$data = $this->getRealStateData( $code );
			if( empty( $data ) ) {
				Logger::getInstance()->add("Dados do imóvel: " . $code . " não retornados pelo CRM");
				$manager->remove( $code );
			} else {
				$manager->addOrUpdate( $data );
			}
        }
    }

    public function listRealStates()
    {
		Logger::getInstance()->clear();
	    $items = $this->requestPage(1);
	    $this->walkThrough( $items );
	    for( $page = 2; $page <= $items->paginas; $page++ ) {
		    $items = $this->requestPage( $page );
		    $this->walkThrough( $items );
	    }
	    $manager = new PropertiesManager();
	    $manager->run( $this->codesQueue );
    }

	/**
	 * @return mixed
	 */
	public function requestPage($page) {
		Logger::getInstance()->add( "Listando imóveis do CRM - página: " . $page );
		return $this->makeRequest( self::CRM_LIST_ENDPOINT, true, $page );
	}

    private function walkThrough( $items )
    {
	    if ( empty( $items ) ) {
		    Logger::getInstance()->add( "Nenhum imóvel retornado" );
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

    private function getRealStateData( $id )
    {
        Logger::getInstance()->add("Importando dados do imóvel código: " . $id);
        $url        = self::CRM_URL . self::CRM_DETAILS_ENDPOINT . "?key=" . self::CRM_KEY . "&imovel=" . $id . "&pesquisa=" . CRMFields::getSearchArgs();
        $response   = wp_remote_get( $url, $this->requestArgs );
        if( is_wp_error( $response ) ) {
            Logger::getInstance()->add("Erro ao importar dados do imóvel: " . $id . " - " . $response->get_error_message());
            return "";
        }
		$content = json_decode( wp_remote_retrieve_body( $response ) );
		if($content->status === 400) {
			Logger::getInstance()->add("Erro ao importar dados do imóvel: " . $id . " - " . $content->message );
			return "";
		}
        return $content;
    }


}
