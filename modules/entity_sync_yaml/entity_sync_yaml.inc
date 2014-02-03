<?php


function entity_sync_yaml_list($type, $bundle = "all", $format="yaml") {
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
    
      $response = entity_load($type, (array_key_exists($type,$results)?array_keys($results[$type]):array()));
  }
  return \Drupal\EntitySync\Response::emit($response);
}