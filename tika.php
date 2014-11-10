<html>
	<head>
		<!--
		<script type='text/javascript' src='extensions/jQuery.js'></script>
		<script>
			setTimeout(function(){
				$(document).scrollTop($(document).height());
			},'200');
		</script>
		-->
	</head>
	<body>
		<h4>Delete All Entries</h4>
		<a href="http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E*:*%3C/query%3E%3C/delete%3E" target="_blank">Delete Entries</a>
		<h4>ToDo</h4>
		<ul>
			<li>https://wiki.apache.org/tika/TikaOCR</li>
		</ul>
		<h4>Basic Queries</h4>
		<ul>
			<li><a href="http://localhost:8983/solr/collection1/select?q=lastModified:[2014-10-08T00:00:00Z%20TO%20*]&wt=json&indent=true" target="basicQueries">Date Modified</a></li>
			<li><a href='http://localhost:8983/solr/collection1/select?q=text:"navigation training"&wt=json&indent=true' target="basicQueries">Contains</a></li>
			<li><a href='http://localhost:8983/solr/collection1/select?q=-text:"SAP"&wt=json&indent=true' target="basicQueries">Does Not Contain</a></li>
		</ul>
		<?php
			$baseDir = $_SERVER['DOCUMENT_ROOT'].'/tika/docs';
			echo '<h4>XML Files</h4>';
			if (isset($_GET['parent'])){
				$parent = urldecode($_GET['parent']);
			}else{
				die('<input type="file" id="ctrl" webkitdirectory directory/><input type="button" value="Click" onClick="upload()"><script>function upload(){console.log(document.getElementById(ctrl))}</script>');
			}
			
			
			//require_once('err.php');
			
			/*
			DELETE SOLR
			http://localhost:8983/solr/update?stream.body=<delete><query>*:*</query></delete>
			ADD TO SOLR
				cd c:\tika\solr\bin
				solr.cmd start
				cd ..\example\exampledocs\sap
				java -jar ..\post.jar *.xml
			*/
			$time_start = microtime(true);
			
			//Setup basic PHP classes for using Tika
			require_once('Funstaff\vendor\autoload.php');
			use Funstaff\Tika\Configuration;
			use Funstaff\Tika\Document;
			use Funstaff\Tika\Wrapper;
			//use Funstaff\Tika\Metadata;
			$config = new Configuration('c:\tika\tika-app-1.6.jar');
			$config->setOutputFormat('html');
			$config->setOutputEncoding('UTF8');
			
			//Defines parent directory we want to loop through (same directory where XML files will be placed)
			
	
			//$parent = 'C:\Users\mhumphreys\Documents\SAP\1_SAP General Training';
			
			//Begin iterator of the parent directory
			$ext = array('pdf','doc','docx','ppt','pptx');
			$di = new RecursiveDirectoryIterator($parent);
			$dex = 1;
			
			//Setup buffer for flushing during loop
			if (ob_get_level() == 0) ob_start();
			echo "<pre>";
			echo 'Starting... ' . '<br>';
			foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
				$path_info = pathinfo($file);
				//checks for valid extension sytpe
				if (in_array($path_info['extension'],$ext)){
					echo $dex.': Trying: ' . $path_info['filename'].'<br>';
					ob_flush();
					flush();
					$dex++;
					$wrapper = new Wrapper($config);
					$wrapper->addDocument(new Document('doc',$file));
					$wrapper->execute();
					
					$document = $wrapper->getDocument('doc');
					
					//$content = $document->getContent();
					$content = $document->getRawContent();
					
					$content = htmlentities($content, ENT_SUBSTITUTE, 'UTF-8');
					$content = utf8_encode($content);
					//$content = html_entity_decode($content);
					
					$content = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
 '|[\x00-\x7F][\x80-\xBF]+'.
 '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
 '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
 '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
 '', $content );
 
//reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
$content = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
 '|\xED[\xA0-\xBF][\x80-\xBF]/S','', $content );
 
 //$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
					/*
					$content = iconv("UTF-8", "ISO-8859-1//IGNORE", $content);
					$content = trim(strip_tags($content));
					$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
					*/
					$content = preg_replace( "/\r|\n/", "", $content );
					$content = preg_replace("/\]\]\>/","",$content);
					$content = preg_replace('/\s+/', ' ',$content);
					
					$content = html_entity_decode($content);
					
					$metadata = $document->getMetadata();
					
					$type = $metadata->get('Content-Type');
					$meta = array(
						'md5'			=> md5_file($file)
						,'contentType'	=> $type
						,'baseDir'		=> substr(dirname($file),strlen($baseDir))
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
					
					$ms = array(
						'application/vnd.openxmlformats-officedocument.presentationml.presentation'	=> true,
						'application/vnd.openxmlformats-officedocument.wordprocessingml.document'	=> true,
						'application/msword'	=> true
					);
					$pdf = array('application/pdf'	=> true);
					
					if (isset($ms[$type])){
						if ($metadata->get('Application-Name')=='Microsoft Office PowerPoint'){
							$meta['pageCount']	= $metadata->get('Slide-Count');
						}elseif($metadata->get('Application-Name')=='Microsoft Office Word'){
							$meta['pageCount']	= $metadata->get('Page-Count');
						}
					}elseif (isset($pdf[$type])){
						$meta['pageCount']	= $metadata->get('xmpTPg:NPages');
					}else{
						//die('Did not parse correctly');
					}
					
					
					/*----------------------------------------------------------*/
					/*----Begin XML Creation------------------------------------*/
					/*----------------------------------------------------------*/
					if (1==1){
						//header('Content-Type: text/xml'); 
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
							
							/*
							$name = $add->appendChild($xml->createElement('field')); 
							$name->appendChild($xml->createCDATASection($content)); 
								$name->setAttributeNode(new DOMAttr('name','content'));
							*/
							
							
							foreach($aryContent as $val){
								$name = $add->appendChild($xml->createElement('field')); 
								$name->appendChild($xml->createCDATASection($val)); 
									$name->setAttributeNode(new DOMAttr('name','content'));
								unset($name);
							}
							
							
						$xml->formatOutput = true; 
						//echo $xml->saveXML();
						
						$xml->save($parent.'\\'.$meta['fileName'].'.xml');
						//Echoing & flushing during loop
						echo 'Saved @ ' . round((microtime(true) - $time_start),3) . 's<br>';
						ob_flush();
						flush();
					}else{
						//header( 'Content-type: text/html; charset=utf-8' );
						echo $content;
					}
					/*----------------------------------------------------------*/
					/*----End XML-----------------------------------------------*/
					/*----------------------------------------------------------*/
					
					
					//deletes currently used variables
					unset($file,$xml);
				}
			}
			
			echo 'Completed!!!';
			echo "</pre>";
			echo mb_internal_encoding();
			ob_end_flush();
			
			function getData($data,$class){
				$dom = new DOMDocument;
				$searchPage = mb_convert_encoding($data, 'HTML-ENTITIES', "UTF-8"); 
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
	</body>
</html>