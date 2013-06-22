<?php
  
  
  
namespace Drupal\EntitySync\Export;

use Drupal\EntitySync;

class View extends \Drupal\EntitySync\ExportBase implements \Drupal\EntitySync\ExportInterface {
  
  var $entityType = "view";
  
  function export() {
    $this->entity->init_display();
    $this->entity->init_query();
    $toReturn = array();
    $this->export_row($toReturn);
    $toReturn['api_version'] = views_api_version();
    $toReturn['disabled'] = false;

    foreach ($this->entity->display as $id => $display) {
      $toReturn[$id]['display_plugin'] = $display->display_plugin;
      $toReturn[$id]['display_title'] = $display->display_title;      
      if (empty($display->handler)) {
        // @todo -- probably need a method of exporting broken displays as
        // they may simply be broken because a module is not installed. That
        // does not invalidate the display.
        continue;
      }
      foreach($display->handler->options as $key => $value){
        $toReturn[$id]['options'][$key] = $value;
      }
    }

    // Give the localization system a chance to export translatables to code.
    if ($this->init_localization()) {
      $this->export_locale_strings('export');
      $translatables = $this->localization_plugin->export_render($indent);
      if (!empty($translatables)) {
        $toReturn['translatables'] .= $translatables;
      }
    }

    return $toReturn;
  }
  
  
  function export_row(&$toReturn) {

    if (!$identifier) {
      $identifier = $this->entity->db_table;
    }
    $schema = drupal_get_schema($this->entity->db_table);
    $toReturn['classname'] = get_class($this->entity);
    
      
    // Go through our schema and build correlations.
    foreach ($schema['fields'] as $field => $info) {
      if (!empty($info['no export'])) {
        continue;
      }
      if (!isset($this->$field)) {
        if (isset($info['default'])) {
          $toReturn[$toReturn['classname']][$field] = $info['default'];
        }
        else {
          $toReturn[$toReturn['classname']][$field] = '';
        }

        // serialized defaults must be set as serialized.
        if (isset($info['serialize'])) {
          $toReturn[$toReturn['classname']][$field] = unserialize($this->entity->$field);
        }
      }
      $value = $this->entity->$field;
      if ($info['type'] == 'int') {
        if (isset($info['size']) && $info['size'] == 'tiny') {
          $value = (bool) $value;
        }
        else {
          $value = (int) $value;
        }
      }
      $toReturn[$toReturn['classname']][$field] = $value;      
    }
    return $toReturn;
  }
  
  public function findByUUID($uuid) {
    xdebug_break();
    return views_get_view($uuid);
  }
  
}