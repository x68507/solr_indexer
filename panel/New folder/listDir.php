<?php
	require_once('config.php');
	echo "<?xml version='1.0' encoding='utf-8'?>";
	echo "<xml>";
	
	$baseDir = realpath(dirname(__FILE__).'../..//'.$baseDir);
	if (isset($_COOKIE['curDir'])){
		$curDir = realpath($_COOKIE['curDir']);
	}else{
		$curDir = realpath($baseDir);
	}
	echo '<poop>'.$_COOKIE['curDir'].'</poop>';
	echo '<poop>'.realpath('c:\fucker').'</poop>';
	//prevents user from going outside of base directory
	if (strlen($curDir)<strlen($baseDir)){
		$curDir = $baseDir;
	}
	
	
	
	
	$dir = new DirectoryIterator($curDir);
	$aryFiles = array();
	$aryDirs = array();
	
	foreach($dir as $fileinfo){
		if (!$fileinfo->isDot()){
			
			if ($fileinfo->isFile()){
				array_push($aryFiles,$fileinfo->getFilename());
			}elseif($fileinfo->isDir()){
				array_push($aryDirs,$fileinfo->getFilename());
			}
		}
	}
	
	asort($aryFiles);
	asort($aryDirs);
	echo "<curDir>$curDir</curDir>";
	foreach($aryDirs as $val){
		echo "<dir>$val</dir>";
	}
	foreach($aryFiles as $val){
		echo "<file>$val</file>";
	}
	
	echo "</xml>";
?>