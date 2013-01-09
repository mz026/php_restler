<?php
$theHeaders = apache_request_headers();
$response = json_encode(array(
  'body' => $_POST
  , 'query' => $_GET
  , 'headers' => $theCoolHeaders
  , 'files' => $_FILES
));

foreach ($theHeaders as $key => $val) {
  header("$key: $val");
}
?>
