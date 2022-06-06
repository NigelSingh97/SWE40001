<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];
$supplier = $_GET["sup"];
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
    $sql = "INSERT INTO suppliers(name)VALUES('$supplier')";
    
    if ($connection->query($sql) == TRUE) {
         echo '<script>alert("Supplier added successfully!!!")</script>';
    } 
    else {
    	echo '<script>alert("Failed to add supplier, please check DB connection!!!")</script>';
    }
    echo "Error: ".$connection->error;
    $connection->close();
	echo "<script>
          window.location.href='addsuppliers.php';
          </script>"; 

?>