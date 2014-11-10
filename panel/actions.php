<?php
	require_once(realpath(__DIR__ .'/config.php'));
	$myBase = $baseDir;
	require_once(realpath(__DIR__ .'/tika_file.php'));
	session_start();
	if (!isset($_SESSION['uid']) || $_SESSION['uid']!=true){
		die;
	}
	$action = $_POST['action'];
	
	switch($action){
		case 'server_start':
			$cmd = 'START /B '.$solr.' start -h localhost';
			
			pclose(popen($cmd,'r'));echo 'Server started';
			break;
		case 'server_stop':
			$cmd = 'START /B '.$solr.' stop -p 8983';
			pclose(popen($cmd,'r'));echo 'Server stopped';
			break;
		case 'refresh_index':
			/*
			$m = fopen(dirname(__FILE__).'\base.txt','r');
				$file = fread($m,filesize(dirname(__FILE__).'\base.txt'));
				$base = realpath(dirname(__FILE__).'../..//'.$file);
			fclose($m);
			*/
			$base = realpath(__DIR__ .'/..//'.$myBase);
			
			
			//echo $base.'<br>';
			
			$tika = dirname(__FILE__).'/tika.php';
			$txt = $php.' '.$tika.' '.$base;
			//echo '<hr>'.$txt.'<hr>';
			exec($txt);
			
			echo 'Parsed & updated base directory';
			
			
			//pclose(popen('START '.$txt,'r'));
			break;
		case 'update_base':
			$m = fopen('base.txt', 'w');
			fwrite($m, $_POST['base']);
			fclose($m);
			echo 'Updated root folder';
			break;
		case 'rname':
			$dir = $_POST['curDir'];
			$type = $_POST['type'];
			$x = ($type=='file'?'\\':'');
			$old = realpath($dir.$x.$_POST['oldName']);
			$new = ($dir.$x.$_POST['newName']);
			if (file_exists($new)){
				echo '<error>File already exists</error>';
				die;
			}
			
			rename($old,$new);
			if (isset($_COOKIE['scan']) && $_COOKIE['scan']){
				del(basename($old));
				tika($new);
			}
			break;
		case 'new_directory':
			$dir = $_POST['curDir'];
			$x = '\\';
			$new = $_POST['name'];
			mkdir($dir.$x.$new,777);
			break;
		case 'del':
			$json = json_decode($_POST['json']);
			$curDir = $_POST['curDir'];
			$x = '\\';
			foreach($json as $val){
				$file = realpath($curDir.$x.$val); 
				if (is_dir($file)){
					rmdir($file);
				}else{
					//need to extract the base and add this as a second key
					if (isset($_COOKIE['scan']) && $_COOKIE['scan']){
						del($val);
					}
					unlink($file);
				}
				echo '<file>'.realpath($curDir.$x.$val).'</file>';
			}
			break;
		case 'logout':
			$_SESSION['uid'] = false;
			header('Location: index.php');
			break;
	}




function del($file){
	$q = urlencode('fileName:"'.$file.'"');// AND baseDir:""');
	$url = 'http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E'.$q.'%3C/query%3E%3C/delete%3E';
	file_get_contents($url);
}



?>