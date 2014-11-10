<?php
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
	
	
	$ext = array('pdf');
	if (!isset($time_start)){
		$time_start = microtime(true);
	}
	
	if (!isset($file)){
		
	}

function tika($file){
	
	global $ext,$tika,$post,$baseDir,$time_start,$parent;
	
	
	$config = new Configuration($tika);
	$config->setOutputFormat('html');
	$config->setOutputEncoding('UTF8');

	$wrapper = new Wrapper($config);
	$wrapper->addDocument(new Document('doc',$file));
	$wrapper->execute();
	
		
		$document = $wrapper->getDocument('doc');
		$content = $document->getRawContent();
		$content = html_entity_decode($content);
		$content = utf8_encode($content);
		$content = preg_replace( "/\r|\n/", "", $content );
		$content = preg_replace("/\]\]\>/","",$content);
		$content = preg_replace('/\s+/', ' ',$content);
		$content = str_replace(array('&nbsp;'),' ',$content);
		
		
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
			$creator = $add->appendChild($xml->createElement('field', $meta['creator']));
				$creator->setAttributeNode(new DOMAttr('name','creator'));
			$title = $add->appendChild($xml->createElement('field', htmlentities($meta['title'])));
				$title->setAttributeNode(new DOMAttr('name','title'));
			$fileName = $add->appendChild($xml->createElement('field', $meta['fileName']));
				$fileName->setAttributeNode(new DOMAttr('name','fileName'));
			$lastModified = $add->appendChild($xml->createElement('field', $meta['lastModified']));
				$lastModified->setAttributeNode(new DOMAttr('name','lastModified'));
			$pageCount = $add->appendChild($xml->createElement('field', $meta['pageCount']));
				$pageCount->setAttributeNode(new DOMAttr('name','pageCount'));
			$contentType = $add->appendChild($xml->createElement('field', $meta['contentType']));
				$contentType->setAttributeNode(new DOMAttr('name','contentType'));
			$bd = $add->appendChild($xml->createElement('field', $meta['baseDir']));
				$bd->setAttributeNode(new DOMAttr('name','baseDir'));
			
			$aryContent = getData($content,'page');
			//echo $content;
			
			foreach($aryContent as $val){
				$name = $add->appendChild($xml->createElement('field')); 
				$name->appendChild($xml->createCDATASection($val)); 
					$name->setAttributeNode(new DOMAttr('name','content'));
				unset($name);
			}

			
		$xml->formatOutput = true; 
		
		//$fullFile = $parent.'\\'.$meta['fileName'].'.xml';
		$fullFile = $file.'.xml';
		
		$xml->save($fullFile);
		$str = 'Saved';
		exec('java -jar '.$post.' "'.$fullFile.'"');
		$str .= ', uploaded "'.$fullFile.'"';
		$str .= ' @ ' . round((microtime(true) - $time_start),3) . "s\n";
		return $str;
		
		//unlink($fullFile);
		
	
	unset($config,$wrapper,$file,$xml);
	
}

function getData($data,$class){
	$data = utf8_decode($data);
	$data = preg_replace('/\x{00a0}/','?', $data );
	$dom = new DOMDocument;
	
	$searchPage = mb_convert_encoding($data, 'HTML-ENTITIES', 'iso-8859-1'); 
	$dom -> loadHTML( $searchPage );
	
	$divs = $dom -> getElementsByTagName('div');
	$loop = 5;
	$ary = array();
	foreach ( $divs as $div ){
		
		if ( $div -> hasAttribute('class') && strpos( $div -> getAttribute('class'), $class ) !== false ){
			array_push($ary,preg_replace("/\s+/", " ", $div -> nodeValue));
		}
	}
	return $ary;
}


?>