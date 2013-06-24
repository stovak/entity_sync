<?php
  
namespace Drupal\EntitySync;
  
  
class PushUIController extends UIBase {
  
  
  protected function _get_operation_form($form, &$form_state, $entity, $op) {
    xdebug_break();
    if (!($entity  instanceof Deployment)) {
      $entity = new Push();
    }
    $form['#entity'] = $entity;
    
    $form['title'] = array(
      "#label" => t("Title"),
      "#type" => "text",
      "#default_value" => $entity->title
    );
    
    $form['remote_uri'] = array(
      "#label" => t("Remote URI"),
      "#type" => "text",
      "#default_value" => $entity->title
    );
    $form['remote_username'] = array(
      "#label" => t("Remote Username"),
      "#type" => "text",
      "#default_value" => $entity->title
    );
    $form['remote_password'] = array(
      "#label" => t("Remote Password"),
      "#type" => "text",
      "#default_value" => $entity->title
    );
    
    $form .= $this->_getSubmitElements($form, $form_state);
    return $form;
  }
  
}