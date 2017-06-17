<?php
require_once('load.php');
header('Content-Type: application/json');

//dump_req_data();

$array_data = router($_SERVER['REQUEST_METHOD'],$_SERVER['PATH_INFO']);
$json_data = json_encode($array_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//$json_data_slash_removed=stripslashes($json_data);

echo $json_data;
