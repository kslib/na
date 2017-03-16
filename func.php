<?php



// db
try {
    $db = new PDO('mysql:host=localhost;dbname=na', 'root', '123');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
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





function echo_table($table_name){

$sql = "SELECT * from $table_name";
$result = get_obj($sql);



foreach ($result as $row) {
	do_action('pre_dump',$row);
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