<?php 
session_start(); 

$formID = $_GET["formID"];
$_SESSION['fid'] = $formID;
  echo "<script>
               let commentstr = prompt('Please add your comments!!!');
               document.cookie = 'comment='+commentstr;
             if (!commentstr) {
               window.location.href='p2.php';     
             }
              window.location.href='reject.php';     
             </script>";

?>     