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
      , 'files' => isset($options['files']) ? $options['files'] : array()
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
    if ( ! self::is_option_method_valid($options) ) {
      return 'GET';
    } 

    switch (strtolower($options['method'])) {
      case 'post':
        $method = 'POST';
        break;
      case 'put':
        $method = 'PUT';
        break;
      default:
        $method = 'GET';
        break;
    }
    return $method;
  }
  private static function is_option_method_valid($options)
  {
    return isset($options['method']) && is_string($options['method']);
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
    $this -> files = $options['files'];
  }
  function setup()
  {
    curl_setopt_array($this -> ch, array(
      CURLOPT_URL => $this -> url
      , CURLOPT_HEADER => TRUE
      , CURLOPT_CUSTOMREQUEST => $this -> method  
      , CURLOPT_HTTPHEADER => $this -> headers
      , CURLOPT_RETURNTRANSFER => TRUE
      , CURLOPT_POSTFIELDS => $this -> get_request_body()
    ));
    return $this;
  }
  private function get_request_body()
  {
    if ( ! empty($this -> files) ) {
      return $this -> get_body_with_file();
    } else if (is_array($this -> body)) {
      return http_build_query($this -> body);
    } else {
      return $this -> body;
    }
  }
  private function get_body_with_file()
  {
    $body = $this -> body;
    foreach ($this -> files as $file_key => $file_path) {
      $to_merge = array();
      $to_merge[$file_key] = "@{$file_path}";
      $body = array_merge($body, $to_merge);
    }
    return $body;
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
