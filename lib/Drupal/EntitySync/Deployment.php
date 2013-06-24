<?php
  
namespace Drupal\EntitySync;
  
  
class Deployment extends EntityBase {
  
  function __construct($values = array()) {
    parent::__construct($values, "entity_sync_deployment");
  }
  
}