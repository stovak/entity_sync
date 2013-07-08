<?php
  
namespace Drupal\EntitySync;
  
  
class PushController extends ControllerBase {
  
  public function __construct($entityType = "entity_sync_push") {
    xdebug_break();
    parent::__construct($entityType);
  }
  
}