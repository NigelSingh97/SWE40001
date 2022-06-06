<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];
$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
$connection = $result[1];

$product = $_SESSION["fproduct"];
$supplier = $_SESSION["fsupplier"];
$desc = $_SESSION["fdesc"];
$price = $_SESSION["fprice"];
$remarks = $_COOKIE["fcomment"];
$qty = $_SESSION["fqty"];
            echo "------Approved--->";
    	$timesub= date('Y-m-d H:i:s');
    	$sql = "INSERT INTO  RequisitionForms(timeSubmitted,userID)VALUES ('$timesub','$userID')";
    	$formID = -1;
        if ($connection->query($sql) == FALSE) {
             echo '<script>alert("Failed to add request, please check DB connection111!!!")</script>';
             header("Location: p1.php");
             return;
        } 
        else {
          $formID = $connection->insert_id;
        }
        
        $sql = "INSERT INTO  RequisitionFormLines(formID,product,item,quantity, unitPrice,supplier, remarks)
           VALUES ('$formID','$product','$desc','$qty','$price', '$supplier','$remarks')";
        $message = "Form ID ".$formID." got newly filed";
        if ($connection->query($sql) == TRUE) {
        
        $fetch = "select id from Users where type ='Validator'";
        $fetchres = $connection->query($fetch);
        $auid = $fetchres->fetch_assoc()['id'];
        $notifquery = "INSERT INTO NotificationMessages(userID,message,isread)VALUES('$auid','$message','0')";
        $connection->query($notifquery);

        $fetch = "select id from Users where type ='Admin'";
        $fetchres = $connection->query($fetch);
        $auid = $fetchres->fetch_assoc()['id'];
        $notifquery = "INSERT INTO NotificationMessages(userID,message,isread)VALUES('$auid','$message','0')";
        $connection->query($notifquery);

             echo '<script>alert("Request Submitted successfully!!!")</script>';
        } 
        else {
        	echo '<script>alert("Failed to add request, please check DB connection!!!")</script>';
        }

        echo "Error: ".$connection->error;
   
    $connection->close();

	  
     
echo "<script>
        window.location.href='p1.php';
        </script>"; 

?>