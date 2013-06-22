<?php
  
  namespace Drupal\EntitySync;
  
  
  
  interface ExportInterface {
    
    public function export(); 
    
    public function import();
    
    function findByUUID($uuid);
    
    function __toYaml();
    
  }