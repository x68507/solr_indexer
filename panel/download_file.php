<?php
	
	
	
	
	$file = realpath($_GET['f']);
	
	
	
	//$file = realpath(__DIR__ .$_GET['f']);
	if (file_exists($file)) {
		$file_name = $file;
		header("Pragma: public"); 
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		//header("Content-Type: ".$row['Type']); 
		header("Content-Disposition: attachment; filename=\"".basename($file_name)."\";" ); 
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file));
		ob_clean();
		flush();

		readfile( $file); 
		exit();
	}else{
		
		echo "<pre>".$_GET['f'].'</pre>';
		echo 'File Not Found';
	}
?>