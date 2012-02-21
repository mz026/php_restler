<?php
require_once(__DIR__ . "/../../restler.php");

class DescribeRestler extends \PHPSpec\Context
{
  function itShouldPass ()
  {
    $this -> spec(TRUE) -> should -> beTrue();
  }

  function itShouldBeAClass ()
  {
    $is_class = class_exists('Restler');
    $this -> spec($is_class) -> should -> beTrue();
  }

  function itShouldThrowGivenNoUrl()
  {
    $this -> spec(function() {
        $result = Restler::request();
      }) -> should -> throwException('NULL_URL_EXCEPTION');
  }

  function itShouldThrowGivenNoCallback()
  {
    $this -> spec(function() {
        $result = Restler::request(array('url' => 'http://localhost/phpTrial/echo'));
      }) -> should -> throwException('INVALID_ARGUMENT_EXCEPTION');
  }

  function itShouldBeAbleToGetNormalRequest()
  {
    $that = $this;
    $expected_result_array = array(
        'method' => 'GET'  
        , 'body' => array()
        , 'headers' => array(
          'host' => 'localhost/phpTrial/echo'
          , 'accept' => '*/*'
          , 'headerkey1' => 'headerVal1'
          , 'headerkey2' => 'headerVal2')
        , 'query' => array()
    );
    
    Restler::request(array(
        'url' => 'http://localhost/phpTrial/echo'
        , 'headers' => array(
            'headerkey1' => 'headerVal1'
          , 'headerkey2' => 'headerVal2')  
          , 'callback' => 
          function ($err, $result, $ch) use ($that, $expected_result_array) {
            $res_array = $that -> string2JSONArray($result->get_body());
            $that -> spec($err) -> should -> beNull();
            $that -> spec($res_array) -> shouldNot -> beNull();
            $that -> spec($res_array['headers']['headerkey1']) 
              -> should -> be('headerVal1');
            $that -> spec($res_array['headers']['headerkey2']) 
              -> should -> be('headerVal2');
          }
      ));
  }

  function itShouldBeAbleToPost()
  {
    $that = $this;
    $body = array(
      'hello' => 'world'
      , 'default' => 'Tomi');
    $headers = array(
      'headerkey1' => 'headerVal1');

    Restler::request(array(
      'url' => 'http://localhost/phpTrial/echo'
      , 'method' => 'POST'
      , 'headers' => $headers
      , 'body' => $body
      , 'callback' => function($err, $res, $ch)use($body, $that){
        $res_array = $that -> string2JSONArray($res->get_body());
        $that -> spec($res_array) -> shouldNot -> beNull();
        $that -> spec($res_array['method']) -> should -> be('POST');
        $that -> spec($res_array['body']) -> should -> be($body);
      }
    ));

  }
  function itShouldBeAbleToGetErrorRequest()
  {
    $that = $this;
    Restler::request(array(
      'url' => 'http://localhost/404'
      , 'callback' => function($err, $res, $ch) use ($that) {
        $that -> spec($err) -> should -> beFalse();
      }));
  }

  function string2JSONArray($str, $level=10)
  {
    return json_decode($str, true, 10);
  }

}
?>
