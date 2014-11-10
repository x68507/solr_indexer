<?php
	libxml_use_internal_errors(true);
	$xml = simplexml_load_file(realpath(dirname(__FILE__).'\config.xml'));
	/*
	if ($xml === false) {
		echo "Failed loading XML\n";
		foreach(libxml_get_errors() as $error) {
			echo "\t", $error->message;
		}
		die;
	}
	*/
		$tika = $xml->tika;
		$post = $xml->post;
		$php  = $xml->php;
		$solr = $xml->solr;
		$baseDir = $xml->baseDir;
		$numRows = $xml->numRows
	
/*	
	//only echos for AJAX requie
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			
			
		}
		*/
?>