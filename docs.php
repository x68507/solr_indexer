<!--<html manifest="manifest.manifest">-->
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<title>
			<?php
				require_once(realpath(__DIR__ .'/panel/config.php'));
				echo $pageTitle;
			?>
		
		</title>
		<link rel="shortcut icon" href="images/doc.png">
		
		<script type='text/javascript' src='extensions/jQuery_v1.11.1.js'></script>
		<!--Filetree-->
		<script type='text/javascript' src='extensions/filetree/jaofiletree.js'></script>
		<link type='text/css' rel='stylesheet' href='extensions/filetree/jaofiletree.css'/>
		
		<!--jQuery UI-->
		<script type='text/javascript' src='extensions/jquery-ui-1.11.2/jquery-ui.min.js'></script>
		<link type='text/css' rel='stylesheet' href='extensions/jquery-ui-1.11.2/jquery-ui.css'/>
		
		<!--[if IE 8]>
			<script src="extensions/json3.js"></script>
		<![endif]-->
		
		<!--Custom-->
		<script id='jscript' type='text/javascript' src='jscript.js?v=4'></script>
		<link type='text/css' rel='stylesheet' href='stylesheet.css?v=4'/>
		
		<script type='text/javascript' src='common.js'></script>
	</head>
	<body>
		<div id='container'>
			<div id='sidr'>
				<div id='jao'></div>
			</div>
			<div id='right'>
				<div id='container-search'>
					<div id='search-head'>
						<div id='border-menu' class='border-menu'></div>
						<input type='text' id='search-box' placeholder='' class='sai si'>
						<input type='text' id='search-bg' value=''>
						<div id='search-dd' class='hover nus'>&nbsp;</div>
						<input id='search-button' class='hover bb blue btr bbr bbs' type='button' value='Search'><div id='search-answer'>
					</div>
						<div id='search-x' class='h'>
							<div id='search-xx' class='hover'>&#10006;</div>
							<div id='search-advanced'>
								<table id='sat'>
									<tr><td>Creator</td><td><div class='sai-ic'><input type='text' class='sai si full' id='s-creator'></div></td><td></td></tr>
									<tr><td>Title</td><td ><div class='sai-ic'><input type='text' class='sai si full' id='s-title'></div></td><td></td></tr>
									<tr>
										<td>Date</td>
										<td>
											<div class='sai-ic'>
												<input type='text' class='sai si full' id='s-date'>
												<input type='text' class='sai si half h second' id='s-date2'>
											</div>
										</td>
										<td>
											<div class='radio nus' id='s-db'>
												<span class='r-d radio-sub active' data-op='eq'>&nbsp;&nbsp;&nbsp;On&nbsp;&nbsp;&nbsp;</span>
												<span class='r-d radio-sub' data-op='lt'>&nbsp;Before&nbsp;</span>
												<span class='r-d radio-sub' data-op='gt'>&nbsp;After&nbsp;</span>
												<span class='r-d radio-sub' data-op='bt'>&nbsp;Between&nbsp;</span>
											</div>
											<select id='s-dbm'>
												<option data-op='eq' selected>On</option>
												<option data-op='lt'>Before</option>
												<option data-op='gt'>After</option>
												<option data-op='bt'>Between</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Page Count</td>
										<td>
											<div class='sai-ic'>
												<input type='text' class='sai si full' id='s-page'>
												<input type='text' class='sai si half h second' id='s-page2'>
											</div>
										</td>
										<td>
											<div class='radio nus' id='s-pb'>
												<span class='r-pc radio-sub active' data-op='lt' title='Less Than'>&nbsp;&nbsp;&nbsp;&lt;&nbsp;&nbsp;&nbsp;</span>
												<span class='r-pc radio-sub' data-op='eq' title='Equals'>&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;</span>
												<span class='r-pc radio-sub' data-op='gt' title='Greater Than'>&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;</span>
												<span class='r-pc radio-sub' data-op='bt'>&nbsp;Between&nbsp;</span>
											</div>
											<select id='s-pbm'>
												<option data-op='lt' selected>Less</option>
												<option data-op='eq'>Equals</option>
												<option data-op='gt'>Greater</option>
												<option data-op='bt'>Between</option>
											</select>
										</td>
									</tr>
									
								</table>
								<input id='search-button-x' class='hover bb blue btl bbl btr bbr sai bbs' type='button' value='Search'>
								<input id='b-clear' class='hover bb gray btl bbl btr bbr sai' type='button' value='Clear'>
							</div>
							<div id='search-auto' class='h'></div>
						</div>
					</div>
					<div id='search-results'>
						<div id='search-time'>&nbsp;</div>
						<div id='search-header'>
							<div class='sr-div'>
								<div id='hsr-title' class='sr-title hover asc nus' data-field='fileNameSort'>Title</div>
								<div id='hsr-file' class='sr-file hover asc nus' data-field='baseDirSort'>Folder</div>
								<div id='hsr-pages' class='sr-pages hover asc numeric nus' data-field='pageCount'>Pages</div>
								<div id='hsr-lm' class='sr-lm hover asc nus' data-field='lastModified'>Last Modified</div>
							</div>
						</div>
						<div id='search-body'>
						</div>
						
					</div>
					<div id='search-footer' class='h'>
							<div id='search-left' class='hover blue btl bbl'><</div>
							<div id='search-pages'>
								
							</div>
							<div id='search-right' class='hover blue btr bbr'>></div>
						</div>
					
				</div>
			</div>
		</div>
		<div id='copy' class='h ctdiv bs'>
			<div class='ctdiv'>
				Press '<b>Ctrl+C</b>' to copy the link
			</div>
			<div class='ctdiv'>
				<input type='text' id='copy-text' class='ctdiv'>
			</div>
			<div class='ctdiv'>
				<input type='button' id='copy-close' onClick='rc();' value='Close' class='ctdiv hover bb blue btl bbl btr bbr'>
			</div>
		</div>
		
		<div id='console' class='h'></div>
		
	</body>
</html>