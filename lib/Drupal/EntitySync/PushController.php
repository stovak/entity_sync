<?php
  
namespace Drupal\EntitySync;
  
  
class PushController extends ControllerBase {
  
  public function __construct($entityType = null) {
    xdebug_break();
    parent::__construct("entity_sync_push");
  }
  
}