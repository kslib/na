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
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

echo '--- file_get_contents <br>';
var_dump(file_get_contents('php://input'));
echo '<br>---<br>';

echo '--- json_decode <br>';
var_dump($input);
echo '<br>---<br>';

router($request);

//---------------------------
 
$ch = curl_init("http://115.159.55.151"); // such as http://example.com/example.xml

$options = array(CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HEADER => true,
	CURLINFO_HEADER_OUT => true
	);

curl_setopt_array($ch, $options);
$data = curl_exec($ch);
curl_close($ch);

var_dump($data);


 

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