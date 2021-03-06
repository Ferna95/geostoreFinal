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

 
class BusquedaNegociosController extends ControllerBase {


 public function getNegociosByTitle(){
 	$keys = $_GET['keys'];
 	$latitud = $_GET['latitud'];
 	$longitud = $_GET['longitud'];
 	$range = $_GET['range'];
 	$degrees_range = geostore_busquedas_convert_meters_to_degrees($range);
  	$negocios = \Drupal::service('geostore_busquedas.negocios_catalog')->getNegociosByTitle($keys,$degrees_range,$latitud,$longitud);
 	$result = array();
 	foreach ($negocios as $key => $negocio) {
 		$result[] = array(
 			'nid' => $negocio->id(),
 			'title' => $negocio->getTitle(),
 			'field_image' => file_create_url($negocio->field_image->entity->getFileUri()),
 			'field_direccion' => $negocio->field_direccion->getString(),
 		);
 	}
 	return new JsonResponse($result);
 }

}