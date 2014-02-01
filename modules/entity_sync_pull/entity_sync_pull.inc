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
  $entities = $remote->get("entity/sync/json/list/entities/all", "json");
  return theme("entity_sync_pull_entity_list", array("entities" => $entities));
}

function entity_sync_pull_bundle($entity_type_id, $bundle_type_id = null) {
  $remote = _entity_sync_get_remote();
  $entities = $remote->get("entity/sync/yaml/list/{$entity_type_id}/$bundle_type_id", "yaml");
  print_r($entities);
  exit();

  
}
