<?php
	session_start();
	if (!isset($_SESSION['uid']) || $_SESSION['uid']!=true){
		header('Location: index.php');
	}

?>
<html>
	<head>
		<script type='text/javascript' src='../extensions/jQuery_v1.11.1.js'></script>
		<!--<script id='jscript' type='text/javascript' src='jscript.js'></script>-->
		<link type='text/css' rel='stylesheet' href='stylesheet.css'/>
		<!--[if IE 8]>
			<script src="../extensions/json3.js"></script>
		<![endif]-->
		<script type='text/javascript' src='dropzone.js'></script>
		<script type='text/javascript' src='jscript.js'></script>
		
	</head>
	
	<body>
		
		<!--
		<input class='disabled' type='text' id='base' disabled='disabled' value='<?php $m = fopen('base.txt','r');echo fread($m,filesize('base.txt'));fclose($m); ?>'>
		<input type='button' value='Unlock' onclick='enable_base()'>
		-->
		
		<div class='container-links'>
			<div class='links'>
				<a href='#' onclick='server_start()'>Start Server</a>
			</div>
			<div class='links'>
				<a href='#' onclick='refresh_index()'>Refresh Index</a>
			</div>
			<div class='links'>
				<a href='#' onclick='server_stop()'>Stop Server</a>
			</div>
			<div class='links h'>
				<a href='#' onclick='delete_entries()'>Delete Entries</a>
			</div>
			<hr>
			<div class='links'>
				<?php
					require_once(realpath(__DIR__ .'/config.php'));
					$xml = simplexml_load_file($jetty);

					//checking to see if XML node is present in schema file
					foreach($xml->Call->Arg->{'New'}->Set as $item){
						if ((string)$item['name']=='host'){
							echo "<input type='text' id='secure_ip' value='".$item->SystemProperty[0]['default']."'>";
						}
					}
				?>
				<a href='#' onclick='secure_ip()'>Secure IP</a>
			</div>
			<hr>
			<div class='links'>
				<a href='..' target='_blank'>Launch App</a>
			</div>
			<div class='links h'>
				<a href='#' onclick='schema()'>Write Schema</a>
			</div>
			<div class='links'>
				<a href='#' onclick='clear_console()'>Clear Console</a>
			</div>
		</div>
		<pre id='console'>
			
		</pre>
		<div id='container-folders'>
			<div id='header-folders'>
				
				<!--<input type='Button' value='New File'>-->
				<div id='header-form'>
					<form id='uploadForm' method="post" action="upload.php" enctype="multipart/form-data">
						<input type="checkbox" name='scan' value='yes' id='scan'>Update Solr
						<input class='h' type="file" id="upload" name="uploadFile" style="width: 88px; opacity:0.0; filter:alpha(opacity=0); " onchange='uploadChange()'/>
						<input class='h' type="submit" id="submit" name="submit" value='New File' style="margin-left: -88px"/>
						<input type='Button' value='New Directory' onclick='new_directory()'>
						<input type='Button' value='Delete Selected' onclick='del()'>
						<input type='button' value='Rename' onclick='rename()'>
					</form>
				</div>
			</div>
			<div id='curDir'></div>
			<div id='folders'></div>
		</div>
		<div id='logout'>
			<a href='#' onclick='logout()'>Logout</a>
		</div>
	</body>
	
</html>