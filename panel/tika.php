<?php
	require_once(realpath(__DIR__ .'\config.php'));
	require_once(realpath(__DIR__ .'\tika_file.php'));
	
	if (defined('STDIN')) {
		$parent = $argv[1];
	}
	
	$time_start = microtime(true);
	$di = new RecursiveDirectoryIterator($parent);
	$dex = 1;
	//$tika = 'c:\tika\tika-app-1.6.jar';
	echo $tika."\n";
	echo 'Starting... ' . "\n";
	
	if (ob_get_level() == 0) ob_start();
	
	foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
		$path_info = pathinfo($file);
		if (in_array($path_info['extension'],$ext)){
			echo $dex.': Trying: ' . $path_info['filename']."\n";
			ob_flush();
			flush();
			$dex++;
			//calls the actual TIKA parser .php file
			//echo $file.'<br>';
			echo tika($file);
		}
	}
	ob_end_flush();
	echo 'Completed!!!';
?>