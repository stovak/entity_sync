<?php

namespace Drupal\EntitySync;

use Drupal\EntitySync\Export;

  
class DeploymentUIController extends UIBase {
  
  protected function _get_operation_form($form, &$form_state, $entity, $op) {
    
    if (!($entity  instanceof Deployment)) {
      $entity = new Deployment();
    }
    $form['#entity'] = $entity;
    
    $form['title'] = array(
      "#type" => "text",
      "#default_value" => $entity->title
    );
    
    field_attach_form("deployment", $entity, $form, $form_state);
    $form .= $this->_getSubmitElements($form, $form_state);
    return $form;
  }
  
}