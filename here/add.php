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
    $formid =$_GET["formid"];
    

    $sql = "select * from RequisitionFormLines where product='$product'";
    $result= $connection->query($sql);

    if ($result->num_rows > 0) {
      	echo '<script>alert("Rejected due to duplicate!!!")</script>';
        if ($_SESSION['executive'] == "true") {

            $_SESSION['fproduct'] = $product;
            $_SESSION['fsupplier'] = $supplier;
            $_SESSION['fdesc'] = $desc;
            $_SESSION['fprice'] = $price;
            $_SESSION['fremarks'] = $remarks;
            $_SESSION['fqty'] = $qty;
            $_SESSION['fformID'] = $formid;
                echo "<script>
        window.location.href='forcecomment.php';
        </script>"; 

        }
    }
    else if ($qty > 100000) {
        echo '<script>alert("Rejected due to exceeding max quantity of 100000!!!")</script>';
        if ($_SESSION['executive'] == "true") {

            $_SESSION['fproduct'] = $product;
            $_SESSION['fsupplier'] = $supplier;
            $_SESSION['fdesc'] = $desc;
            $_SESSION['fprice'] = $price;
            $_SESSION['fremarks'] = $remarks;
            $_SESSION['fqty'] = $qty;
                echo "<script>
        window.location.href='forcecomment.php';
        </script>"; 

        }
    }
    else {
            echo "------Approved--->";
        if ($formid == -1 ){
                	   $timesub= date('Y-m-d H:i:s');
    	   $sql = "INSERT INTO  RequisitionForms(timeSubmitted,userID)VALUES ('$timesub','$userID')";
    	   $formid = -1;
            if ($connection->query($sql) == FALSE) {
                echo '<script>alert("Failed to add request, please check DB connection111!!!")              </script>';
                header("Location: p1.php");
                return;
            } 
            else {
            $formid = $connection->insert_id;
            }
        }

        
            $sql = "INSERT INTO  RequisitionFormLines(formID,product,item,quantity, unitPrice,supplier,     remarks)
            VALUES ('$formid','$product','$desc','$qty','$price', '$supplier','$remarks')";
            $message = "Form ID ".$formid." got newly filed";
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

	/*echo "<script>
          window.location.href='p1.php';
          </script>"; */
      }
     
echo "<script>
        window.location.href='p1.php?formid=$formid';
                </script>"; 

?>