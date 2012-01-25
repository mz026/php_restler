Restler for PHP
=========

## Installation:

clone and include ``restler.php`` file would introduce a class ``Restler`` under global namespace.

## Usage:

    Restler::request(array(
      'url' => 'http://google.com'
      , 'callback' => function ($err, $result, $curl_handle){
        if ($err !== NULL) { // success
          // do something with $result
        }
        else {
          // do something else
        }
      }
      , 'method' => 'GET' (optional, default to 'GET')
      , 'headers' => array('key' => 'val') (optional)
      , 'body' => <string> | <array> (optional)
    ));

## Test
restler uses PHPSpec to test. To run the test:
1 . first, ``cd`` to ``test`` directory.

    $ cd /path/to/test

2 . second, 

    $ ./phpspec-composer spec/DescribeRestler.php -c
