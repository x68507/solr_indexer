<?php
	libxml_use_internal_errors(true);
	$xml = simplexml_load_file(realpath(dirname(__FILE__).'\config.xml'));

		$pageTitle	= $xml->title;
		$tika		= $xml->tika;
		$post		= $xml->post;
		$php		= $xml->php;
		$solr		= $xml->solr;
		$baseDir	= $xml->baseDir;
		$numRows	= $xml->numRows;
		$schema		= $xml->schema;
		$jetty		= $xml->jetty;
		$remote_ip	= (isset($xml->remote_ip)?$xml->remote_ip:'localhost');
?>