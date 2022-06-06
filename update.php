<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];

	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
	$product = $_GET["product"];
    $supplier = $_GET["supplier"];
    $desc = $_GET["desc"];
    $price = $_GET["price"];
    $remarks = $_GET["remarks"];
    $qty = $_GET["qty"];
    $formID = $_GET["formID"];
    
    $sql = "update RequisitionFormLines set product='$product', item='$desc',quantity='$qty', unitPrice='$price',supplier='$supplier', remarks='$remarks' where formID='$formID'";
    
    echo "Update query ".$sql;    
    if ($connection->query($sql) == TRUE) {
         echo '<script>alert("Form updated successfully!!!")</script>';
    } 
    else {
    	echo '<script>alert("Failed to update request, please check DB connection!!!")</script>';
    }
    echo "Error: ".$connection->error;
    $connection->close();
	echo "<script>
          window.location.href='p1.php';
          </script>"; 

?>