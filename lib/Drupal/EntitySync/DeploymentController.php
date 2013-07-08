<?php
  
namespace Drupal\EntitySync;
  
  
class DeploymentController extends ControllerBase {
  public function __construct($entityType = "entity_sync_deployment") {
    xdebug_break();
    parent::__construct($entityType);
  }
  
  
}