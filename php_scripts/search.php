<?php
	/*--------------------------------------*/
	/*--USER DEFINED CONSTANTS--------------*/
	/*--------------------------------------*/
	require_once(realpath(__DIR__ .'/../panel/config.php'));
	
	$maxAuto = 10;
	/*--------------------------------------*/
	/*--END OF CONSTANT SECTION-------------*/
	/*--------------------------------------*/
	
	if (!isset($_POST) || !isset($_POST['json'])) die;
	
	//$host = $_SERVER['SERVER_NAME'];
	$host = $remote_ip;

	header('Content-Type: text/xml'); 
	echo "<?xml version='1.0' encoding='utf-8'?>";
	echo "<xml>";
		require_once('err.php');
		switch($_POST['action']){
			case 'auto':
				
				$maxAuto = 10;
				$json = json_decode($_POST['json']);
				
				$q = trim($json->{'val'});
				$sub = '';
				$tempFolder = '';
				if (isset($json->{'folders'})){
					$tempFolder = implode(' || ',($json->{'folders'}));
					$sub = ' AND (baseDir:"'.solrEscape(implode((count($json->{'folders'})==1?'':'" OR baseDir:"'),($json->{'folders'}))).'")';
					$sub = urlencode($sub);
				}
				
				//$regex = '/'.$q.'[^\s]* [^\s]*/i';
				$regex = '/'.$q.'[^\s]*/i';
				
				$blacklist = array('or','on','and');
				preg_replace('("|\')','',$q);
				$q = urlencode($q);
				//$hl = '&hl=true&hl.fl=content&hl.simple.pre=%3Cem%3E&hl.simple.post=%3C%2Fem%3E&hl.preserveMulti=true';
				$hl = '&hl=true&hl.fl=content&hl.preserveMulti=true';
				$url = 'http://'.$host.':8983/solr/collection1/select?q=text:"'.$q.'"'.$sub.'&fl=id&wt=json&indent=true'.$hl;
				
				
				
				$context = stream_context_create(array('http' => array('header' => "Host: $host")));
				$result = file_get_contents($url, 0, $context);
				
				echo "<result><![CDATA[".$result."]]></result>";
				$response = json_decode($result,true);
				
				if ($xml){
					echo "<url><![CDATA[".$url."]]></url>";
					//echo "<url>".$url."</url>";
				}
				
				
				$ary = array();

				
				$bc = true;

				$d = 0;
				foreach($response['highlighting'] as $key=>$val){
					$str = strtolower(strip_tags(implode('',$val['content'])));
					echo "<test><![CDATA[".json_encode($str)."]]></test>";
					$str = str_replace(array('.',',',')','(',':',';','?','!'),' ',$str);
					$str = preg_replace('!\s+!', ' ', $str);
					//$t = $str;
					$t = 'first';
					preg_match_all($regex,$str,$match);
					$t = $regex. ' || '.$str;
					
					$t0 = 'match: '.json_encode($match);
					
					echo "<test1><![CDATA[".strtolower(strip_tags(implode('',$val['content'])))."]]></test1>";
					
					if (isset($match[0][0])){
						
						$v2 = $match[0][0];
						$arr = preg_split("/\s+(?=\S*+$)/",$v2);
						
						if (isset($arr[1])){
							//legacy code before implementing folders
							if ($arr[0]!=$arr[1] && strlen($arr[1])>2 && strlen($arr[1])<30 && !in_array($arr[1],$blacklist)){
								if (!array_key_exists($v2,$ary)){
									$ary[$v2] = 0;
								}
								$ary[$v2]++;
							}
						}else{
							if (strlen($arr[0])>2 && strlen($arr[0])<30 && !in_array($arr[0],$blacklist)){
								if (!array_key_exists($v2,$ary)){
									$ary[$v2] = 0;
								}
								$ary[$v2]++;
							}
						}
						
					}
					

					
					/*
					foreach($match as $v1){
						//$t = implode(' ... ',$v1);
						
						foreach($v1 as $v2){
							
							$arr=preg_split("/\s+(?=\S*+$)/",$v2);
							//$t = $arr[0];
							if (isset($arr[1])){
								//legacy code before implementing folders
								if ($arr[0]!=$arr[1] && strlen($arr[1])>2 && strlen($arr[1])<30 && !in_array($arr[1],$blacklist)){
									if (!array_key_exists($v2,$ary)){
										$ary[$v2] = 0;
									}
									$ary[$v2]++;
								}
							}else{
								if (strlen($arr[0])>2 && strlen($arr[0])<30 && !in_array($arr[0],$blacklist)){
									if (!array_key_exists($v2,$ary)){
										$ary[$v2] = 0;
									}
									$ary[$v2]++;
								}
							}
						}
					}
					
					$d++;
					*/
					unset($str,$match,$arr);
				}
				unset($val);
				arsort($ary);
				
				echo "<count>".count($ary)."</count>";
				echo "<t>".$t."</t>";
				

				$dex = 1;
				/*
				if (count($ary)>0){
					reset($ary);
					$first_key = key($ary);
					$arr = preg_split("/\s+(?=\S*+$)/",$first_key);
					echo "<main><![CDATA[".$arr[0]."]]></main>";
				}
				*/
				
				//echo "<main>".$first_key."</main>";
				//echo "<auto>test</auto>";
				foreach($ary as $key=>$val){
					if (mb_detect_encoding($key)=='ASCII'){
							echo "<auto><![CDATA[".$key."]]></auto>";
					}
					$dex++;
					if ($dex>$maxAuto) break;
				}

				
				break;
			case 'search':
				/*
					if the Apache error logs have something about mpm_winnt 150 child processes, then change the httpd.conf file
					   find httpd-mpm.conf with <if_module mpm_winnt> and change child processes to 25
				*/
				
				$time_start = microtime(true);
				
				//Gets all the data passed to the server
				$json = json_decode($_POST['json']);
			
				//Main search term; SOLR is setup to not require a field for this term (searches the content of a file)
				$q = '';
				$x = array();
				if (isset($json->{'term'})){
					$term = $json->{'term'};
					$enclose = ($json->{'auto'}=='true'?'"':'');
					$q = $term;
					array_push($x,$enclose.$term.$enclose);
				}
				
				//Default params for building the URL
				$start = '&start='.$json->{'offset'}*$numRows;
				$rows = '&rows='.$numRows;
				
				$fl = '&fl=creator%2Ctitle%2CfileName%2ClastModified%2CpageCount%2CbaseDir';
				
				//Generates information regarding searching in a subdirectory
				$sub = '';
				$tempFolder = '';
				if (isset($json->{'folders'})){
					$tempFolder = implode(' || ',($json->{'folders'}));
					//http://localhost:8983/solr/collection1/select?q=a+AND+%28baseDir%3A%22%5C%5C1+SAP+General+Training%22%29&start=0&rows=25&wt=json&indent=true
					$sub = ' AND (baseDir:"'.solrEscape(implode((count($json->{'folders'})==1?'':'" OR baseDir:"'),($json->{'folders'}))).'")';
					$sub = urlencode($sub);
				}
				
				//Generates extra information for advanced searching
				if (isset($json->{'creator'})){
					$enclose = (substr_count($json->{'creator'},' ')>0?'"':'');
					array_push($x,'creator:'.$enclose.$json->{'creator'}.$enclose);
				}
				if (isset($json->{'title'})){
					$enclose = (substr_count($json->{'title'},' ')>0?'"':'');
					array_push($x,'fileName:'.$enclose.$json->{'title'}.$enclose);
				}
				if (isset($json->{'page'})){
					$p = $json->{'page'};
					$p2 = (isset($json->{'page2'})?$json->{'page2'}:$p);
					$op = $json->{'page-op'};
					array_push($x,'pageCount:['.($op=='lt'?'*':$p).' TO '.($op=='gt'?'*':$p2).']');
				}
				$op = '';
				$temp = '';
				if (isset($json->{'date'})){
					$d = $json->{'date'};
					$d2 = (isset($json->{'date2'})?$json->{'date2'}:$d);
					$op = $json->{'date-op'};
					$temp = 'lastModified:['.($op=='lt'?'*':$d.'T00:00:00Z').' TO '.($op=='gt'?'*':$d2.'T23:59:59Z').']';
					array_push($x,$temp);
				}
				$sort = '';
				if (isset($json->{'sort'})){
					$sort = '&sort='.$json->{'sort'}.'%20'.$json->{'sortDir'};
				}
				
				echo "<op>$op</op>";
				
				echo "<temp><![CDATA[".solrEscape($tempFolder)."]]></temp>";
				$q = urlencode(implode(' AND ',$x));
				
				
				
				$url = 'http://'.$host.':8983/solr/collection1/select?q='.$q.$sub.$start.$rows.'&wt=json&indent=true'.$sort;

				if (strlen($url)==0) die;
				
				
				echo "<url><![CDATA[".$url."]]></url>";
				
				$context = stream_context_create(array('http' => array('header' => "Host: $host")));
				$result = file_get_contents($url, 0, $context);
				$response = json_decode($result,true);
					
				
					$tots = $response['response']['numFound'];
					$e = $json->{'offset'}*$numRows+$numRows;
					
					echo "<start>".($tots==0?0:$json->{'offset'}*$numRows+1)."</start>";
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
							
							echo "<fileName><![CDATA[".$val['fileName']."]]></fileName>";
							
							//echo "<needle>$term</needle>";
							
							//echo "<haystack><![CDATA[".$val['content'][0]."]]></haystack>";
							echo "<pageCount>$val[pageCount]</pageCount>";
							
						echo "</file>";
					}
				
				echo "<time>".round((microtime(true) - $time_start),2)."</time>";
				
				
				break;
		}
	echo "</xml>";
	
function solrEscape($str){
	//[\+-&&!(){}[]^~]
	
	
	//$regex = "/[A-Za-z]oo\b/";
	$regex = '`/`';
	$replace = '\\\\\\';
	$str = preg_replace($regex, $replace, $str);
	
	return $str;
}
	

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

?>