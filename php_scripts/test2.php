<?php

	$regex = '/proces[^\s]*/i';
	
	$str = '5 reliance process';
	
	preg_match_all($regex,$str,$match);
	
	echo "<pre>";
		print_r($match);
	echo "</pre>";
	







?>