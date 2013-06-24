<?php
  
namespace Drupal\EntitySync;
  
  
class DeploymentController extends ControllerBase {
  public function __construct($entityType = null) {
    xdebug_break();
    parent::__construct("entity_sync_deployment");
  }
  
  
}