<?php

namespace Drupal\geostore_busquedas;


//0,0008984725965858041° EQUIVALE A 100 MTS
class BusquedasOfertasCatalog{

	public function getOfertasCercanas($latitud,$longitud,$range){

		$query = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'oferta');
		$nids = $query->execute();

		$ofertas = \Drupal\node\Entity\Node::loadMultiple($nids);

		$ofertas_filtered = $this->filterOfertasByRange($ofertas,$latitud,$longitud,$range);

		return $ofertas_filtered;
	}

	public function filterOfertasByRange($ofertas,$latitud,$longitud,$range){
		$ofertas_filtered = array();
		foreach ($ofertas as $nid => $oferta) {
			$negocio = node_load($oferta->field_negocio->getString());
			$negocio_latitud  = floatval($negocio->field_latitud->getString());
			$negocio_longitud = floatval($negocio->field_longitud->getString());
			if( $negocio_latitud <= ($latitud + $range) &&
		 		$negocio_latitud >= ($latitud - $range) &&
		 		$negocio_longitud <= ($longitud + $range) &&
		 		$negocio_longitud >= ($longitud - $range) ){
				$ofertas_filtered[$nid] = $oferta;
			}
		}
		return $ofertas_filtered;
	}

}