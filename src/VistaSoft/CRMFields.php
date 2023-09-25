<?php

namespace G28\VistasoftMonitor\VistaSoft;

class CRMFields
{
	const BASIC_FIELDS = [
		"TituloSite",
		"DescricaoWeb",
		"Codigo",
		"ValorVenda",
		"ValorLocacao",
		"ValorCondominio",
		"ValorIptu",
		"FotoDestaque",
		"CategoriaImovel",
		"AreaTotal",
		"AreaPrivativa",
		"Dormitorios",
		"Suites",
		"Vagas",
		"Bairro",
		"Cidade",
		"UF",
		"URLVideo",
		"Elemento",
		"Status",
		"ValorDiaria",
		"Empreendimento",
		"Latitude",
		"Longitude",
		"Categoria",
		"QuantidadeMotor",
		"Motor",
		"ModeloMotor",
		"Hp",
		"Combustivel",
		"Pes",
	];
	const BOOLEAND_FIELDS = ["Lancamento", "ExibirNoSite", "DestaqueWeb",];
	const ENTERPRISE_FIELDS = ["APartirDe", "SobConsulta", "AreaUtil", "AreaUtilAte", "Vagas", "VagasAte", "Dormitorios", "DormitoriosAte"];
	const SUBCATEGORY_FIELDS = ["Caracteristicas", "InfraEstrutura", [ "Foto" => ["Foto"] ], [ "Video" => ["Video"] ]];

	public static function getSearchArgs()
	{
		return json_encode( [
			'fields' => array_merge(self::BASIC_FIELDS, self::SUBCATEGORY_FIELDS, self::BOOLEAND_FIELDS, self::ENTERPRISE_FIELDS)
		] );
	}

	public static function getQueryArgs( $page )
	{
		return json_encode( [
			"fields"    => ["ExibirNoSite"],
			"filter"    => [],
			"paginacao" => [ "pagina" => $page, "quantidade" => 50 ]
		] );
	}

	public static function mapFinalidades( $status ): array
	{
		$purposes = [];
		if( !empty( $status ) ) {
			$words    = explode( " ", $status );
			foreach ( $words as $word ) {
				$word = trim( $word, "," );
				switch ( $word ) {
					case "Venda":
						$purposes[] = "comprar";
						break;
					case "Aluguel":
						$purposes[] = "alugar";
						break;
					case "Temporada":
						$purposes[] = "temporada";
						break;
					default:
						break;
				}
			}
		}
		return $purposes;
	}

}
