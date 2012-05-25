Restler for PHP
=========

## Installation:

clone and include ``restler.php`` file would introduce a class ``Restler`` under global namespace.

## Usage:

    $result = Restler::request(array(
      'url' => 'http://google.com'
      , 'method' => 'GET' (optional, default to 'GET')
      , 'headers' => array('key' => 'val') (optional)
      , 'body' => <string> | <array> (optional)
    ));

    $status = $result['status'];
    $response = $result['response'];

    $body = $response -> get_body();
    $responseHeaders = $response -> get_headers();

## Test
restler uses PHPSpec to test. To run the test:
1 . first, ``cd`` to ``test`` directory.

    $ cd /path/to/test

2 . second, 

    $ ./phpspec-composer spec/DescribeRestler.php -c
