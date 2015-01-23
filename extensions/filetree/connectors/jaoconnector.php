<?php 
//header('Content-Type: application/json');
require_once(realpath(__DIR__ .'\..\..\..\panel\config.php'));

$dir='/';
if(!empty($_GET['dir'])){
	$dir = $_GET['dir'];
	if($dir[0]=='/'){$dir = '.'.$dir.'/';}
}

$dir = str_replace('..', '', $dir);


//$root = dirname(__FILE__).'/../../../docs/SAP/';
$b = __DIR__ .'/../../..';
$c = realpath($b .'/panel/config.php');

require_once($c);

//echo $c.'<hr>';
$root = realpath($b.$baseDir);

$return = $dirs = $fi = array();



if( file_exists($root . $dir) ) {
	
	$files = scandir($root . $dir);
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		// All dirs
		foreach( $files as $file ) {
			if ($file != '.' && $file != '..'){
				$file = utf8_encode($file);
				$fileExists = file_exists($root . $dir . iconv('utf-8', 'cp1252', $file));
				$isDir = is_dir($root . $dir . iconv('utf-8', 'cp1252', $file));
			
			
				if( $fileExists && $isDir) {
					$dirs[] = array('type'=>'dir','dir'=>$dir,'file'=>$file);
				}elseif( $fileExists && !$isDir) {
					if (in_array(getExt($file),$ext)){
						$fi[] = array('type'=>'file','dir'=>$dir,'file'=>$file,'ext'=>strtolower(getExt($file)));
					}
					
				}
				
			}
		}
		$return = array_merge($dirs,$fi);
	}
}
echo json_encode($return,JSON_UNESCAPED_UNICODE);


function getExt($file){
	$dot = strrpos($file, '.') + 1;
        return substr($file, $dot);
}
?>
