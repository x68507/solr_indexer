<?php
//Error Handle

function customError($errno, $errstr,$errfile,$errline){
	echo "<b>Error:</b> [$errno] $errstr in file $errfile on line $errline<br>";
	echo "Ending Script";
	die();
}
set_error_handler("customError");

?>