<?php

namespace Drupal\EntitySync;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Symfony\Component\Yaml\Yaml;

class Remote {
  
  /**
   * Guzzle Client object
   *
   * @var Client $client
   */
  private $client;
  
  /**
   * URL of remote
   *
   * @var string
   */
  private $url;
  
  /**
   * location of REST servers on Remote
   *
   * @var string
   */
  private $services;
  
  /**
   * username for rest auth
   *
   * @var string
   */
  private $username;
  
  /**
   * password for rest auth
   *
   * @var string
   */
  private $password;
  
  /**
   * Keeper of the session cookie
   *
   * @var string
   */
  private $cookiePlugin;
  
  /**
   * constructor
   *
   * @param array $remote 
   * @author stovak
   */
  function __construct($remote) {

    foreach ($remote as $key => $value) {
      $k = str_replace("remote-", "", $key);
      $this->$k = $value;
    }
    $this->doLogin();
  }
  
  /**
   * Do the action of logging in to remote server
   *
   * @return void
   * @author stovak
   */
  private function doLogin() {
    $this->client = new Client($this->url);
    $this->cookiePlugin = new CookiePlugin(new ArrayCookieJar());
    $this->client->addSubscriber($this->cookiePlugin);
    $this->post($this->services."/user/login.json", array("Accept" => "application/json"), array(
        "username" => $this->username,
        "password" => $this->password
      ), "json"
    );
  }
     
  /**
   * get action to receive response from remote
   *
   * @param string $uri 
   * @param string $format 
   * @return void
   * @author stovak
   */
  function get($uri, $format = "yaml") {
    $request = $this->client->get($uri, array("Accept:" =>"text/{$format},application/{$format}"));
    return $this->formatResponse($request->send(), $format);
  }
  /**
   * post action to receive response from remote
   *
   * @param string $uri 
   * @param array $headers 
   * @param array $values 
   * @param string $format 
   * @return void
   * @author stovak
   */
  function post($uri, $headers = array(), $values = array(), $format = "yaml") {
    $headers['Accept'] = "text/{$format},application/{$format}";
    $request = $this->client->post( $uri , $headers , $values );
    return $this->formatResponse($request->send(), $format);
  }
  
  /**
   * format the response from remote
   *
   * @param Response $response 
   * @param string $format 
   * @return void
   * @author stovak
   */
  private function formatResponse(\Guzzle\Http\Message\MessageInterface $response, $format) { 
    $message = array();
    if (in_array($response->getStatusCode(), array(200, 201, 202))) {
      $message = $response->getBody();
    }
      switch ($format) {
      
        case "response":
          return $response;
          break;
    
        case "json":
          return json_decode($message, true);
          break;
    
        default: 
          return Yaml::parse($message, 5);
      }
    
  }
  
}