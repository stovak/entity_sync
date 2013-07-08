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
      "file" => "includes/entity.ui.inc",
      "file path" => drupal_get_path("module","entity"),
    );
    $items[$this->entityInfo['admin ui']['overview form uri'] . '/list'] = array(
      'title' => 'List',
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => -10,
    );
    $items[$this->entityInfo['admin ui']['overview form uri'] . '/add'] = array(
      'title callback' => 'entity_ui_get_action_title',
      'title arguments' => array('add', $this->entityType),
      'page callback' => 'drupal_get_form',
      'page arguments' => array($this->entityType . '_operation_form', $this->entityType, NULL, 'add'),
      'access callback' => 'entity_access',
      'access arguments' => array('create', $this->entityType),
      "file" => "includes/entity.ui.inc",
      "file path" => drupal_get_path("module","entity"),
      'type' => MENU_LOCAL_ACTION,
    );
    $items[$this->path ."/". $wildcard] = array(
      'title' => 'View',
      'title callback' => 'entity_label',
      'title arguments' => array($this->entityType, $id_count + 1, "view"),
      'page callback' => 'entity_ui_get_form',
      'page arguments' => array($this->entityType, $id_count + 1, "view"),
      'load arguments' => array(2),
      'access callback' => 'entity_access',
      'access arguments' => array('update', $this->entityType, $id_count + 1),
      "file" => "includes/entity.ui.inc",
      "file path" => drupal_get_path("module","entity"),
    );
    $items[$this->path . "/" . $wildcard . '/view'] = array(
      'title' => 'View',
      'load arguments' => array(2),
      'type' => MENU_DEFAULT_LOCAL_TASK,
    );
    
    $items[$this->path ."/". $wildcard . "/%"] = array(
      'title' => 'Edit',
      'title callback' => 'entity_label',
      'title arguments' => array($this->entityType, $id_count + 1, $id_count + 2),
      'page callback' => 'drupal_get_form',
      'page arguments' => array($this->entityType . '_operation_form', $this->entityType, 2, 3),
      'load arguments' => array(2),
      'access callback' => 'entity_access',
      'access arguments' => array('update', $this->entityType, 2),
      "file" => "includes/entity.ui.inc",
      "file path" => drupal_get_path("module","entity"),
    );
    xdebug_break();
    return $items;
  }
  
  
  public function overviewTable($conditions = array()) {

    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', $this->entityType);

    // Add all conditions to query.
    foreach ($conditions as $key => $value) {
      $query->propertyCondition($key, $value);
    }

    if ($this->overviewPagerLimit) {
      $query->pager($this->overviewPagerLimit);
    }

    $results = $query->execute();

    $ids = isset($results[$this->entityType]) ? array_keys($results[$this->entityType]) : array();
    $entities = $ids ? entity_load($this->entityType, $ids, array(), true) : array();
    xdebug_break();
    ksort($entities);
    $rows = array();
    foreach ($entities as $entity) {
      $rows[] = $this->overviewTableRow($conditions, entity_id($this->entityType, $entity), $entity);
    }

    $render = array(
      '#theme' => 'table',
      '#header' => $this->overviewTableHeaders($conditions, $rows),
      '#rows' => $rows,
      '#empty' => t('None.'),
    );
    xdebug_break();
    return $render;
  }
  
  protected function overviewTableRow($conditions, $id, $entity, $additional_cols = array()) {
    $entity_uri = entity_uri($this->entityType, $entity);
    $wildcard = isset($this->entityInfo['admin ui']['menu wildcard']) ? $this->entityInfo['admin ui']['menu wildcard'] : '%entity_object';

    $operations = array();
    $row = array();
    $destination = "{$entity->entity_type}/{$entity->entity_id}/keys";
    if (array_key_exists("entity", $additional_cols)) {
      $entity = $additional_cols['entity'];
    } else {
      $entity = entity_load($entity->entity_type, array($entity->entity_id));
    }
    $row["data"]['title'] = check_plain($entity->title);
    $this->_addLocalOperations($entity, $operations);
    $row['data']['status'] = @drupal_render($this->_getStatusMenu($entity));
    foreach (module_implements('_entity_sync_operations') as $module) {
      $function = $module . '_entity_sync_operations';
      $function($operations, $entity, $entity);
    }    
    $row["data"]['operations'] = theme("entity_sync_operations_menu", array("operations" => $operations));
    $row['data-key'] = array($entity->key_id);
    $row['class'] = array(($entity->status)?"active":"revoked");
    return $row;
    
  }
  
  
  public function operationForm($form, &$form_state, $entity, $op) {
    xdebug_break();
    switch(strtolower($op)) {
      case 'delete':
        $label = entity_label($this->entityType, $entity);
        $confirm_question = t('Are you sure you want to delete the %entity %label?', array('%entity' => $this->entityInfo['label'], '%label' => $label));
        return confirm_form($form, $confirm_question, $this->path);
      
      case 'save':
      case 'add':
        xdebug_break();
        return $this->_get_operation_form($form, $form_state, $entity, $op);
        break;
      
      default:
        drupal_not_found();
        exit();
      
    }
    
    
  }
  
  protected function _get_operation_form($form, &$form_state, $entity, $op) {
    $class = "\\".$this->entityInfo['entity class'];
    $label = $this->entityInfo['entity keys']['label'];
    xdebug_break();
    if (!($entity  instanceof $class)) {
      $entity = new $class();
    }
    $form['#entity'] = $entity;
    foreach($this->entityInfo['entity keys'] as $key => $entity_property) {
      if ($key != "label") {
        $form[$entity_property] = array(
          "#type" => "value",
          "#value" => $entity->{$entity_property}
        );
      }
    }
    
    $form[$label]     = array(
      "#label"         => t("Title"),
      "#description"   => "Enter a title for your ".$this->entityInfo['label'],
      "#type"          => "textfield",
      "#default_value" => $entity->$label,
      "#weight"         => -99
    );
    
    
    field_attach_form($this->entityType, $entity, $form, $form_state, LANGUAGE_NONE);
    $this->_getSubmitElements($form, $form_state);    
    xdebug_break();
    return $form;
  }
  
  
  /**
   * Operation form validation callback.
   */
  public function operationFormValidate($form, &$form_state) {
    $field_instances = field_info_instances($this->entityType, $form_state['values']['bundle']);
    foreach ($field_instances as $name => $instance) {
      if (array_key_exists("add_more", $form_state['values'][$name][LANGUAGE_NONE])) {
        unset($form_state['values'][$name][LANGUAGE_NONE]["add_more"]);
      }
    }
    xdebug_break();
  }

  /**
   * Operation form submit callback.
   */
  public function operationFormSubmit($form, &$form_state) {
    $msg = $this->applyOperation($form_state['op'], (object)$form_state["values"]);
    drupal_set_message($msg);
    $form_state['redirect'] = $this->entityInfo['admin ui']['overview form uri'];
  }
  
  public function applyOperation($op, $entity) {
    xdebug_break();
    if (property_exists($entity, $this->entityInfo['entity keys']["id"]) && $entity->{$this->entityInfo['entity keys']["id"]} == 0) {
      unset($entity->{$this->entityInfo['entity keys']["id"]});
      $entity->is_new = 1;
    } else {
      $entity->original = entity_load($this->entityType, array($entity->{$this->entityInfo['entity keys']["id"]}));
    }
    
    if (array_key_exists("bundle", $this->entityInfo['entity keys'])) {
      if (!property_exists($entity, $this->entityInfo['entity keys']['bundle']) && count($this->entityInfo['bundles']) == 1) {
        $entity->bundle = reset(array_keys($this->entityInfo['bundles']));
      } 
    }
    
    
    switch($op) {
      case "add":
      case "edit":
        xdebug_break();
        entity_get_controller($this->entityType)->save($entity);
        return "{$this->entityInfo['label']} Information Saved";
        break;
        
      case "delete":
        if (!property_exists($entity, $this->entityInfo['entity keys']["id"])) {
          drupal_not_found();
          exit();
        } else {
          entity_get_controller($this->entityType)->delete(array($entity->{$this->entityInfo['entity keys']["id"]}));
        }
        return "{$this->entityInfo['label']} Deleted.";
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
      '#href' => "entity_sync/deployments",
    );
    
    xdebug_break();
  }
  
  protected function _addLocalOperations($entity, &$operations) {
    $id = entity_id($this->entityType, $entity);
    $operations['edit'] = l(t('edit'), $this->path ."/". $id . "/edit", array('query' => array("destination" => $this->entityInfo['admin ui']['overview form uri'])) );
    $operations['delete'] =  l(t('delete'), $this->path . '/' . $id . '/delete', array('query' => array("destination" => $this->entityInfo['admin ui']['overview form uri'])) );
  }
  
}


