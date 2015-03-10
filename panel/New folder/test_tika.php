<?php
	require_once(realpath(__DIR__ .'\config.php'));
	$nl = "\n";
	
	$baseDir = realpath(dirname(__FILE__).'/..//'.$baseDir);
	
	//Setup basic PHP classes for using Tika
	$soft = realpath(dirname(__FILE__).'/../Funstaff/vendor/autoload.php');
	require_once($soft);
	
	$test = 3;
	
	if ($test==1){
		$file = 'C:\xampp\htdocs\sap\docs\Training\Laser Safety\A Laser Safety Procedure.docx';
	}elseif($test==2){
		$file = 'C:\xampp\htdocs\sap\docs\Training\Laser Safety\A Laser Safety Procedure.pdf';
	}elseif($test==3){
		$file = 'C:\xampp\htdocs\sap\docs\Training\Laser Safety\A Laser Safety Procedure.pptx';
	}

	use Funstaff\Tika\Configuration;
	use Funstaff\Tika\Document;
	use Funstaff\Tika\Wrapper;
	
	$config = new Configuration($tika);
	$config->setOutputFormat('xml');
	$config->setOutputEncoding('UTF-8');

	$wrapper = new Wrapper($config);
	$wrapper->addDocument(new Document('doc',$file));
	$wrapper->execute();
	
	$document = $wrapper->getDocument('doc');
	$content = $document->getContent();
	
	echo $file.'<hr>';
		echo "<textarea style='width:100%;height:300px'>";
			//$temp = getData($content);
			
			print_r($document->getMetadata());
		echo "</textarea>";
		echo "<div style='width:100%;height:500px;overflow-y:scroll'>";
			
			echo convert_smart_quotes($content);
		echo "</div>";
			//print_r($content);

	echo '<hr>mio';

function convert_smart_quotes($string){ 
	$search = array('“','‘','’','”',' '); 
    $replace = array('"',"'","'",'"',' '); 
    return str_replace($search, $replace, $string); 
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