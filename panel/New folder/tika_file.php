<?php
	$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                      # ...one or more times
  )
| ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
| ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
/x
END;
	require_once(realpath(__DIR__ .'\config.php'));
	$nl = "\n";
	
	$baseDir = realpath(dirname(__FILE__).'/..//'.$baseDir);
	//$baseDir = 'C:\xampp\htdocs\TEMP\sap\docs\SAP';
	
	
	//Setup basic PHP classes for using Tika
	$soft = realpath(dirname(__FILE__).'/../Funstaff/vendor/autoload.php');
	require_once($soft);

	use Funstaff\Tika\Configuration;
	use Funstaff\Tika\Document;
	use Funstaff\Tika\Wrapper;
	
	
	if (!isset($time_start)){
		$time_start = microtime(true);
	}
	
	if (!isset($file)){
		
	}
	//tika('C:\xampp\htdocs\sap\docs\DMS\CV04N_FIND DOCUMENT.pdf');
	
	if (1==0){
		$file = 'C:\xampp\htdocs\sap\docs\DMS\CV04N_FIND DOCUMENT.pdf';
		$config = new Configuration($tika);
		$config->setOutputFormat('html');
		$config->setOutputEncoding('UTF-8');

		$wrapper = new Wrapper($config);
		$wrapper->addDocument(new Document('doc',$file));
		$wrapper->execute();
		
		$document = $wrapper->getDocument('doc');
		$metadata = $document->getMetadata();
		echo $document->getRawContent();
		echo '<hr>';
		echo "<pre>";print_r($metadata);echo "</pre>";
	}
	
	

function tika($file){
	
	global $ext,$tika,$post,$baseDir,$time_start,$parent,$regex;
	
	
	$config = new Configuration($tika);
	$config->setOutputFormat('xml');
	$config->setOutputEncoding('UTF-8');

	$wrapper = new Wrapper($config);
	$wrapper->addDocument(new Document('doc',$file));
	$wrapper->execute();
	
		
		$document = $wrapper->getDocument('doc');
		$content = $document->getContent();
		
		/*-----------------------------------------*/
		/*-----------------------------------------*/
		/*-----------------------------------------*/
		$content = convert_smart_quotes($content);
		/*-----------------------------------------*/
		/*-----------------------------------------*/
		/*-----------------------------------------*/
		
		$content = html_entity_decode($content);
		$content = utf8_encode($content);
		$content = preg_replace( "/\r|\n/", " ", $content );
		$content = preg_replace("/\]\]\>/","",$content);
		$content = preg_replace('/\s+/', ' ',$content);
		$content = str_replace(array('&nbsp;'),' ',$content);
		$content = preg_replace_callback($regex, "utf8replacer", $content);
		
		
		$metadata = $document->getMetadata();
		$type = $metadata->get('Content-Type');
		$meta = array(
			'md5'			=> md5_file($file)
			,'contentType'	=> $type
			,'baseDir'		=> substr(dirname($file),strlen($baseDir))
			//,'baseDir'		=> $baseDir
		);
		
		try{
			$meta['creator'] = $metadata->get('creator');
		}catch(Exception $e){
			$meta['creator'] = 'None';
		}
		try{
			$meta['fileName'] = $metadata->get('resourceName');
		}catch(Eception $e){
			$meta['fileName'] = basename($file);
		}
		try{
			$meta['lastModified'] = $metadata->get('Last-Modified');
		}catch(Exception $e){
			$meta['lastModified'] = '1970-01-01T01:01:01Z';
		}
		try{
			$meta['title'] = $metadata->get('dc:title');
		}catch(Exception $e){
			$meta['title'] = basename($file);
		}
		$meta['pageCount']	= $metadata->get('xmpTPg:NPages');
		
		$xml = new DOMDocument( "1.0", "UTF-8" ); 		
		$base = $xml->appendChild($xml->createElement( 'add' )); 
			$add = $base->appendChild($xml->createElement('doc'));
			
			
			$id = $add->appendChild($xml->createElement('field',$meta['md5']));
				$id->setAttributeNode(new DOMAttr('name','id'));
			/*
			$creator = $add->appendChild($xml->createElement('field', $meta['creator']));
				$creator->setAttributeNode(new DOMAttr('name','creator'));
			
			$title = $add->appendChild($xml->createElement('field', htmlentities($meta['title'])));
				$title->setAttributeNode(new DOMAttr('name','title'));
			
			*/
			
			$creator = $add->appendChild($xml->createElement('field')); 
			$creator->appendChild($xml->createCDATASection($meta['creator']));
				$creator->setAttributeNode(new DOMAttr('name','creator'));
			
			$title = $add->appendChild($xml->createElement('field')); 
			$title->appendChild($xml->createCDATASection($meta['title']));
				$title->setAttributeNode(new DOMAttr('name','title'));
						
			$bd = $add->appendChild($xml->createElement('field')); 
			$bd->appendChild($xml->createCDATASection($meta['baseDir'].'\\'));
				$bd->setAttributeNode(new DOMAttr('name','baseDir'));
				
			$fileName = $add->appendChild($xml->createElement('field')); 
			$fileName->appendChild($xml->createCDATASection($meta['fileName']));
				$fileName->setAttributeNode(new DOMAttr('name','fileName'));
			$lastModified = $add->appendChild($xml->createElement('field', $meta['lastModified']));
				$lastModified->setAttributeNode(new DOMAttr('name','lastModified'));
			$pageCount = $add->appendChild($xml->createElement('field', $meta['pageCount']));
				$pageCount->setAttributeNode(new DOMAttr('name','pageCount'));
			$contentType = $add->appendChild($xml->createElement('field', $meta['contentType']));
				$contentType->setAttributeNode(new DOMAttr('name','contentType'));
			
			
			$name = $add->appendChild($xml->createElement('field'));
				$name->appendChild($xml->createCDATASection($content));
					$name->setAttributeNode(new DOMAttr('name','content'));
				unset($name);
		$xml->formatOutput = true; 
		
		//$fullFile = $parent.'\\'.$meta['fileName'].'.xml';
		if (strlen(trim($content))==0){
			$fullFile = $file.' - THIS_IS_EMPTY.xml';
		}else{
			$fullFile = $file.'.xml';
		}
		
		$xml->save($fullFile);
		$str = 'Saved';
		
		exec('java -jar '.$post.' "'.$fullFile.'"');
		$str .= ', uploaded "'.$fullFile.'"';
		$str .= ' @ ' . round((microtime(true) - $time_start),3) . "s\n";
		
		unlink($fullFile);
		return $str;
		
		
		
	
	unset($config,$wrapper,$file,$xml);
	
}


function utf8replacer($captures) {
  if ($captures[1] != "") {
    // Valid byte sequence. Return unmodified.
    return $captures[1];
  }
  elseif ($captures[2] != "") {
    // Invalid byte of the form 10xxxxxx.
    // Encode as 11000010 10xxxxxx.
    return "\xC2".$captures[2];
  }
  else {
    // Invalid byte of the form 11xxxxxx.
    // Encode as 11000011 10xxxxxx.
    return "\xC3".chr(ord($captures[3])-64);
  }
}
//preg_replace_callback($regex, "utf8replacer", $text);

function convert_smart_quotes($string){ 
	$search = array('“','‘','’','”',' '); 
    $replace = array('"',"'","'",'"',' '); 
    return str_replace($search, $replace, $string); 
}

function wordReplace($s){
	$search  = array('&');
	$replace = array('and');
	return str_replace($search,$replace,$s);
}

function getData($data,$class){
	$data = utf8_decode($data);
	$data = preg_replace('/\x{00a0}/','?', $data );
	$dom = new DOMDocument;
	
	$searchPage = mb_convert_encoding($data, 'HTML-ENTITIES', 'iso-8859-1'); 
	$dom -> loadHTML( $searchPage );
	
	$divs = $dom -> getElementsByTagName('div');
	$ary = array();
	foreach ( $divs as $div ){
		
		if ( $div -> hasAttribute('class') && strpos( $div -> getAttribute('class'), $class ) !== false ){
			array_push($ary,preg_replace("/\s+/", " ", $div -> nodeValue));
		}
	}
	return $ary;
}


?>