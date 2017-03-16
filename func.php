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
	url_match("$url");
	url_match("$url");
}

function url_match($regx){

	return ;
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