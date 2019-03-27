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
  	$ofertas = \Drupal::service('geostore_busquedas.ofertas_catalog')->getOfertasCercanas($latitud,$longitud,$range);
 	//$result = array();
 	//foreach ($negocios as $key => $negocio) {
 	//	$result[] = array(
 	//		'nid' => $negocio->id(),
 	//		'title' => $negocio->getTitle(),
 	//		'field_image' => file_create_url($negocio->field_image->entity->getFileUri()),
 	//		'field_direccion' => $negocio->field_direccion->getString(),
 	//	);
 	//}
 	//return new JsonResponse($result);
 }


}