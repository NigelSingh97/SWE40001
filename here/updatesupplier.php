<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];

	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
    $uname = $_GET["uname"];
    $ids   = $_GET["ids"];
    
    $sql = "UPDATE `suppliers` SET `name`='$uname' WHERE `id`='ids'";
    
    echo "Update query ".$sql;    
    if ($connection->query($sql) == TRUE) {
         echo '<script>alert("User updated successfully!!!")</script>';
    } 
    else {
    	echo '<script>alert("Failed to update user, please check DB connection!!!")</script>';
    }
    echo "Error: ".$connection->error;
    $connection->close();
	echo "<script>
          window.location.href='addsuppliers.php';
          </script>"; 

?>