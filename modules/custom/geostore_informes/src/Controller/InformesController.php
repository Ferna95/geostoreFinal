<?php
/**
 * @file
 * Contains \Drupal\geostore_informes\Controller\FirstController.
 */
 
namespace Drupal\geostore_informes\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

 
class InformesController extends ControllerBase {


 public function getInformes($node){

 	$busquedas_cercanas = \Drupal::service('geostore_busquedas.productos_catalog')->getBusquedasCercanas(1,1,3,1);
  $mas_buscados = \Drupal::service('geostore_busquedas.productos_catalog')->getMasBuscados(1,1,1,604800);
  $busquedas_por_hora = \Drupal::service('geostore_busquedas.productos_catalog')->getBusquedasPorHora(1,2,1,604800);
  $busquedas_negocio = \Drupal::service('geostore_busquedas.negocios_catalog')->getBusquedas($node->id(),604800);
  
 	return [
      '#theme' => 'geostore_informes',
      '#node' => $node,
      '#attached' => array(
      	'drupalSettings' => array(
          'nid' => $node->id(),
      		'company_location' => array(
  				'longitud' => floatval($node->field_longitud->getString()),
      			'latitud' => floatval($node->field_latitud->getString()),
      		),
      		'busquedas_cercanas' => $busquedas_cercanas,
          'mas_buscados' => array(
            'nids' => array_column($mas_buscados, 'title'),
            'count' => array_column($mas_buscados, 'count'),
          ),
          'busquedas_por_hora' => array(
            'hours' => array_column($busquedas_por_hora, 'hour'),
            'count' => array_column($busquedas_por_hora, 'count'),
          ),
          'busquedas_negocio' => array(
            'days' => array_column($busquedas_negocio, 'day'),
            'count' => array_column($busquedas_negocio, 'count'),
          ),
      	)
      ),
    ];
	$rendered = \Drupal::service('renderer')->render($renderable);
 }

  public function getMasBuscados(){

    $cantidadProductos = $_POST["cantidadProductos"];
    $periodo = $_POST["periodo"];
    $mas_buscados = \Drupal::service('geostore_busquedas.productos_catalog')->getMasBuscados(1,1,$cantidadProductos,$periodo);

    //ESTRUCTURA LA RESPUESTA DEL AJAX
    $mas_buscados_render = array(
      'mas_buscados' => array(
        'nids' => array_column($mas_buscados, 'title'),
        'count' => array_column($mas_buscados, 'count'),
      ),
    );

    return new JsonResponse($mas_buscados_render);
    $rendered = \Drupal::service('renderer')->render($renderable);

  }

  public function getBusquedasPorHora(){

    $periodoHora = $_POST["periodoHora"];
    $periodos = \Drupal::service('geostore_busquedas.productos_catalog')->getBusquedasPorHora(1,1,1,$periodoHora);

    //ESTRUCTURA LA RESPUESTA DEL AJAX
    $periodos_render = array(
      'busquedas_por_hora' => array(
        'hours' => array_column($periodos, 'hour'),
        'count' => array_column($periodos, 'count'),
      ),
    );

    return new JsonResponse($periodos_render);
    $rendered = \Drupal::service('renderer')->render($renderable);
  }

  public function getBusquedasNegocio(){

    $periodoNegocio = $_POST["periodoNegocio"];
    $nid = $_POST["nid"];

    $busquedas_negocio = \Drupal::service('geostore_busquedas.negocios_catalog')->getBusquedas($nid,$periodoNegocio);
    $busquedas_negocio_render = array(
      'busquedas_negocio' => array(
        'days' => array_column($busquedas_negocio, 'day'),
        'count' => array_column($busquedas_negocio, 'count'),
      ),
    );

    return new JsonResponse($busquedas_negocio_render);
    $rendered = \Drupal::service('renderer')->render($renderable);


  }



}