<?php

namespace Drupal\geostore_productos\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\paragraphs\Entity\Paragraph;
use \Drupal\file\Entity\File;
use \Drupal\image\Entity\ImageStyle;

class ActualizarProductosNegocio extends FormBase {

  public function getFormId() {
    return 'ActualizarProductosNegocio';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = array(
      '#markup' => t("A través de este formulario puede actualizar los productos de un negocio"),
    );
   
    $form['negocio'] = array(
      '#type' => "entity_autocomplete",
      '#target_type' => 'node',
      '#required' => TRUE,
      '#selection_settings' => ['target_bundles' => ["negocio"]],
    );
    
    $form['csv'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Productos de negocio'),
      '#upload_location' => 'public://productosNegocios',
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['xls'],
      ],
    );

    $form['action'] = array(
      '#type' => 'submit',
      '#value' => t('Actualizar catálogo'),
    );
    return $form;
  }



  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do here.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

      require_once 'modules/custom/geostore_productos/libraries/excelReader/Excel/reader.php';
      $file_input = $form_state->getValue("csv")[0];
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($file_input);
      $absolute_path = \Drupal::service('file_system')->realpath($file->getFileUri());

      $data = new \Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->setUTFEncoder('mb');
      $data->read($absolute_path);

      $negocio = Node::load($form_state->getValue("negocio"));
      $negocio->field_productos_del_negocio = array();

      foreach ($data->sheets[0]['cells'] as $rowNumber => $rowData) {
        if($rowNumber != 1 && $rowData[4]){ //QUITO LOS HEADERS Y VALIDO QUE EL PROD DEBE ESTAR EN EL LISTADO
          $product = \Drupal::service('geostore_busquedas.productos_catalog')->getProductoByCode($rowData[1]);
          
          try {
            $paragraph = Paragraph::create([
              'type'                 => 'producto_de_negocio',
              'field_precio_visible' => $rowData[5],
              'field_precio'         => $rowData[6],
              'field_publicado'      => $rowData[7],
              'field_producto'       => $product,
            ]);

            $negocio->field_productos_del_negocio[] = $paragraph;
            $negocio->save();
          }
          catch (\Exception $e) {
            watchdog_exception('myerrorid', $e);
          }

        }
      }
      drupal_set_message("Los productos del negocio fueron actualizados.");
  }

}