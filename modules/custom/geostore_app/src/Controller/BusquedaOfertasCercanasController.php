<?php
/**
 * @file
 * Contains \Drupal\geostore_app\Controller\FirstController.
 */
 
namespace Drupal\geostore_app\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\taxonomy\Entity\Term;

 
class BusquedaOfertasCercanasController extends ControllerBase {

 public function getOfertasCercanas(){
 	$latitud = $_GET['latitud'];
 	$longitud = $_GET['longitud'];
 	$range = $_GET['range'];
 	$degrees_range = geostore_busquedas_convert_meters_to_degrees($range);
  	$ofertas = \Drupal::service('geostore_busquedas.ofertas_catalog')->getOfertasCercanas($latitud,$longitud,$degrees_range);
 	$result = array();
 	foreach ($ofertas as $key => $oferta) {
 		$company = node_load($oferta->field_negocio->getString());
 		$result[] = array(
 			'nid' => $oferta->id(),
 			'title' => $oferta->getTitle(),
 			'field_productos_de_oferta' => $oferta->field_productos_de_oferta->getString(),
 			'field_precio' => $oferta->field_precio->getString(),
 			'company_id' => $company->id(),
 			'company_title' => $company->getTitle(),
 			'company_direccion' => $company->field_direccion->getString(),
 			'company_image' => file_create_url($company->field_image->entity->getFileUri()),
 		);
 	}
 	return new JsonResponse($result);
 }


}