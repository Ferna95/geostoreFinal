<?php

namespace Drupal\geostore_busquedas;

class BusquedasNegociosCatalog{

	/*
		NID: ID DEL NEGOCIO
		TIME: PERÍODO DE TIEMPO EN EL QUE BUSCA
	*/
	public function getBusquedas($nid,$time){

		$query = \Drupal::database()->select('geostore_busquedas_negocio', 'gbp');
		$query->addExpression("DATE_FORMAT(FROM_UNIXTIME(gbp.created), '%Y-%m-%d')", 'day');
		$query->addExpression('COUNT(gbp.created)', 'count');
		$query->groupBy('day');
		$query->condition('gbp.nid',$nid);
		$query->orderBy('day','asc');
		$query->condition('gbp.created',time() - $time,'>=');
		$data = $query->execute();
		$results = $data->fetchAll(\PDO::FETCH_ASSOC);
		return $results;

	}

	public function insertBusqueda($nid,$created,$uid){
		db_insert('geostore_busquedas_negocio')
			->fields(
				array(
				'nid' => $nid,
				'created' => $created,
				'uid' => $uid,
				)
			)->execute();
	}

}