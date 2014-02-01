<?php

namespace Drupal\EntitySync;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Symfony\Component\Yaml\Yaml;

class Remote {
  
  private $client;
  private $url;
  private $services;
  private $username;
  private $password;
  private $cookiePlugin;
  
  function __construct($remote) {
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    foreach ($remote as $key => $value) {
      $k = str_replace("remote-", "", $key);
      $this->$k = $value;
    }
    $this->doLogin();
  }
  
  private function doLogin() {
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    $this->client = new Client($this->url);
    $this->cookiePlugin = new CookiePlugin(new ArrayCookieJar());
    $this->client->addSubscriber($this->cookiePlugin);
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    $this->post($this->services."/user/login.json", array("Accept" => "application/json"), array(
        "username" => $this->username,
        "password" => $this->password
      ), "json"
    );
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
  }
  
  private function isLoggedIn(){
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    return true;
  } 
   
  function get($uri, $format = "yaml") {
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    $request = $this->client->get($uri);
    return $this->formatResponse($request->send(), $format);
  }
  
  function post($uri, $headers, $values, $format = "yaml") {
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    $request = $this->client->post( $uri , $headers , $values );
    return $this->formatResponse($request->send(), $format);
  }
  
  private function formatResponse($response, $format) {
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    switch ($format) {
    
      case "json":
        return $response->json();
        break;
    
      default: 
        return Yaml::parse($response->getBody(), 5);
    }
  }
  
}