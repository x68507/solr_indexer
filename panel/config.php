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
		$pageTitle = $xml->title;
		$tika = $xml->tika;
		$post = $xml->post;
		$php  = $xml->php;
		$solr = $xml->solr;
		$baseDir = $xml->baseDir;
		$numRows = $xml->numRows;
		$schema = $xml->schema;
		$solrconfig = $xml->solrconfig;
?>