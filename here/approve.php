<?php 
include "database_functions.php";
session_start(); 
$userID = $_SESSION['id'];

	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $result[1];
	
    $formID = $_SESSION["fid"];
  $comment = "";
 /* echo "<script>
               let commentstr = prompt('Please add your comments!!!');
               document.cookie = 'comment='+commentstr;
             if (!commentstr) {
               window.location.href='p2.php';     
             }
             
             </script>";
*/
    $comment =$_COOKIE['comment'];        
    echo "COmment ".$comment;
    
    
    $time= date('Y-m-d H:i:s');

    $updatequery = "update RequisitionForms set approvedState='approved', approverUserID='$userID' where id='$formID'";
    $connection->query($updatequery);
    echo "Update Forms table status ".$connection->error; 

    
    $connection->query("delete from RequisitionFormComments where formID='$formID'");
    echo "Delete query status".$connection->error;  
     $updatequery = "INSERT INTO RequisitionFormComments(formID,userID,time,comment)VALUES('$formID','$userID','$time','$comment')";
    
   
    echo "Insert query ".$updatequery;    
   
    $message = "Form ID ".$formID." got approved";
    if ($connection->query($updatequery) == TRUE) {
        $fetch = "select userID from RequisitionForms where id ='$formID'";
        $fetchres = $connection->query($fetch);
        $auid = $fetchres->fetch_assoc()['userID'];
        $notifquery = "INSERT INTO NotificationMessages(userID,message,isread)VALUES('$auid','$message','0')";
        $connection->query($notifquery);
        echo '<script>alert("Approved successfully!!!")</script>';
    } 
    else {
    	echo '<script>alert("Failed to approve, please check DB connection!!!")</script>';
    }
    echo "Error 3 ".$connection->error;
    $connection->close();
	echo "<script>
          window.location.href='p2.php';
          </script>"; 

?>