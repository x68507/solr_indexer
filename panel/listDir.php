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

	//prevents user from going outside of base directory
	if (strlen($curDir)<strlen($baseDir)){
		$curDir = $baseDir;
	}
	
	$aryExt = array('3gp','afp','afpa','asp','aspx','avi','bat','bmp','c','cfm','cgi','com','cpp','css','doc','docx','exe','gif','fla','h','htm','html','jar','jpg','jpeg','js','lasso','log','m4p','mov','mp3','mp4','mpg','mpeg','msg','ogg','pcx','pdf','php','png','ppt','pptx','psd','pl','py','rb','rbx','rhtml','rpm','ruby','sql','swf','tif','tiff','txt','vb','wav','wmv','xls','xlsx','xml','zip');
		
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
		echo "<file>";
			echo "<name>$val</name>";
			$ext = pathinfo($val,PATHINFO_EXTENSION);
			echo "<ext>ext_".(in_array($ext,$aryExt)?$ext:'file')."</ext>";
		echo "</file>";
	}
	
	$aryDir = scan_dir($curDir);
	
		echo "<numFiles>".$aryDir."</numFiles>";
	echo "</xml>";
	
function scan_dir($path){
    $allowed = array('pdf','doc','docx');
	
	$ite=new RecursiveDirectoryIterator($path);

    $bytestotal=0;
    $nbfiles=0;
    foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {
        
		$ext = pathinfo($cur,PATHINFO_EXTENSION);
		if (in_array(strtolower($ext),$allowed)){
			$filesize=$cur->getSize();
			$bytestotal+=$filesize;
			$nbfiles++;
			$files[] = $filename;
		}
    }

    $bytestotal=number_format($bytestotal);
	return $nbfiles;
    //return array('total_files'=>$nbfiles,'total_size'=>$bytestotal,'files'=>$files);
}

?>