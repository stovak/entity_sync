<?php
  
namespace Drupal\EntitySync;

  
class Push extends EntityBase {
  
  public $pid = null;
  public $vid = null;
  public $title = "";
  public $status = 1;
  public $created = null;
  public $changed = null;
  public $language = LANGUAGE_NONE;
  public $bundle = "entity_sync_push_type";
  
  protected $_entityType = "entity_sync_push";
  
}