<?php
	require_once(realpath(__DIR__ .'/config.php'));
	
	
	$a = (isset($_GET['a'])?$_GET['a']:'add');
	$xml = simplexml_load_file($jetty);
	
	//arrays to check for SOLR modifications
	$aryProp  = array('host'=>'localhost','port'=>'8983');
	$aryField = array();
	
	//checking to see if XML node is present in schema file
	foreach($xml->Call->Arg->{'New'}->Set as $item){
		if (array_key_exists((string)$item['name'],$aryProp)){
			foreach($item->SystemProperty as $sp){
				if (strlen((string)$sp['default'])==0){
					$sp->addAttribute('default',$aryProp[(string)$item['name']]);
				}
			}
		}
	}
	if (count($aryField)>0){
		echo "<pre>";
			print_r($aryField);
		echo "</pre>";
	}
	
	$xml->asXML($jetty);
?>