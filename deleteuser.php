 <?php
    include "database_functions.php";
    $result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
    $conn = $result[1];
    $uname = $_GET['uname'];
    $pwd = $_GET['pwd'];
    $type = $_GET['type'];

    $sql = "delete from Users where userName='$uname' and password='$pwd'";
    if ($conn->query($sql) == TRUE) {
        echo '<script>alert("Successfully deleted user!!!")</script>';  
    } 
    else{
      echo '<script>alert("Failed to delete user, check database connection!!!")</script>';    
    }
    echo "ERROR ".$conn->error;
    echo "<script>
          window.location.href='addusers.php';
          </script>"; 
?>