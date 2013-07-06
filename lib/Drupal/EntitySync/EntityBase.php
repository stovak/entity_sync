<?php

namespace Drupal\EntitySync;
  
  
class EntityBase extends \Entity {
  
  
  function __construct($values = array()) {
    parent::__construct($values, $this->_entityType);
    $this->field_list = reset(field_info_instances($this->_entityType));
    foreach ($this->field_list as $field_name => $field_info) {
      $this->$field_name = field_get_items($this->_entityType, $this, $field_name);
    }
  }
  
  
}





