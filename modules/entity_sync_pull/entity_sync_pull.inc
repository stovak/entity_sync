<?php

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Symfony\Component\Yaml\Yaml;

function entity_sync_pull_connect($form, &$form_state) {
  //print_r($GLOBALS);
  if (isset($_SESSION['entity_sync_pull_connect'])) {
    $defaults = $_SESSION['entity_sync_pull_connect'];
  } else {
    $defaults = array(
      'remote-url' => 'http://drops7.local',
      'remote-services' => 'services/rest',
      'remote-username' => 'stovak'
    );
  }
  
  $form['header'] = array(
    "#type" => "markup",
    "#markup" => "<h3>Please log in to the remote site to see pull-able entities:</h3>"
  );
  $form['description'] = array(
    "#type" => "markup",
    "#markup" => "<p>entity_sync_yaml must be enabled as well as Services module.</p>"
  );
  $form['remote-url'] = array(
    "#title" => t("Base URL of Remote Site"),
    "#type" => "textfield",
    "#required" => "true",
    '#default_value' => $defaults['remote-url'],
    "#attributes" => array(
      "autocorrect" => "off",
      "autocomplete" => "off",
      'placeholder' => 'http://www.drupal.org'
    )
  );
  
  $form['remote-services'] = array(
    "#title" => t("Location of services rest endpoint"),
    "#type" => "textfield",
    "#required" => "true",
    '#default_value' => $defaults['remote-services'],
    "#attributes" => array(
      "autocorrect" => "off",
      "autocomplete" => "off",
      'placeholder' => 'services/rest'
    )
  );
  
  $form['remote-username'] = array(
    "#title" => t("Remote Site URL"),
    "#type" => "textfield",
    "#required" => "true",
    '#default_value' => $defaults['remote-username'],
    "#attributes" => array(
      "autocorrect" => "off",
      "autocomplete" => "off",
      'placeholder' => 'username'
      
    )
  );
  
  $form['remote-password'] = array(
    "#title" => t("Remote Site password"),
    "#type" => "password",
    "#required" => "true",
    "#attributes" => array(
      "autocorrect" => "off",
      "autocomplete" => "off",
      'placeholder' => 'password'
      
    )
  );
  
  $form['actions'] = array(
    "#type" => "fieldset"
  );
  
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Log In to Remote Site'));
  
  return $form;
}

function entity_sync_pull_connect_validate(&$form, &$form_state ) {
  
  //
  
}

function entity_sync_pull_connect_submit(&$form, &$form_state) {
  unset($GLOBALS['entity_sync_remote']);
  $_SESSION['entity_sync_remote'] =$form_state['values'];
  drupal_goto("entity/sync/pull/entities");
}


function entity_sync_pull_entities() {
  $remote = _entity_sync_get_remote();
  unset($_SESSION['entity_sync_remote_entities']);
  $entities = $remote->get("entity/sync/json/list/entities/all", "json");
  if (!empty($entities)) {
    $_SESSION['entity_sync_remote_entities'] = $entities;
  }
  return theme("entity_sync_pull_entity_list", array("entities" => $entities));
}

function entity_sync_pull_bundle($entity_type_id, $bundle_type_id = null) {
  module_load_include("module", "entity_sync");
  drupal_add_js(drupal_get_path("module", "entity_sync_pull")."/entity_sync_pull.js");
  $remote = _entity_sync_get_remote();
  $entity_info = $_SESSION['entity_sync_remote_entities'][$entity_type_id];
  $response = $remote->get("entity/sync/json/list/{$entity_type_id}/{$bundle_type_id}", "json");
  $header = array();
  foreach($entity_info['entity keys'] as $key => $value) {
    if (strpos($key, "uuid") === false){
      $header[$key] = $value;
    }
  }
  if (!empty($response)) {
    $rows = array();
    foreach($response as $id => $entity) {
      foreach($header as $key => $value) {
        $rows[$id]["data"][$key] = (array_key_exists($value, $entity)?$entity[$value]:"");
      }
      $rows[$id]["data"]['local-status'] = array("data" => "<span></span>", "class" => "entity-sync-remote-diff");
      $rows[$id]['data-uuid'] = (array_key_exists($entity_info['entity keys']['uuid'], $entity)?$entity[$entity_info['entity keys']['uuid']]:"");
      $rows[$id]['data-entity-type-id'] = $entity_type_id;
      $rows[$id]['class'] = array("entity-sync-remote-entity");
    }
  }
  $header[] = "remote";
  return theme("table", array("rows" => $rows, "header" => $header, "attributes" => array("id" => "entity-sync-remote-pull-bundle")));
  //print_r(get_defined_vars());
  //exit();

  
}
