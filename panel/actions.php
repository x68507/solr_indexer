<?php
	require_once(realpath(__DIR__ .'/config.php'));
	$myBase = $baseDir;
	require_once(realpath(__DIR__ .'/tika_file.php'));
	session_start();
	if ((!isset($_SESSION['uid']) || $_SESSION['uid']!=true) && 1==0){
		die('dying');
	}
	$action = $_POST['action'];
	
	switch($action){
		case 'server_start':
		
			$cmd = 'START /B '.realpath($solr).' start -h localhost';
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			$fp = fsockopen('127.0.0.1', 8983, $errno, $errstr, 1);
			if ($fp) {
				
				
				//echo '1'.'<hr>';
				echo '&nbsp;&nbsp;&nbsp;Port is already in use';
			}else{
				//exec($cmd);
				pclose(popen($cmd,'r'));echo 'Server started';
				//echo $cmd.'<hr>';
				//echo '2'.'<hr>';
			}
			
			fclose($fp);
			error_reporting(-1);
			
			
			break;
		case 'server_stop':
			$cmd = 'START /B '.realpath($solr).' stop -p 8983';
			//pclose(popen($cmd,'r'));echo 'Server stopped';
			exec($cmd);echo 'Server stopped';
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
			$txt = realpath($php).' '.realpath($tika).' '.realpath($base);
			echo $txt;
			//pclose(popen($txt,'r'));
			exec($txt);
			
			//echo 'Parsed & updated base directory';
			
			
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
		case 'schema':
			$xmlSchema = simplexml_load_file($schema);
			$sxe = new SimpleXMLElement($xmlSchema->asXML());

			//arrays to check for SOLR modifications
			$aryField = array('creator'=>0,'fileName'=>0,'lastModified'=>0,'pageCount'=>0,'contentType'=>0,'baseDir'=>0);
			
			//checking to see if XML node is present in schema file
			foreach($xmlSchema->field as $field){
				
				if (array_key_exists((string)$field['name'],$aryField)){
					$aryField[(string)$field['name']] = 1;
				}
			}	
			//re-writing schema file to include
			foreach($aryField as $key=>$val){
				if (intval($val)==0){
					switch($key){
						case 'creator':
							addNode('creator','text_general');break;
						case 'fileName':
							addNode('fileName','text_general');break;
						case 'lastModified':
							addNode('lastModified','date');break;
						case 'pageCount':
							addNode('pageCount','int');break;
						case 'contentType':
							addNode('contentType','text_general');break;
						case 'baseDir':
							addNode('baseDir','text_general');break;		
					}
				}
			}
			//saves file if modified
			if (array_sum($aryField)!=count($aryField)) $sxe->asXML($schema);
			break;
	}


function addNode($name,$type){
	global $sxe;
	$newItem = $sxe->addChild('field');
		$newItem->addAttribute('name',$name);
		$newItem->addAttribute('type',$type);
		$newItem->addAttribute('indexed','true');
		$newItem->addAttribute('stored','true');
	unset($newItem);
	return $sxe;	
}

function del($file){
	$q = urlencode('fileName:"'.$file.'"');// AND baseDir:""');
	$url = 'http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E'.$q.'%3C/query%3E%3C/delete%3E';
	file_get_contents($url);
}



?>