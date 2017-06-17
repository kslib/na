<?php

function pre_echo($foo){
	echo '<pre>'.$foo.'</pre>';
}

function pre_dump($foo){
	echo '<pre>'.var_export($foo,true).'</pre>';
}

?>