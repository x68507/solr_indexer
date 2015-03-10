<?php
	
	$target_dir = realpath($_COOKIE['curDir']).'//';
	$target_dir = $target_dir . basename( $_FILES["uploadFile"]["name"]);
	

	//echo $_FILES["uploadFile"]["tmp_name"].'<hr>';
	echo $target_dir.'<hr>';
	//foreach($_FILES['uploadFile'] as $key => $file){
		if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $target_dir)) {
		//if (move_uploaded_file($file['tmp_name'], $target_dir)) {
			if (isset($_POST['scan']) && $_POST['scan']=='yes'){
				require(realpath(__DIR__ .'/tika_file.php'));
				tika(realpath($target_dir));
			}
			
		//	header('Location: index.php');
		}
		/*} else {
			echo "Sorry, there was an error uploading your file.<hr><a href='index.php'>Return</a>";
		}
		*/
	//}
	
?>