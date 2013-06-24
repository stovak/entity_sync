<?php

namespace Drupal\EntitySync;
  
  
class UIBase extends \EntityDefaultUIController {
  
  
  public function hook_menu() {
    
    $items = array();
    $id_count = count(explode('/', $this->path));
    $wildcard = isset($this->entityInfo['admin ui']['menu wildcard']) ? $this->entityInfo['admin ui']['menu wildcard'] : '%entity_object';
    $plural_label = isset($this->entityInfo['plural label']) ? $this->entityInfo['plural label'] : $this->entityInfo['label'] . 's';

    $items[$this->entityInfo['admin ui']['overview form uri']] = array(
      'title' => $plural_label,
      'page callback' => 'drupal_get_form',
      'page arguments' => array($this->entityType . '_overview_form', $this->entityType),
      'description' => 'Manage ' . $plural_label . '.',
      'access callback' => 'entity_access',
      'access arguments' => array('view', $this->entityType),
      'file' => 'includes/entity.ui.inc',
    );
    $items[$this->entityInfo['admin ui']['overview form uri'] . '/list'] = array(
      'title' => 'List',
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => -10,
    );
    $items[$this->entityInfo['admin ui']['overview form uri'] . '/add'] = array(
      'title callback' => 'entity_ui_get_action_title',
      'title arguments' => array('add', $this->entityType),
      'page callback' => 'entity_ui_get_form',
      'page arguments' => array($this->entityType, NULL, 'add'),
      'access callback' => 'entity_access',
      'access arguments' => array('create', $this->entityType),
      'type' => MENU_LOCAL_ACTION,
    );
    $items[$this->path ."/". $wildcard] = array(
      'title' => 'View',
      'title callback' => 'entity_label',
      'title arguments' => array($this->entityType, $id_count + 1, "view"),
      'page callback' => 'entity_ui_get_form',
      'page arguments' => array($this->entityType, $id_count + 1, "view"),
      'load arguments' => array($this->entityType),
      'access callback' => 'entity_access',
      'access arguments' => array('update', $this->entityType, $id_count + 1),
    );
    $items[$this->path . "/" . $wildcard . '/view'] = array(
      'title' => 'View',
      'load arguments' => array($this->entityType),
      'type' => MENU_DEFAULT_LOCAL_TASK,
    );
    
    $items[$this->path ."/". $wildcard . "/%"] = array(
      'title' => 'Edit',
      'title callback' => 'entity_label',
      'title arguments' => array($this->entityType, $id_count + 1, $id_count + 2),
      'page callback' => 'entity_ui_get_form',
      'page arguments' => array($this->entityType, $id_count + 1, $id_count + 2),
      'load arguments' => array($this->entityType),
      'access callback' => 'entity_access',
      'access arguments' => array('update', $this->entityType, $id_count + 1),
    );
    xdebug_break();
    return $items;
  }
  
  public function operationForm($form, &$form_state, $entity, $op) {
    xdebug_break();
    switch($op) {
      
      case 'delete':
        $label = entity_label($this->entityType, $entity);
        $confirm_question = t('Are you sure you want to delete the %entity %label?', array('%entity' => $this->entityInfo['label'], '%label' => $label));
        return confirm_form($form, $confirm_question, $this->path);
      
      case 'add':
        return $this->_get_operation_form($form, $form_state, $entity, $op);
        break;
      
      default:
        drupal_not_found();
        exit();
      
    }
    
  }
  
  
  protected function _getSubmitElements(&$form, &$form_state){
    
    $form['actions'] = array(
      '#weight' => 100,
    );
    $form['actions']['save'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    $form['actions']['cancel'] = array(
      '#type' => 'link',
      '#title' => t('Cancel'),
      '#href' => $destination,
    );
    
    
  }
}


