<?php
	session_start();
	if (isset($_SESSION['uid']) && $_SESSION['uid']){
		header('Location: panel.php');
	}
?>

<html>
	<head>
		<script>
			function load(){
				document.getElementById('username').focus();
			}
		</script>
	</head>
	<body onload='load()'>
		<form id='login' action='login.php' method='post'>
			<table>
				<tr><td>Username</td><td>:</td><td><input type='text' id='username' name='username'></td></tr>
				<tr><td>Password</td><td>:</td><td><input type='password' id='password' name='password'></td></tr>
				<tr><td colspan='3'><input type='submit' value='Submit'></td></tr>
			</table>
			
		</form>
	</body>

</html>