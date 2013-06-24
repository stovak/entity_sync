<?php
  
namespace Drupal\EntitySync;
  
  
class Push extends EntityBase {
  function __construct($values = array()) {
    parent::__construct($values, "entity_sync_push");
  }
  
  
}