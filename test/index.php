<!DOCTYPE html>
<html>
	<head>
		<!--IE bug fix to prevent caching of webpages-->
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="extensions/jQuery.js"></script>
		<!--<link rel="icon" type="image/ico" href="lander.ico">
		<link rel="icon" href="lander.ico" type="image/x-icon">-->
		<link rel="icon" href="lander.png" type="image/x-icon">
		<title></title>
		<style>
			.s{padding:10px;border-radius:25px}
			.s:hover{cursor:pointer;background:#F2F2F2}
			body{overflow:hidden;width:98%;}
			a{text-decoration: none;color:black}
			a:visited{text-decoration:none;color:black}
			img {
				width:150px;
				height:150px;
				max-width: 100%;
				height: auto;
				width: auto\9; /* ie8 */
			}
			
			h1{text-align:center}
			table{width:100%;height:90%;text-align:center}
			#divtitle{width:98%;text-align:center;position:relative;top:5%;font-size:32px}
			
			tr{height:30%}
			td{width:25%}
			.active{background:#EFEFEF}
		</style>
		<script type="text/javascript">
			var _max = 150;
			var l = '';
			$(document).ready(function(){
				$('.s').on('click',function(){
					fncOpen(this);
				});
				var s = '';
				//binds keydown
				$('body').on('keypress',function(e){
					switch (e.keyCode){
						case 49:
							$('#t1').click();break;
						case 50:
							$('#t2').click();break;
						case 51:
							$('#t3').click();break;
						case 52:
							$('#t4').click();break;
						case 53:
							$('#t5').click();break;
						case 54:
							$('#t6').click();break;
						case 55:
							$('#t7').click();break;
						case 56:
							$('#t8').click();break;
						case 57:
							$('#t9').click();break;
					}
					e.preventDefault();
				});
				$(document).on('keydown',function(e){
					//console.log('in here')
					//console.log($('.s.active').index())
					if (e.keyCode==9){
						if ($('.s.active').length==0 && !e.shiftKey){
							$('.s:eq(0)').addClass('active');
							e.preventDefault();
						}else if($('.s.active').length==0 && e.shiftKey){
							$('.s:eq('+($('.s').length-1)+')').addClass('active');
							e.preventDefault();
						}else if($('.s.active').index('.s')<($('.s').length-1) && !e.shiftKey){
							$('.s:eq('+($('.s.active').index('.s')+1)+'),.s.active').toggleClass('active');
							e.preventDefault();
						}else if($('.s.active').index('.s')>0 && e.shiftKey){
							$('.s:eq('+($('.s.active').index('.s')-1)+'),.s.active').toggleClass('active');
							e.preventDefault();
						}else{
							$('.active').toggleClass('active');
						}
					}else if(e.keyCode==13){
						$('.s.active').click();
					}
				});
			})

			function get(name){if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search)) return decodeURIComponent(name[1]);}
			document.title="Suss Corona Webserver";
			function fncOpen(that){
				var _url = '';
				
				
				
				
				window.open($(that).attr('data-url'),'_self');
				return false;
				
				e.preventDefault();
			}
		</script>
	</head>
	<body>
		<!--<div id='divtitle'>Süss MicroTec Photonic Systems Landing Page</div>-->
		<h1>Süss MicroTec Photonic Systems Landing Page</h1>
		<table>
			<tr>
				<td>
					<div id='t1' class='s' data-url='arm/'>
						<img src='sod.png'>
						<div id='divrework'>ARM (Action Request/Minutes)</div>
					</div>
				</td>
				<td>
					<div id='t2' class='s' data-url='wiki/'>
						<img src='wiki.png'>
						<div id='divwiki'>Wiki</div>
					</div>
				</td>
				<td>
					<div id='t3' class='s' data-url='m2m/'>
						<img src='m2m.png'>
						<div id='divvideo'>Item Master</div>
					</div>
				</td>
				<td>
					<div id='t4' class='s' data-url='/m2m/sqlsrv.php'>
						<img src='reports.png'>
						<div id='divrework'>Reporter</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id='t5' class='s' data-url='/m2m/sn.php'>
						<img src='sn.png'>
						<div id='divrework'>Serial Numbers</div>
					</div>
				</td>
				<td>
					<div id='t6' class='s' data-url='https://suss.2leap.com/'> 
						<img src='el.png'>
						<div id='divrework'>eLEAP</div>
					</div>
				</td>
				<td>
					<div id='t7' class='s' data-url='http://<?php echo $_SERVER['HTTP_HOST'];?>:8080/'>
						<img src='elog.png'>
						<div id='divdms'>ELOG</div>
					</div>
				</td>
				<td>
					<div id='t8' class='s' data-url='http://eng01.suss.com/ITA/Login.aspx'>
						<img src='bug-tracking.png'>
						<div id='divbug'>Issue Tracker</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id='t9' class='s' data-url='http://eng01/AutodeskTC/Landing?Server=eng01&Vault=vault'>
						<img src='autodesk.png'>
						<div id='divvault'>Vault</div>
					</div>
				</td>
				<td>
					<div id='t10' class='s' data-url='/3d/'>
						<img src='3d/favicon.png'>
						<div id='div3d'>3D Graph</div>
					</div>
				</td>
				<td>
					<?php
						error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
						$fp = fsockopen('127.0.0.1', 8983, $errno, $errstr, 1);
						if ($fp) {
							echo "<div id='t11' class='s' data-url='/sap/'>";
						}else{
							echo "<div id='t11' class='s' data-url='/sap/panel/'>";
						}
						
						fclose($fp);
						error_reporting(-1);
						
						
					
					?>
						<img src='sap.png'>
						<div id='divSAP'>SAP Manuals</div>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>


