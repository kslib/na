<?php

function router($request_method,$path_info){
	$map_regex_func = array(
		'/^\/tricks\//' => 'process',
		'/^\/tricks$/' => 'get_valid_tricks',	
		'/^\/deposit/' => 'api_deposit', 
		);

	$map_regex_func += array(
		'/^\/?$/'=> 'resp_index',
		'/^\/servers$/' => 'resp_servers',
		'/^\/tables$/' => '',
		'/^\/tables\//' => 'resp_table_data',
		'/^\/logs\//' => 'resp_logs',
		'/^\/test$/' => 'resp_test',
		);


	foreach ($map_regex_func as $regex_pattern => $function_name) {
		if (preg_match($regex_pattern,$path_info) === 1){
			$url_chopped = preg_replace($regex_pattern, '', $path_info);
			return call_user_func($function_name, $url_chopped);
		}
	}

	return array('message'=> 'Not found');
}

function resp_test($url){

	insert_log();

}

function insert_log(){
	$db= get_db();
	try {
		$statment = $db->prepare("INSERT INTO logs (ip, time, facility,type,online,level,detail) VALUES (:ip, :time, :facility, :type, :online, :level, :detail)");
		$statment->execute(array(
			"ip" => "1.1.1.1",
			"time" => "13:00",
			"facility" => "server",
			"type" => "passive",
			"online" => 1,
			"level" => 1,
			"detail" => "this is details",
			));
		
	} 
	catch(PDOException $e)
	{
		echo "Error: " . $e->getMessage();
		die();
	}

	return true;
}


function resp_logs($log_facility){
	$req= array(
		'get' => $_GET[''], 
		);
}

function resp_servers(){
	return get_table('servers');//pre_dump(get_col_name('servers'));
}

	//check is_name_valid
function resp_table_data($table_name){
	return get_table($table_name);
	//pre_dump(get_col_name('servers'));
}

function get_table($table_name){
	$db = get_db();

	$sql = "SELECT * from $table_name";
	$result = get_obj_from_sql($sql);
	$array_col_name=get_col_name($table_name);
	$array_rows=array();
	$array_row=array();

	foreach ($result as $row) {
		$array_row =[];
		foreach ($array_col_name as $col_name) {
			array_push($array_row, $row[$col_name]);
		}
		array_push($array_rows, $array_row);
	}

	$table_data=array(
		'col_name'=>$array_col_name,
		'row_data'=>$array_rows,
		);

	return $table_data;
}






function get_obj_from_sql($query_statment){
	$db=get_db();

	try {
		$result = $db->query($query_statment);
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	return $result;

	
}

function get_col_name($table_name){
	$db = get_db();

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

function resp_index(){
	$array=array(
		'servers'=>'/servers',
	);

	return $array;
}




function get_db(){
	try {
		$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	return $db;
}



function dump_req_data(){
	$config_data = array();
	$config_data['ABS_API_PATH'] = ABS_API_PATH;
	$config_data['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
	$config_data['PATH_INFO'] = $_SERVER['PATH_INFO'];

	pre_dump($config_data);
}

function dump_sys_data(){
	$config_data = array();
	$config_data['SYS_NAME'] = SYS_NAME;
	pre_dump($config_data);
}

function say_hi(){
	echo 'hi~ this is natrix';
}

function pre_dump($var){
	echo '<pre>'.var_export($var,true).'</pre>';
}