<?php
	/*--------------------------------------*/
	/*--USER DEFINED CONSTANTS--------------*/
	/*--------------------------------------*/
	require_once(realpath(__DIR__ .'/../panel/config.php'));
	$rows = $numRows;
	
	/*--------------------------------------*/
	/*--END OF CONSTANT SECTION-------------*/
	/*--------------------------------------*/
	
	if (!isset($_POST) || !isset($_POST['json'])) die;
	
	
	
	header('Content-Type: text/xml'); 
	echo "<?xml version='1.0' encoding='utf-8'?>";
	echo "<xml>";
		require_once('err.php');
		/*
			if the Apache error logs have something about mpm_winnt 150 child processes, then change the httpd.conf file
			   find httpd-mpm.conf with <if_module mpm_winnt> and change child processes to 25
		*/
		
		$time_start = microtime(true);
		$json = json_decode($_POST['json']);
		
		
		
		$term = $json->{'term'};
			
		
		
		
		$start = $json->{'offset'}*$rows;
		
		$keyword = array('lastModifed','creator','fileName');
		$host = $_SERVER['SERVER_NAME'];
		$sub = '';
		
		if (isset($json->{'folders'})){
			$sub = ' AND (baseDir:"'.implode((count($json->{'folders'})==1?'':'" OR baseDir:"'),$json->{'folders'}).'")';
			$sub = urlencode($sub);
		}
		
		//exact search
		
		$re_dq = '/"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"/s';
		$b = preg_match($re_dq,$term,$match); 
		
		echo "<dub><![CDATA[".implode(' | ',$match)."]]></dub>";
		$hl = '&hl=true&hl.fl=*&hl.simple.pre=%3Cem%3E&hl.simple.post=%3C/em%3E&hl.snippets=20';
		
		if ($b){
			$q = urlencode('text_x:'.$match[0]);
		}elseif (strposa($term,$keyword)){
			$type = 'advanced';
		}else{
			$q = 'text:'.urlencode($term).'~200';
		}
		$url = 'http://'.$host.':8983/solr/collection1/select?q='.$q.$sub.'&start='.$start.'&rows='.$rows.'&wt=json&fl=creator%2Ctitle%2CfileName%2ClastModified%2CpageCount%2CbaseDir'.$hl;
		//https://wiki.apache.org/solr/SolrRelevancyFAQ
		//https://wiki.apache.org/solr/AnalyzersTokenizersTokenFilters#WordDelimiterFilter
		//http://localhost:8983/solr/collection1/select?q=text_x%3A%22production+orders%22&wt=xml&indent=true&hl=true&hl.fl=*&hl.simple.pre=%3Cem%3E&hl.simple.post=%3C/em%3E&hl.snippets=20&fl=creator,title,fileName,lastModified,pageCount,baseDir
		//http://localhost:8983/solr/collection1/select?q=text_x%3A%22production+order%22&start=0&rows=25&wt=json&fl=creator%2Ctitle%2CfileName%2ClastModified%2CpageCount%2CbaseDir
		/*most likely needs to use highlighting in order to find the index of a multivalued query*/
		
		if (strlen($url)==0) die;
		
		echo "<url><![CDATA[".$url."]]></url>";
		
		$context = stream_context_create(array('http' => array('header' => "Host: $host")));
		$result = file_get_contents($url, 0, $context);
		$response = json_decode($result,true);
			
			
			$tots = $response['response']['numFound'];
			$e = $start+$rows;
			
			echo "<start>".($tots==0?0:$start+1)."</start>";
			echo "<end>".($tots<$e?$tots:$e)."</end>";
			echo "<pages>".ceil($tots/$numRows)."</pages>";
			echo "<curPage>".$json->{'offset'}."</curPage>";
			echo "<total>".$tots."</total>";
			
			foreach($response['response']['docs'] as $val){
				echo "<file>";
					
					
					echo "<title><![CDATA[".$val['title'][0]."]]></title>";
					
					echo "<lastModified>$val[lastModified]</lastModified>";
					echo "<creator><![CDATA[".$val['creator']."]]></creator>";
					echo "<baseDir>$val[baseDir]</baseDir>";
					echo "<fileName>$val[fileName]</fileName>";
					echo "<needle>$term</needle>";
					
					//echo "<haystack><![CDATA[".$val['content'][0]."]]></haystack>";
					echo "<pageCount>$val[pageCount]</pageCount>";
					
				echo "</file>";
			}
		
		echo "<time>".round((microtime(true) - $time_start),2)."</time>";
		
	echo "</xml>";
	
	
	

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

?>