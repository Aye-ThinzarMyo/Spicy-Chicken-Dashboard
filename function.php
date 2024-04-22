<?php
session_start();
include 'components/connect.php';

function test_input($data) {
	
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = test_input($_POST["username"]);
	$pwd = test_input($_POST["pwd"]);
	$stmt = $conn->prepare("SELECT * FROM users");
	$stmt->execute();
	$users = $stmt->fetchAll();
	
	foreach($users as $user) {
		
		if(($user['username'] == $username) && 
			($user['pwd'] == $pwd)) {
		
				echo "<script language='javascript'>";
				echo "alert('Login successful!')";
				echo "</script>";
				header("refresh:0; url=home.php");
				exit;
				// header("location: home.php");
		}
		else {
			echo "<script language='javascript'>";
			echo "alert('WRONG INFORMATION! PLEASE TRY AGAIN')";
			echo "</script>";
			header("refresh:0; url=login.php");
        exit;
		// echo "<script language='javascript'>";
        // echo "var alertBox = alert('WRONG INFORMATION! PLEASE TRY AGAIN');";
        // echo "alertBox.style.color = 'red'";
        // echo "</script>";
        // header("refresh:0; url=login.php");
        // exit;
		
	
	}
}
}

?>