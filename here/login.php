<?php 

session_start(); 
include "database_functions.php";

if (isset($_POST['uname']) && isset($_POST['password'])) {

	function Sanitise($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$uname = Sanitise($_POST['uname']);
	$pass = Sanitise($_POST['password']);

	if (empty($uname)) {
		header("Location: index.php?error=Username is required.");
	    exit();
	} else if(empty($pass)) {
        header("Location: index.php?error=Password is required.");
	    exit();
	}
	
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	if ($result[0] == 1) {
		header("Location: index.php?error=A database connection error occured.");
		exit();
	}
	$connection = $result[1];
	
	$result = LogInUser($connection, $uname, $pass);
	switch ($result[0]) {
	case 0:
		$_SESSION['id'] = $result[1];
		$uid = $result[1];
		$typesql = "select type from Users where id= '$uid'";
		$typeResult =  $connection->query($typesql);
		
		$type = $typeResult->fetch_assoc()['type'];
		echo "USER TYPE ".$type;
	
		if ($uname == 'admin') {
			header("Location: addusers.php");	
			break;
		}
		if ($type == 'Validator') {
			header("Location: homeplus.php");	
			break;
		}
		if ($type == 'Approver') {
			header("Location: p2.php");
			break;	
		}
		else {
			header("Location: home.php");	
		}
		break;
	case 1:
		header("Location: index.php?error=Incorrect Username or password.");
		break;
	case 2:
		header("Location: index.php?error=An internal database error has occured.");
		break;
	}
	
	exit();
}else{
	header("Location: index.php");
	exit();
}

?>