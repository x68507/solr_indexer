
		<?php
			$file = 'C:\xampp\htdocs\TEMP\sap\docs\SAP\1_SAP General Training\SAP Navigation Training.pdf';
			
				header('Content-Type: text/html; charset=utf-8');
			//Setup basic PHP classes for using Tika
			require_once('Funstaff\vendor\autoload.php');
			use Funstaff\Tika\Configuration;
			use Funstaff\Tika\Document;
			use Funstaff\Tika\Wrapper;
			//use Funstaff\Tika\Metadata;
			$config = new Configuration('c:\tika\tika-app-1.6.jar');
			$config->setOutputFormat('html');
			$config->setOutputEncoding('UTF8');
			
			
			$wrapper = new Wrapper($config);
			$wrapper->addDocument(new Document('doc',$file));
			$wrapper->execute();
			
			$document = $wrapper->getDocument('doc');
			
			$content = $document->getContent();
			$raw     = $document->getRawContent();
			
			$metadata = $document->getMetadata();
			
			
			echo "<pre>";
				print_r($metadata);
				
			echo "</pre>";
			echo "<hr>";
			echo $content;
			echo "<hr>";
			echo "<textarea style='width:100%;' rows=30>";
				$var = getData($raw,'page');
				foreach($var as $val){
					echo $val;
				}
					
			echo "</textarea>";
			
function getData($data,$class){
    $dom = new DOMDocument;
    $dom -> loadHTML( $data );
	$dom->preserveWhiteSpace = false;
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