<?php
	require_once(realpath(__DIR__ .'\config.php'));
	require_once(realpath(__DIR__ .'\tika_file.php'));
	
	if (defined('STDIN')) {
		$parent = $argv[1];
	}
	
	$protected = array('%');
	
	$time_start = microtime(true);
	$di = new RecursiveDirectoryIterator($parent);
	$dex = 1;
	//$tika = 'c:\tika\tika-app-1.6.jar';
	echo $tika."\n";
	echo 'Starting... ' . "\n";
	
	if (ob_get_level() == 0) ob_start();
	
	foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
		//Checks to make sure filename is valid ASCII
		$path_info = pathinfo($file);
		if (in_array($path_info['extension'],$ext)){
			echo $dex.': Trying: ' . $path_info['filename']."\n";
			if (strposa($file, $protected, 1)) {
				rename($file,str_replace($protected,'',$file));
				$file = str_replace($protected,'',$file);
				echo "   ***renamed file***\n";
			}
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
function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}
?>