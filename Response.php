<?php

class Response {
  private $raw_response;
  private $body = NULL;
  private $raw_headers_set = NULL;
  private $headers = NULL;

  function __construct($response_str)
  {
    if ( ! is_string($response_str) ) {
      throw new Exception('argument should be a string.');
    } 
    $this -> raw_response = $response_str;
  }

  function get_body()
  {
    if ( $this -> body === NULL ) {
      $this -> parse_response_to_body_and_raw_headers_set();
    }
    return $this -> body;
  }
  private function parse_response_to_body_and_raw_headers_set()
  {
    $spliter = "\r\n\r\n";
    $splited_string_array = explode($spliter, $this -> raw_response);
    $headers_set_array = array();
    $body_pieces_array_with_spliter;
  
    foreach($splited_string_array as $idx => $str) {
      if ( strpos($str, "HTTP/") === 0 ) {
        $headers_array[] = $str;
      } else {
        $body_pieces_array_with_spliter 
          = array_slice($splited_string_array, $idx);
        break;
      }
    }
    $this -> body = implode($spliter
      , $body_pieces_array_with_spliter);
    $this -> raw_headers_set = end($headers_array);
  }

  function get_headers()
  {
    if ( $this -> raw_headers_set === NULL ) {
      $this -> parse_response_to_body_and_raw_headers_set();
    }
    if ( $this -> headers === NULL ) {
      $this -> parse_header_set_string_into_array();
    }
    return $this -> headers;
  }

  private function parse_header_set_string_into_array()
  {
    $spliter = "\r\n";
    $headers_array = array();
    $header_str_array = explode($spliter, $this -> raw_headers_set);
    
    foreach ($header_str_array as $idx => $val) {
      if (strpos($val, ':') === FALSE) continue;

      list($header_key, $header_val) = explode(':', $val, 2);
      $headers_array[trim($header_key)] = trim($header_val);
    }
    $this -> headers = $headers_array;
  }
}
?>
