<?php

namespace Drupal\EntitySync\Export;

use Drupal\EntitySync;

class Entity extends \Drupal\EntitySync\ExportBase implements \Drupal\EntitySync\ExportInterface {
  
  function __construct($uuid) {
    $entityType = check_plain(strtolower(arg(3)));
    $entityInfo = entity_get_info();
    xdebug_break();
    if (array_key_exists($entityType, $entityInfo)) {
      $this->entityType = $entityType;
      $this->entityInfo = entity_get_info($this->entityType);
      $this->entity = $this->findByUUID($uuid);
    }
    else return false;
  }
  
}