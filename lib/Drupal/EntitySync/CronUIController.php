<?php

namespace Drupal\EntitySync;


class CronUIController extends UIBase {

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

    $form .= $this->_getSubmitElements($form, $form_state);
    return $form;
  }

}