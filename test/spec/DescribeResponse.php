<?php
require_once(__DIR__ . "/../../Response.php");

class DescribeResponse extends \PHPSpec\Context {
  function itShouldBeAClass ()
  {
    $this -> spec(class_exists('Response')) -> should -> beTrue();
  }

  function itCanParseBodyOfSingleHeadersResponse()
  {
    $response_str 
      = $this -> fake_response_with_single_headers_set();
    $response = new Response($response_str);
    $body = $response -> get_body();

    $this -> spec($body) -> should -> be($this->fake_response_body());
  }

  private function fake_response_with_single_headers_set()
  {
    return ($this -> fake_response_headers_set('200 OK')). 
      ($this -> fake_response_body());
  }

  private function fake_response_headers_set($http_status)
  {
    $to_return = '';

    $to_return .= "HTTP/1.1 $http_status\r\n";
    $to_return .= "X-Powered-By: Express\r\n";
    $to_return .= "Content-Type: application/json; char-set=utf8\r\n";
    $to_return .= "\r\n";

    return $to_return;
  }

  private function fake_response_body()
  {
    return "ya! Im body.";
  }

  function itCanParseBodyOfMultipleHeadersResponse()
  {
    $response_str = $this -> fake_response_with_multiple_headers_set();

    $response = new Response($response_str);
    $body = $response -> get_body();

    $this -> spec($body) -> should -> be($this->fake_response_body());
  }

  private function fake_response_with_multiple_headers_set()
  {
    return ($this -> fake_response_headers_set('100 continue'))
      . ($this -> fake_response_headers_set('200 OK'))
      . ($this -> fake_response_body());
  }

  function itCanParseHeadersWithSingleHeadersResponse()
  {
    $response_str = $this -> fake_response_with_single_headers_set();
    $response = new Response($response_str);
    $headers = $response -> get_headers();

    $this -> spec($headers) -> should -> beEqualTo(array(
      'X-Powered-By' => 'Express'  
      , 'Content-Type' => 'application/json; char-set=utf8'
    ));
  }

  function itCanParseHeadersWithMultipleHeadersResponse()
  {
    $response_str = $this -> fake_response_with_multiple_headers_set();
    $response = new Response($response_str);
    $headers = $response -> get_headers();

    $this -> spec($headers) -> should -> beEqualTo(array(
      'X-Powered-By' => 'Express'  
      , 'Content-Type' => 'application/json; char-set=utf8'
    ));
  }
}
?>
