<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];

	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
    $sql = "update NotificationMessages set isread='1' where userID='$userID'";
    
    echo "Update query ".$sql;    
    $connection->query($sql);
    echo "Error: ".$connection->error;
    $connection->close();
    if ($_SESSION['user'] == "true" || $_SESSION['executive'] == "true") {
	       echo "<script>
                window.location.href='p1.php';
            </script>"; 
      }
     else {
           echo "<script>
                window.location.href='p2.php';
            </script>"; 
     } 

?>