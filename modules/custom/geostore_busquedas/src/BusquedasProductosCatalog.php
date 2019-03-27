<?php

namespace Drupal\geostore_busquedas;


//0.0008984725965858041° EQUIVALE A 100 MTS
class BusquedasProductosCatalog{

	public function getBusquedasCercanas($latitud,$longitud,$range,$time){

		$busquedas = \Drupal::database()->select('geostore_busquedas_productos', 'gbp')
			->fields('gbp')
			->execute()
			->fetchAll();
		return $busquedas;

	}
	
	/* 
	LATITUD: LOCALIZACION DEL NEGOCIO
	LONGITUD: LOCALIZACION DEL NEGOCIO
	RANGE: CANTIDAD DE ELEMENTOS QUE DEVUELVE
	TIME: TIEMPO DESDE DONDE EMPIEZA A FILTRAR
		25200: SEGUNDOS DE UNA SEMANA
		756000: SEGUNDOS EN UN MES (30 DIAS)
		9198000: SEGUNDOS EN UN AÑO (365 DIAS)
	*/
	public function getMasBuscados($latitud,$longitud,$range,$time){

		$query = \Drupal::database()->select('geostore_busquedas_productos', 'gbp');
		$query->join('node','n','gbp.nid = n.nid');
		$query->join('node_field_data','nfd','n.nid = nfd.nid');
		$query->addField('gbp', 'nid');
		$query->addField('nfd', 'title');
		$query->addExpression('COUNT(gbp.nid)', 'count');
		$query->groupBy('gbp.nid');
		$query->groupBy('nfd.title');
		$query->range(0, $range);
		$query->condition('gbp.created',time() - $time,'>=');
		$data = $query->execute();
		$results = $data->fetchAll(\PDO::FETCH_ASSOC);

		return $results;

	}


	public function getBusquedasPorHora($latitud,$longitud,$range,$time){

		$query = \Drupal::database()->select('geostore_busquedas_productos', 'gbp');
		$query->addExpression("DATE_FORMAT(FROM_UNIXTIME(gbp.created), '%H:00')", 'hour');
		$query->addExpression('COUNT(gbp.created)', 'count');
		$query->groupBy('hour');
		$query->condition('gbp.created',time() - $time,'>=');
		$data = $query->execute();
		$results = $data->fetchAll(\PDO::FETCH_ASSOC);
		return $results;

	}

	//SEARCH PRODUCTOS BY STIRNG TITLE
	public function getProductosByTitle($string){

		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'producto')
		    ->condition('title', '%' . $string . '%' , 'LIKE');
		$nids = $query->execute();

		$productos = \Drupal\node\Entity\Node::loadMultiple($nids);
		
		return $productos;
	}

}