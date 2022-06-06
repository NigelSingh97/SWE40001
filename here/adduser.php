<?php 
include "database_functions.php";
    session_start(); 
    $userID = $_SESSION['id'];
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
	$uname = $_GET["uname"];
    $pwd = $_GET["pwd"];
    $type = $_GET["type"];
    $sql = "select * from Users where userName='$uname' and password = '$pwd'";
    $result= $connection->query($sql);
    if ($result->num_rows > 0) {
      	echo '<script>alert("User name exists already!!!")</script)'
      	;
        $connection->close();
      	echo "<script>
          window.location.href='addusers.php';
        </script>"; 
    }
    else {
    	$sql = "INSERT INTO  Users(userName,password,admin,type)VALUES ('$uname','$pwd','0','$type')";
        if ($connection->query($sql) == TRUE) {
             echo '<script>alert("User Added successfully!!!")</script>';
        } 
        else {
        	echo '<script>alert("Failed to add user, please check DB connection!!!")</script>';
        }
        echo "Error: ".$connection->error;
        $connection->close();
        echo "<script>
          window.location.href='addusers.php';
          </script>";
    }

?>