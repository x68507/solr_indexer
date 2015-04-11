<?php
	require_once(realpath(__DIR__ .'/config.php'));
	$myBase = $baseDir;
	require_once(realpath(__DIR__ .'/tika_file.php'));
	session_start();
	if ((!isset($_SESSION['uid']) || $_SESSION['uid']!=true) && 1==0){
		die('dying');
	}
	$action = $_POST['action'];
	
	$host = $remote_ip;
	
	switch($action){
		case 'server_start':
			echo server_start();
			break;
		case 'server_stop':
			echo server_stop();
			break;
		case 'refresh_index':
			$url = 'http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E*:*%3C/query%3E%3C/delete%3E';
			$context = stream_context_create(array('http' => array('header' => "Host: $host")));
			$result = file_get_contents($url, 0, $context);
			$base = realpath(__DIR__ .'/..//'.$myBase);
			
			$tika = dirname(__FILE__).'/tika.php';
			$txt = realpath($php).' '.realpath($tika).' '.realpath($base);
			echo $txt;
			//pclose(popen($txt,'r'));
			//exec($txt);
			
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
			
			
			
			/*
			if (file_exists($new)){
				echo '<error>File already exists</error>';
				die;
			}
			
			rename($old,$new);
			if (isset($_COOKIE['scan']) && $_COOKIE['scan']){
				
				//deletes old file
				//del(basename($old));
				//re-adds new file
				//tika($new);
				
			}
			*/
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
						
					}
					del($val);
					unlink($file);
				}
				//echo '<file>'.($curDir.$x.$val).'</file>';
			}
			break;
		case 'logout':
			$_SESSION['uid'] = false;
			header('Location: index.php');
			break;
		case 'schema':
			fncSchema();
			break;
		case 'secure_ip':
			$host = secureJetty($_POST['host']);
			server_stop();
			sleep(1);
			server_start();
			echo 'IP secured @ '.$host;
			break;
	}

function server_start(){
	global $solr;
	
	fncSchema();
	$cmd = 'START /B '.realpath($solr).' start -h localhost';
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	$fp = fsockopen('127.0.0.1', 8983, $errno, $errstr, 1);
	if ($fp) {
		$str = '&nbsp;&nbsp;&nbsp;Port is already in use';
	}else{
		pclose(popen($cmd,'r'));
		$str = 'Server started';
	}
	
	fclose($fp);
	error_reporting(-1);
	
	return $str;
}

function server_stop(){
	global $solr;
	$cmd = 'START /B '.realpath($solr).' stop -p 8983';
	//pclose(popen($cmd,'r'));echo 'Server stopped';
	exec($cmd);
	return 'Server stopped';
}

function fncSchema(){
	xmlRegex();

	global $schema;
	
	$xmlSchema = simplexml_load_file($schema);

	$sxe = new SimpleXMLElement($xmlSchema->asXML());
	//arrays to check for SOLR modifications
	$aryField = array('creator'=>0,'fileName'=>0,'lastModified'=>0,'pageCount'=>0,'contentType'=>0,'baseDir'=>0,
		'fileNameSort'=>0,'baseDirURL'=>0,'baseDirSort'=>0);
	
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
					addNode('creator','text_general',$sxe);break;
				case 'fileName':
					addNode('fileName','text_general',$sxe);break;
				case 'lastModified':
					addNode('lastModified','date',$sxe);break;
				case 'pageCount':
					addNode('pageCount','int',$sxe);break;
				case 'contentType':
					addNode('contentType','text_general',$sxe);break;
				case 'baseDir':
					addNode('baseDir','text_general',$sxe);break;		
				case 'fileNameSort':
					addNode('fileNameSort','alphaOnlySort',$sxe);
					addNodeCopy('fileName','fileNameSort',$sxe);
					break;
				case 'baseDirURL':
					addNode('baseDirURL','string',$sxe);
					addNodeCopy('baseDir','baseDirURL',$sxe);
					break;
				case 'baseDirSort':
					addNode('baseDirSort','alphaOnlySort',$sxe);
					addNodeCopy('baseDir','baseDirSort',$sxe);
					break;
			}
		}
	}
	
	
	
	
	//saves file if modified
	if (array_sum($aryField)!=count($aryField)){
	
		$sxe->asXML($schema);
		
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$xml = simplexml_load_file($schema)->asXML();
		$doc->loadXML($xml);
		$doc->save($schema);
	}
}

function xmlRegex(){
	global $schema;
	
	$xmlSchema = simplexml_load_file($schema);
	
	//fixes alphaOnlySort regex to handle file names beginning with numbers
	$bSave = false;
	foreach($xmlSchema->fieldType as $ft){
		if ((string)$ft['name']=='alphaOnlySort'){
			foreach($ft->analyzer->filter as $filter){
				if ((string)$filter['class']=='solr.PatternReplaceFilterFactory' && (string)$filter['pattern']!='([^a-z0-9])'){
					$filter['pattern'] = '([^a-z0-9])';
					$bSave = true;
				}
			}
		}
	}
	if ($bSave==true){
		$xmlSchema->asXML($schema);
	}
}

function addNodeCopy($source,$dest,$sxe){
	$newItem = $sxe->addChild('copyField');
		$newItem->addAttribute('source',$source);
		$newItem->addAttribute('dest',$dest);
	unset($newItem);
	return $sxe;
}


function addNode($name,$type,$sxe){
	$newItem = $sxe->addChild('field');
		$newItem->addAttribute('name',$name);
		$newItem->addAttribute('type',$type);
		$newItem->addAttribute('indexed','true');
		$newItem->addAttribute('stored','true');
	unset($newItem);
	return $sxe;	
}

function secureJetty($host){
	global $jetty,$remote_ip;
	
	if (strlen($host)==0) $host = 'localhost';
	
	$xml = simplexml_load_file($jetty);
	
	//arrays to check for SOLR modifications
	$aryProp  = array('host'=>$host,'port'=>'8983');
	$aryField = array();
	
	//checking to see if XML node is present in schema file
	foreach($xml->Call->Arg->{'New'}->Set as $item){
		if (array_key_exists((string)$item['name'],$aryProp)){
			foreach($item->SystemProperty as $sp){
				if (isset($sp['default'])){
					$sp['default'] = $aryProp[(string)$item['name']];
				}else{
					$sp->addAttribute('default',$aryProp[(string)$item['name']]);
				}
			}
		}
	}
	$xml->asXML($jetty);
	unset($xml);
	
	//changes config.xml to reflect proper IP address for searching remote server
	$config = realpath(__DIR__ .'/config.xml');
	$xml = simplexml_load_file($config);
	
	$xml->remote_ip = $host;
	
	$xml->asXML($config);
	
	
	
	return $host;
}

function del($file){
	$q = urlencode('fileName:"'.$file.'"');// AND baseDir:""');
	$url = 'http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E'.$q.'%3C/query%3E%3C/delete%3E';
	file_get_contents($url);
}



?>