<?php
	$host = 'localhost';
	$maxAuto = 8;
	$xml = false;

	if ($xml){
		header('Content-Type: text/xml'); 
		echo "<?xml version='1.0' encoding='utf-8'?>";
		echo "<xml>";
	}
	
	
	$q = trim('bom co');
	$regex = '/'.$q.'[^\s]* [^\s]*/i';
	
	$blacklist = array('or','on','and');
	
	preg_replace('("|\')','',$q);
	$q = urlencode($q);
	$hl = '&hl=true&hl.fl=content&hl.simple.pre=%3Cem%3E&hl.simple.post=%3C%2Fem%3E&hl.preserveMulti=true';
	$url = 'http://'.$host.':8983/solr/collection1/select?q=text:"'.$q.'"&fl=id&wt=json&indent=true'.$hl;
	
	$context = stream_context_create(array('http' => array('header' => "Host: $host")));
	$result = file_get_contents($url, 0, $context);
	
	$response = json_decode($result,true);
	
	if ($xml){
		echo "<url><![CDATA[".$url."]]></url>";
	}
	
	
	$ary = array();

	
	$bc = true;
	
	foreach($response['highlighting'] as $key=>$val){
		
		$str = strtolower(strip_tags(implode('',$val['content'])));
		
		$str = str_replace(array('.',',',')','(',':'),'',$str);
		
		preg_match_all($regex,$str,$match);
		$t = 'match: '.json_encode($match);
		
		foreach($match as $v1){
			foreach($v1 as $v2){
				$arr=preg_split("/\s+(?=\S*+$)/",$v2);
				if ($arr[0]!=$arr[1] && strlen($arr[1])>2 && strlen($arr[1])<30 && !in_array($arr[1],$blacklist)){
					if (!array_key_exists($v2,$ary)){
						$ary[$v2] = 0;
					}
					$ary[$v2]++;
				}
			}
		}
		unset($str,$match);
	}
	
	
	unset($val);
	arsort($ary);
	if (count($ary)>0){
		reset($ary);
		$first_key = key($ary);
		$arr=preg_split("/\s+(?=\S*+$)/",$first_key);
		echo $arr[0];
	}
	if (!$xml){
		echo "<pre>";
			echo $t;
			print_r($arr);
			print_r($ary);

		echo "</pre>";
	}
	
	if ($xml){
		$dex = 1;
		foreach($ary as $key=>$val){
			echo "<auto><![CDATA[".$key."]]></auto>";
			$dex++;
			if ($dex>$maxAuto) break;
		}
		echo "</xml>";
	}
	
?>