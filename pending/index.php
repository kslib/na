<?php


require_once('load.php');

echo '<meta charset="utf-8">'; 
//echo_table('servers');

echo 'holy shit!<br>';
echo '---<br>';
echo $_SERVER['REQUEST_METHOD'].' &nbsp; '.$_SERVER['PATH_INFO'];
echo '<br>---<br>';

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];

$path_info=trim($_SERVER['PATH_INFO'],'/');

preg_match('/^dada\/.*/', $path_info, $matches);

pre_dump($matches);






die();
$array_path = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

echo '--- file_get_contents <br>';
var_dump(file_get_contents('php://input'));
echo '<br>---<br>';

echo '--- json_decode <br>';
var_dump($input);
echo '<br>---<br>';

$response=router($array_path);
pre_dump($response);
die(); 
//---------------------------
 


// $http_info = get_http_info("211.87.148.243");
// $json_http_info = json_encode($http_info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// pre_dump($http_info);
// pre_echo($json_http_info);

// $log_detail['http']=$http_info;
// $json_log_detail = json_encode($log_detail, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// pre_dump($log_detail);
// pre_echo($json_log_detail);


$ip = get_item_by_uname('win8')['ip'];


echo '>>>>> response<br>';

$r= dada('211.87.148.243');

$r = json_encode($r, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
pre_dump($r);



echo '<<<<< response<br>';
















die(); 
// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;
 
// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($db_link) {
  if ($value===null) return null;
  return mysqli_real_escape_string($db_link,(string)$value);
},array_values($input));
 
// build the SET part of the SQL command
$set = '';
for ($i=0;$i<count($columns);$i++) {
  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
}
 
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $sql = "select * from `$table`".($key?" WHERE id=$key":''); break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete `$table` where id=$key"; break;
}
 
echo '--- sql <br>';
var_dump($sql);
echo '<br>---<br>';


// excute SQL statement
$result = mysqli_query($db_link,$sql);

// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die(mysqli_error($db_link));
}
 
// print results, insert id or affected row count
if ($method == 'GET') {
  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  }
  if (!$key) echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($db_link);
} else {
  echo mysqli_affected_rows($db_link);
}
 
// close mysql connection
$dbh = null;