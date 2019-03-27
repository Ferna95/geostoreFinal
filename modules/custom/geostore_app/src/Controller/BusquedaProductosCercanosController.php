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

 
class BusquedaProductosCercanosController extends ControllerBase {

 public function getProductosByTitle(){
  	$productos = \Drupal::service('geostore_busquedas.productos_catalog')->getProductosByTitle($_GET['keys']);
 	$result = array();
 	foreach ($productos as $key => $producto) {
 		$result[] = array(
 			'nid' => $producto->id(),
 			'title' => $producto->getTitle(),
 			'field_image' => file_create_url($producto->field_image->entity->getFileUri()),
 			'field_categoria' => Term::load($producto->field_categoria->getString())->getName(),
 		);
 	}
 	return new JsonResponse($result);
 }

 public function getNegociosCercanosByNid(){
 	$producto_id = $_GET['producto_id'];
 	$latitud = $_GET['latitud'];
 	$longitud = $_GET['longitud'];
 	$range = $_GET['range'];
  	$negocios = \Drupal::service('geostore_busquedas.negocios_catalog')->getNegociosCercanosByProductoNid($producto_id,$latitud,$longitud,$range);
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