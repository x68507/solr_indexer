<?php
	$q = 'bom';
	$str = " m2m vs sap bom comparison contents bom comparisons download bom from sap using cs12";
	$regex = '/'.$q.'[^\s]*/i';

	preg_match_all($regex,$str,$match);
	$arr=preg_split("/\s+(?=\S*+$)/",$match[0][0]);
	
	echo "<pre>";
		print_r($match);
		print_r($arr);
		print_r($match[0][0]);
	echo "</pre>";

?>