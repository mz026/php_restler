<?php
/**
 *  restler.php
 *  curl wrapper.
 *  Author: Yang-Hsing Lin
 *
 *  usage:
 *  $result = Restler::request(array(
 *    'url' => <string>
 *    , 'method' => 'GET' (optional, default to 'GET')
 *    , 'headers' => array('key' => 'val') (optional)
 *    , 'body' => <string> | <array>
 *  ));
 *
 *  $result is array of the format:
 *  array(
 *    'status' => <Number>
 *    , 'response' => <Response>
 *  );
 */
require_once "Response.php";

class Restler {
  public static function request($options = NULL)
  {
    $method = self::get_request_method($options);
    $url = self::get_url_and_throw_if_needed($options);
    $headers = self::make_headers_array_from_options($options);
    $body = self::get_request_body($options);
    
    $handle = new Curl_Handle(array(
      'method' => $method
      , 'url' => $url
      , 'headers' => $headers
      , 'body' => $body
    ));

    $handle -> setup();
    $raw_response = $handle -> execute();
    $response = new Response($raw_response);
    $handle -> close();

    return array(
      'status' => $handle -> get_status()
      , 'response' => $response
    );
  }

  private static function get_request_method($options) 
  {
    if ( isset($options['method']) && is_string($options['method'])
      && (strtolower($options['method']) === 'post')) {
      $method = 'POST';
    } else {
      $method = 'GET';
    }
    return $method;
  }
  private static function get_url_and_throw_if_needed ($options) 
  {
    if ( is_array($options) && is_string($options['url'])) {
      return $options['url'];
    } else {
      throw new NULL_URL_EXCEPTION('url must be provided');
    }
  }

  private static function make_headers_array_from_options ($options)
  {
    $generated_headers_arr = array();
    
    if ( is_array($options) 
      && isset($options['headers'])
      && is_array($options['headers']) ) {
      $headers = $options['headers'];

      foreach($headers as $key => $val) {
        if( ! is_string($key) || ! is_string($val)) {
          throw new INVALID_ARGUMENT_EXCEPTION('key and value must be string.');
        }
        $generated_headers_arr[] = "$key:$val";
      };
    }

    return $generated_headers_arr;
  }

  private static function get_request_body($options) 
  {
    $body = '';
    if(isset($options['body'])) {
      $body = $options['body'];
    }
    return $body;
  }
}

class Curl_Handle {

  function __construct ($options)
  {
    $this -> ch = curl_init();
    $this -> method = $options['method'];  
    $this -> url = $options['url'];
    $this -> headers = $options['headers'];
    $this -> body = $options['body'];
  }
  function setup()
  {
    curl_setopt_array($this -> ch, array(
      CURLOPT_URL => $this -> url
      , CURLOPT_HEADER => TRUE
      , CURLOPT_CUSTOMREQUEST => $this -> method  
      , CURLOPT_HTTPHEADER => $this -> headers
      , CURLOPT_RETURNTRANSFER => TRUE
      , CURLOPT_POSTFIELDS => $this -> body
    ));
    return $this;
  }
  function execute()
  {
    $this -> result = curl_exec($this -> ch);
    $this -> status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    return $this -> result;
  }

  function is_success()
  {
    return $this->get_status() < 400;
  }

  function get_status()
  {
    return $this -> status; 
  }

  function get_handler()
  {
    return $this -> ch;
  }

  function close()
  {
    curl_close($this -> ch);
  }

  public $status;
  private $ch;
}

class RESTLER_EXCEPTION extends Exception {}
class NULL_URL_EXCEPTION extends RESTLER_EXCEPTION {}
class INVALID_ARGUMENT_EXCEPTION extends RESTLER_EXCEPTION {}
?>
