<?php 
session_start(); 
  
echo "<script>
          let fcomment = prompt('Do you want to force submit it?');
          document.cookie = 'fcomment='+fcomment;
          if (!fcomment) {
            window.location.href='p1.php';  
          }
          window.location.href='addforce.php';     
          </script>";

?>     