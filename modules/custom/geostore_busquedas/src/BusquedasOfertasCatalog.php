<?php

namespace Drupal\geostore_busquedas;


//0.0008984725965858041° EQUIVALE A 100 MTS
class BusquedasOfertasCatalog{

	public function getOfertasCercanas($latitud,$longitud,$range){

		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'oferta');
		$nids = $query->execute();

		$ofertas = \Drupal\node\Entity\Node::loadMultiple($nids);

		var_dump($ofertas);exit;
	}

}