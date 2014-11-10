<?php

	$base = dirname(__FILE__).'/../docs/SAP';
	

	$action = $_POST['action'];
	
	switch($action){
		case 'new_dir':
			//echo $_POST['name'];
			
			$root = $base.$_POST['name'];
			mkdir($root,0777);
			echo $root;
			break;
		case 'new_file':
			break;
		case 'del':
			$file = $base.$_POST['file'];
			if (is_dir($file)){
				//recursively delete all files
				//unlink($file);
				rmdir($file);
			}else{
				unlink($file);
			}
			
			break;
	}





?>