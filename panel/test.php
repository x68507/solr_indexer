<?php
	$schema = 'C:\tika\solr\example\solr\collection1\conf\schema.xml';
	

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
	if (array_sum($aryField)!=count($aryField)) $sxe->asXML("fuck.xml");
	
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
?>








