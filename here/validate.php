<?php 
include "database_functions.php";
session_start(); 
$userID = $_GET['id'];

$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
$connection = $result[1];
  $failed = false;   	
  $numbers= "";
  foreach($_POST['checkbox'] as $formID){
    echo "FormID : ".$formID.'<br/>';
    $numbers = $numbers.", ".$formID;
    $sql = "update RequisitionForms set levelStatus='validated' where id='$formID'";
    if ($connection->query($sql) == FALSE) {
    	echo '<script>alert("Failed to validate, please check DB connection!!!")</script>';
     $failed = true;
     break;
     }
  }

  $message ="New Forms validated ".$numbers;
  $fetch = "select id from Users where type ='Approver'";
        $fetchres = $connection->query($fetch);
        $auid = $fetchres->fetch_assoc()['id'];
        $notifquery = "INSERT INTO NotificationMessages(userID,message,isread)VALUES('$auid','$message','0')";
        $connection->query($notifquery);

  if (!$failed) {
    echo '<script>alert("Forms Validated successfully!!!")</script>';
     }
    echo "Error: ".$connection->error;
    $connection->close();
	echo "<script>
          window.location.href='p2.php';
          </script>";

?>