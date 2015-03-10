<?php
	session_start();
	
	libxml_use_internal_errors(true);
	$xml = simplexml_load_file(realpath(dirname(__FILE__).'\config.xml'));
	
	$username = 'root';
	$password = 'sap';
	
	//checks validation
	if ($_POST['username']==$username && $_POST['password']==$password){
	
		$_SESSION['uid'] = true;
	}else{
		unset($_SESSION['uid']);
	}

	if (!isset($_SESSION['uid']) || $_SESSION['uid']!=true){
		header('Location: index.php');
	}else{
		header('Location: panel.php');
	}
	
	





?>