<!--<html manifest="manifest.manifest">-->
<html>
	<head>
		<title>SAP Manuals</title>
		<link rel="shortcut icon" href="images/sap.png">
		
		<script type='text/javascript' src='extensions/jQuery_v1.11.1.js'></script>
		<!--Filetree-->
		<!--
		<script type='text/javascript' src='extensions/filetree/jaofiletree.js'></script>
		<link type='text/css' rel='stylesheet' href='extensions/filetree/jaofiletree.css'/>
		-->
		<!--Custom-->
		<!--
		<script type='text/javascript' src='jscript.js'></script>
		<link type='text/css' rel='stylesheet' href='stylesheet.css?v=2'/>
		-->
		
	</head>
	<body>
		<div id='jao'></div>
		<div id='container-search' class='_h'>
			<div id='search-header'>
				<input type='text' id='search-box' placeholder='Search Manuals'><input id='search-button' class='hover' type='button' value='Search'><div id='search-answer'></div>
			</div>
			<div id='search-results'>
				<div id='search-header'>
					<div class='sr-div'><div class='sr-title hover asc'>Title</div><div class='sr-file hover asc'>Folder</div><div class='sr-count hover asc numeric'>Count</div><div class='sr-pages hover asc numeric'>Pages</div><div class='sr-lm hover asc'>Last Modified</div></div>
				</div>
				<div id='search-body'></div>
			</div>
		</div>
		<div id='container-pdf' class='h'>
			<div id='iframe-urltext' class='ellipsis'></div>
			<iframe id='viewer' src='' width='600' height='450' allowfullscreen webkitallowfullscreen></iframe>
		</div>
		<div id='console' class='h'></div>
		
	</body>
</html>