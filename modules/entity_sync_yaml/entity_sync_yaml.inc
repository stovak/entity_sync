<?php


function entity_sync_yaml_list($type, $bundle = "all", $format="YAML") {
  xdebug_break();
  switch($type) {
    case "entities":
      $response = entity_get_info();
      break;
    
    
    default:
      $query = new EntityFieldQuery();
      $query->entityCondition("entity_type", $type);
      if (!empty($bundle) && $bundle != "all") {
        $query->propertyCondition("bundle", $bundle);
      }
      $results = $query->execute();
      $response = (array_key_exists($results, $type)?$results[$type]:array());
  }
  
  switch ($format) {
    
    case "json":
      echo json_encode($response, true);
      break;
    
    default: 
      echo Symfony\Component\Yaml\Yaml::dump($response, 5);
  }
  exit();
}