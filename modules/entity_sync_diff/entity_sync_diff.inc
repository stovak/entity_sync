<?php


function entity_sync_diff_get_options($remote_entity_type_id, $remote_entity_uuid) {
  module_load_include("inc", "diff", "diff.diff");
  module_load_include("module", "entity_sync");
  $icon_classes = array("glyphicon");
  $controller = entity_get_controller($remote_entity_type_id);
  if (!$controller instanceOf \DrupalEntityControllerInterface) {
    $icon_classes[] = "glyphicon-ban-circle";
    $base_element = "div";
    $message = "Entity doesn't exist locally";
    $button_classes = array("alert","alert-danger");
  } else {
    $base_element = "button type='button'";
    $local_entity = @array_shift($controller->load(array(), array("uuid" => $remote_entity_uuid)));
    if (is_object($local_entity) && (!empty($local_entity))) {
      $remote = _entity_sync_get_remote();
      $remote_entity = (object)$remote->get("entity/sync/yaml/view/{$remote_entity_type_id}/{$remote_entity_uuid}", "yaml");
      $diff = diff_entity_diff($local_entity, $remote_entity, array("entity_type" => $remote_entity_type_id));
      if (count($diff)){
        $icon_classes[] = "glyphicon-ok";
        $message = "In Sync";
        $base_element = "div";
        $button_classes = array();
      } else {
        $icon_classes[] = "glyphicon-cloud-download";
        $message = "Sync with Local";
        $button_classes = array("btn","btn-default","btn-sm");
      }
    } else {
      $icon_classes[] = "glyphicon-plus-sign";
      $message = "Add to Local";
      $button_classes = array("btn","btn-default","btn-sm");
    }
  }
  echo theme("entity_sync_diff_get_options", array("icon_classes" => $icon_classes, "button_classes" => $button_classes, "message" => $message, "base_element" => $base_element));
  exit();
}