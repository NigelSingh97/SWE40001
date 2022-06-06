 <?php
    include "database_functions.php";
    $result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
    $conn = $result[1];
    $fid = $_GET['fid'];
    $rid = $_GET['rid'];

    $sql = "delete from RequisitionFormLines where formID='$fid'";
    if ($conn->query($sql) == FALSE) {
      echo '<script>alert("Failed to delete111, check database connection!!!")</script>';    
    }
echo "ERROR1 ".$conn->error;
    $sql = "delete from RequisitionForms where id='$fid'";
    if ($conn->query($sql) == TRUE) {
        echo '<script>alert("Successfully deleted request form!!!")</script>';  
    } 
    else{
      echo '<script>alert("Failed to delete222, check database connection!!!")</script>';    
    }
    echo "ERROR ".$conn->error;
    echo "<script>
          window.location.href='p1.php';
          </script>"; 
?>