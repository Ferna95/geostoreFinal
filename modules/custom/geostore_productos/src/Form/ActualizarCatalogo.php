<?php

namespace Drupal\geostore_productos\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use \Drupal\file\Entity\File;
use \Drupal\image\Entity\ImageStyle;

class ActualizarCatalogo extends FormBase {

  public function getFormId() {
    return 'ActualizarCatalogo';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = array(
      '#markup' => t("A través de este formulario puede actualizar el catálogo de productos del sitio"),
    );
   
    $form['csv'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Nuevo catálogo'),
      '#upload_location' => 'public://catalogos',
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

      foreach ($data->sheets[0]['cells'] as $rowNumber => $rowData) {
        if($rowNumber != 1){ //QUITO LOS HEADERS
          $product = \Drupal::service('geostore_busquedas.productos_catalog')->getProductoByCode($rowData[1]);
          
          if($product){ //VALIDO SI EXISTE PREVIAMENTE SINO LO CREO
            try {
              $product->set('title', $rowData[2]);
              $product->set('body', $rowData[3]);
              $product->set('field_categoria', Term::load($rowData[4]));
              $product->set('field_proveedor', Term::load($rowData[5]));
              $product->save();
            }
            catch (\Exception $e) {
              watchdog_exception('myerrorid', $e);
            }
          }
          else{
            try {
              $product = Node::create([
                'type'                 => 'producto',
                'field_codigo_interno' => $rowData[1],
                'title'                => $rowData[2],
                'body'                 => $rowData[3],
                'field_categoria'      => Term::load($rowData[4]),
                'field_proveedor'      => Term::load($rowData[5]),
              ]);
              $product->save();
            }
            catch (\Exception $e) {
              watchdog_exception('myerrorid', $e);
            }
          }

        }
      }
      drupal_set_message("El catálogo fue actualizado.");
  }

}