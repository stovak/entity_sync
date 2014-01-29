<?php



function entity_sync_pull_connect(&$form, $form_state) {
  print_r(func_get_args());
  exit();
  $form['header'] = array(
    "#type" => "markup",
    "#markup" => "<h3>Please log in to the remote site to see pull-able entities:</h3>"
  );
  
  $form['url'] = array(
    "#title" => t("Remote Site URL"),
    "#type" => "textfield",
    "#required" => "true"
  );
  
  $form['username'] = array(
    "#title" => t("Remote Site URL"),
    "#type" => "textfield",
    "#required" => "true"
  );
  
  $form['password'] = array(
    "#title" => t("Remote Site password"),
    "#type" => "password",
    "#required" => "true"
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
  
  
  
}