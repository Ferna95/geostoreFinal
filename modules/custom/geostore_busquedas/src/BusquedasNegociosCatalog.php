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


	//SEARCH NEGOCIOS BY STIRNG TITLE
	public function getNegociosByTitle($string){

		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'negocio')
		    ->condition('title', '%' . $string . '%' , 'LIKE');
		$nids = $query->execute();

		$negocios = \Drupal\node\Entity\Node::loadMultiple($nids);
		
		return $negocios;
	}

	public function getNegociosCercanosByProductoNid($pnid,$latitud,$longitud,$range){
		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'negocio');
		$nids = $query->execute();

		//TODOS LOS NEGOCIOS
		$negocios = \Drupal\node\Entity\Node::loadMultiple($nids);

		$negocios_filtered = $this->filterNegociosByRange($negocios,$latitud,$longitud,$range);

		//ARRAY DONDE GUARDO LOS NEGOCIOS QUE TIENEN EL PRODUCTO Y ESTAN EN EL RANGO
		$negocios_producto = array();
		foreach ($negocios_filtered as $nid => $negocio) {
			$productos_de_negocio = $negocio->field_productos_del_negocio->getValue();
			//RECORRO LOS PARAGRAPH DE CADA NEGOCIO PARA VER CUAL TIENE EL PRODUCTO
			foreach ( $productos_de_negocio as $element ) {
			  $p = \Drupal\paragraphs\Entity\Paragraph::load( $element['target_id'] );
			  $producto_id = $p->field_producto->getString();
			  if($producto_id == $pnid){
			  	$negocios_producto[] = $negocio;
			  }
			}
		}

		//RETORNO NEGOCIOS QUE TIENEN EL PRODUCTO Y ESTÁN EN EL RANGO
		return $negocios_producto;
	}

	public function filterNegociosByRange($negocios,$latitud,$longitud,$range){
		$negocios_filtered = array();
		foreach ($negocios as $nid => $negocio) {
			$negocio_latitud  = floatval($negocio->field_latitud->getString());
			$negocio_longitud = floatval($negocio->field_longitud->getString());
			if( $negocio_latitud <= ($latitud + $range) &&
		 		$negocio_latitud >= ($latitud - $range) &&
		 		$negocio_longitud <= ($longitud + $range) &&
		 		$negocio_longitud >= ($longitud - $range) ){
				$negocios_filtered[$nid] = $negocio;
			}
		}
		return $negocios_filtered;
	}

}