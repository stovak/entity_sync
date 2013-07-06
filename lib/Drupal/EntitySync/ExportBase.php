<?php
  
  
  namespace Drupal\EntitySync;
  
  use \Symfony\Component\Yaml\Yaml;
  
  class ExportBase {
    
    var $entityType;
    var $entity;
    var $entityInfo;
    
    
    function __construct($uuid) {
      $aClass = explode("\\", strtolower(get_class($this)));
      $entityType = end($aClass);
      $entityInfo = entity_get_info();
      xdebug_break();
      if (array_key_exists($entityType, $entityInfo)) {
        $this->entityType = $entityType;
        $this->entityInfo = entity_get_info($this->entityType);
        $this->entity = $this->findByUUID($uuid);
      }
      else return false;
    }
    
    public function __toYaml() {
      echo Yaml::dump($this->export(), 2); 
    }
    
    
    public function export() {
      return (array)$this->entity; 
    }
  
    public function import() {
      return entity_save($this->entityType, (object)$this->entity);
    }
    
    public function applyOperation($op){
      
      switch ($op) {
        
        case "view":
          header("Content-Type: text/yaml");
          echo $this->__toYaml();
          break;
          
        default:
          drupal_not_found();
          exit();
      }
      
      
    }
    
    public function findByUUID($uuid) {
      $sql = "select {$this->entityInfo['entity keys']['id']} as id from {$this->entityInfo['base table']} where uuid=:uuid";
      xdebug_break();
      $q = db_query($sql,  array( ":uuid" => $uuid))
          ->fetchObject();
      xdebug_break();
      if (is_object($q) && !empty($q)) {
        $entity = entity_load($this->entityType, array($q->id));
        xdebug_break();
        return reset($entity);
      } else {
        return array();
      }
    }
    
  }