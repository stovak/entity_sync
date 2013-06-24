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
      "#label" => t("Title"),
      "#description" => "Enter a title for your deployment",
      "#type" => "textfield",
      "#default_value" => $entity->title
    );
    
    field_attach_form("entity_sync_deployment", $entity, $form, $form_state);
    $this->_getSubmitElements($form, $form_state);
    xdebug_break();
    
    return $form;
  }
  
}