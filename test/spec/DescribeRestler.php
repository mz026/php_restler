<?php
require_once(__DIR__ . "/../../Restler.php");

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
      $result = Restler::request(
        array('url' => 'http://localhost/phpTrial/echo.php'));
      }) -> shouldNot -> throwException('INVALID_ARGUMENT_EXCEPTION');
  }

  function itShouldBeAbleToGetNormalRequest()
  {
    $result = Restler::request(array(
        'url' => 'http://localhost/phpTrial/echo.php'
        , 'headers' => array(
            'headerkey1' => 'headerVal1'
          , 'headerkey2' => 'headerVal2')  
      ));
    $res_array = json_decode($result['response'] -> get_body(), true, 10);

    $this -> spec($result['status']) -> should -> be(200);
    $this -> spec($res_array['headers']['headerkey1']) 
      -> should -> be('headerVal1');
    $this -> spec($res_array['headers']['headerkey2']) 
      -> should -> be('headerVal2');


  }

  function itShouldBeAbleToPost()
  {
    $body = array(
      'hello' => 'world'
      , 'default' => 'Tomi');
    $headers = array(
      'headerkey1' => 'headerVal1');

    $result = Restler::request(array(
      'url' => 'http://localhost/phpTrial/echo.php'
      , 'method' => 'POST'
      , 'headers' => $headers
      , 'body' => $body
    ));

    $res_arr = json_decode($result['response'] -> get_body(), TRUE, 10);
    $this -> spec($res_arr['body']) -> should -> be($body);
    $this -> spec($res_arr['headers']['headerkey1']) 
      -> should -> be('headerVal1');

  }
  function itShouldBePostWithUrlEncodedContentType()
  {
    $body = array(
      'hello' => 'world'
      , 'default' => 'Tomi');
    $headers = array(
      'headerkey1' => 'headerVal1'
      , 'Content-Type' => 'application/x-www-form-urlencoded'
    );

    $result = Restler::request(array(
      'url' => 'http://localhost/phpTrial/echo.php'
      , 'method' => 'POST'
      , 'headers' => $headers
      , 'body' => $body
    ));

    $res_arr = json_decode($result['response'] -> get_body(), TRUE, 10);
    $this -> spec($res_arr['body']) -> should -> be($body);
    $this -> spec($res_arr['headers']['headerkey1']) 
      -> should -> be('headerVal1');

  }
  function itShouldBeAbleToGetErrorRequest()
  {
    $that = $this;
    $result = Restler::request(array(
      'url' => 'http://localhost/404'
    ));

    $this -> spec($result['status']) -> should -> be(404);
  }

  function string2JSONArray($str, $level=10)
  {
    return json_decode($str, true, 10);
  }

}
?>
