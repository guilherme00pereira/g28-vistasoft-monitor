<?php

namespace G28\VistasoftMonitor\Core;

use G28\VistasoftMonitor\Api\PropertyDAO;

class TermTaxonomies {

	private string $state;
	private string $city;
	private string $neighbour;

	const TAXONOMY_STATE_CITY   = 'estado-cidade';
	const TAXONOMY_ENTERPRISE   = 'home';
	const ENTERPRISE_TERM_ID    = 6590;

	public function __construct( $regions )
	{
		$this->state        = strtoupper( $regions['UF'] );
		$this->city         = strtoupper( $regions['Cidade'] );
		$this->neighbour    = strtoupper( $regions['Bairro'] );
	}

	public function getTermsIds(): array
	{
		$ids = [];
		if( !is_null( $this->state )) {
			register_taxonomy( self::TAXONOMY_STATE_CITY, [ PropertyDAO::POSTTYPE, PropertyDAO::BOATTYPE]);
			$stateId   = $this->setTermData( $this->state );
			$ids[] = $stateId;
			$cityId    = $this->setTermData( $this->city, intval( $stateId ) );
			$ids[] = $cityId;
			$neighbour = $this->setTermData( $this->neighbour, intval( $cityId ) );
			$ids[] = $neighbour;
		}
		return $ids;
	}

	private function setTermData( $value, $parent = 0 )
	{
		if( !empty( $value ) ) {
			$idtax = $this->getTermTaxonomyId( $value );
			if ( is_null( $idtax ) ) {
				if ( $parent > 0 ) {
					$ids = wp_insert_term( $value, self::TAXONOMY_STATE_CITY, [ "parent" => $parent ] );
				} else {
					$ids = wp_insert_term( $value, self::TAXONOMY_STATE_CITY );
				}
				return $ids['term_taxonomy_id'];
			} else {
				return (int)$idtax;
			}
		}
	}

	private function getTermTaxonomyId( $value )
	{
		global $wpdb;
		$sql = "SELECT tx.term_taxonomy_id FROM " . $wpdb->prefix . "term_taxonomy tx
				INNER join " . $wpdb->prefix . "terms te on te.term_id = tx.term_id
				where tx.taxonomy = '" . self::TAXONOMY_STATE_CITY . "' and te.name = '" . $value . "'";
		$result = $wpdb->get_results( $sql, ARRAY_A );
		return $result[0]['term_taxonomy_id'];
	}
}
