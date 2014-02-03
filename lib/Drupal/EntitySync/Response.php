<?php

namespace Drupal\EntitySync;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Symfony\Component\Yaml\Yaml;


class Response {
  
  public static function emit($values) {
    $accept = explode(",", @array_shift(explode(";", $_SERVER['HTTP_ACCEPT'])));
    switch(true) {

      case in_array("application/yaml", $accept):
      case in_array("text/yaml", $accept):
        header("Content-Type: text/yaml");
        echo Yaml::dump($values, 5);
        exit();
        break;
      
      case in_array("application/json", $accept):
      case in_array("text/json", $accept):
        header("Content-Type: text/json");
        echo json_encode($values, true);
        exit();
        break;
      
      default:
        return $values;
      
    }
    
    
  }
  
}
