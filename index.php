<!--<html manifest="manifest.manifest">-->
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<title>SAP Manuals</title>
		<link rel="shortcut icon" href="images/sap.png">
		
		<script type='text/javascript' src='extensions/jQuery_v1.11.1.js'></script>
		<!--Filetree-->
		<script type='text/javascript' src='extensions/filetree/jaofiletree.js'></script>
		<link type='text/css' rel='stylesheet' href='extensions/filetree/jaofiletree.css'/>
		
		<!--[if IE 8]>
			<script src="extensions/json3.js"></script>
		<![endif]-->

		
		<!--Custom-->
		<script id='jscript' type='text/javascript' src='jscript.js'></script>
		<link type='text/css' rel='stylesheet' href='stylesheet.css?v=2'/>
		
	</head>
	<body>
		<div id='jao'></div>
		<div id='container-search'>
			<div id='search-header'>
				<input type='text' id='search-box' placeholder='Search Manuals'><input id='search-button' class='hover blue btr bbr' type='button' value='Search'><div id='search-answer'></div>
			</div>
			<div id='search-results'>
				<div id='search-time'>&nbsp;</div>
				<div id='search-header'>
					<div class='sr-div'><div class='sr-title hover asc'>Title</div><div class='sr-file hover asc'>Folder</div><div class='sr-count hover asc numeric'>&nbsp;</div><div class='sr-pages hover asc numeric'>Pages</div><div class='sr-lm hover asc'>Last Modified</div></div>
				</div>
				<div id='search-body'>
				<!--
				<form method="get" action="somephp.php">
					<input type="file" id="upload" name="upload" style="width: 88px; opacity:0.0; filter:alpha(opacity=0); " onchange='uploadChange()'/>
					<input type="submit" name="submit" style="margin-left: -88px"/>
				</form>
				-->
				</div>
				
			</div>
			<div id='search-footer' class='h'>
					<div id='search-left' class='hover blue btl bbl'><</div>
					<div id='search-pages'>
						
					</div>
					<div id='search-right' class='hover blue btr bbr'>></div>
				</div>
			
		</div>
		<div id='console' class='h'></div>
		
	</body>
</html>