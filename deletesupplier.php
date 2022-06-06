 <?php
    include "database_functions.php";
    $result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
    $conn = $result[1];
    $uname = $_GET['uname'];

    $sql = "delete from suppliers where name='$uname'";
    if ($conn->query($sql) == TRUE) {
        echo '<script>alert("Successfully deleted user!!!")</script>';  
    } 
    else{
      echo '<script>alert("Failed to delete user, check database connection!!!")</script>';    
    }
    echo "ERROR ".$conn->error;
    echo "<script>
          window.location.href='addsuppliers.php';
          </script>"; 
?>