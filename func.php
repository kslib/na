<?php



// db
try {
	$db = new PDO('mysql:host=localhost;dbname=na', 'root', '123');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}

function router($url){
	// url_match("$url");
	// url_match("$url");
}

function url_match($regx){

	return ;
}

function is_url_valid(){
	if(!$url || !is_string($url) || ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)){
		return false;
	}
	return true;
}

// $url must checked
function get_http_info($url){

	$ch = curl_init($url); 

	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => true,
		CURLOPT_NOBODY => true,
		CURLOPT_TIMEOUT => 10
		);

	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

	$http_info=$info;
	$http_info['header']=substr($response, 0, $info['header_size']);
	return $http_info;
}


function get_detail($url){
	$log_detail = [];
	$log_detail['http']=get_http_info($url);

	return $log_detail;
}

function get_sql_result($sql){
	global $db;
	$sql = "";
}


function get_obj($query_statment){
	global $db;

	try {
		$result = $db->query($query_statment);

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	return $result;
}

function get_col_name($table_name){
	global $db;

	try {
		$q = $db->prepare("DESCRIBE $table_name");
		$q->execute();
		$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);

	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	return $table_fields;
}




// mysql only
function echo_table($table_name){

	global $db;

	$sql = "SELECT * from $table_name";
	$result = get_obj($sql);
	$array_col_name=get_col_name($table_name);

	echo '<table border="1px solid black" style="border-collapse: collapse;">';

	echo '<thead><tr>';
	foreach ($array_col_name as $col_name) {
		echo '<th>'.$col_name.'</th>';
	}
	echo '</tr></thead>';

	echo '<tbody>';
	foreach ($result as $row) {
		echo '<tr>';
		foreach ($array_col_name as $col_name) {
			echo '<td>'.$row[$col_name].'</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';

	echo '</table>';


}


function get_item_by_uname($uname){

	if (trim($_SERVER['PATH_INFO'],'/') == 'dada/uname/al') {
		echo '<br> haha ,dada get ~~~~~~ <br>';

		$table_name = 'servers';

		$sql = "SELECT * from $table_name where uname = '$uname' ";
		$result = get_obj($sql);
		if ($count_line = 1) {

			foreach ($result as $row) {
				return $row;
			}


		}else{
			die('uname was not unique!');
		}

		$array_col_name=get_col_name($table_name);

	} 

}




function do_action($function_name,$arg){
	global $db;

	if ( function_exists($function_name) ){
		$a = call_user_func($function_name, $arg);
		$db = null;
		return $a;
	}else{
		return null;
	}
}



?>