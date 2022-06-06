<?php
include("database_functions.php");

session_start();
$isAdmin = true;
$userID = $_SESSION['id'];
$_SESSION['user'] = "";
$_SESSION['executive'] = "";
$_SESSION['validator'] = "";
$_SESSION['approver'] = "";
$_SESSION['admin'] = "";
$connection = null;
if (isset($userID)) {
	$isAdmin = false;
	$username = "";
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	if ($result[0] == 0) {
		$connection = $result[1];
		$result = GetUsername($connection, $userID);
		if ($result[0] == 0) $username = $result[1];
		$typesql = "select type from Users where id= '$userID'";
		$typeResult =  $connection->query($typesql);
		
		$type = $typeResult->fetch_assoc()['type'];
		echo "USER TYPE ".$type;
		if ($type == 'Regular User') {
			$_SESSION['user'] = "true";
		}
		if ($type == 'Executive') {
			$_SESSION['executive'] = "true";
		}
		if ($type == 'Validator') {
			$_SESSION['validator'] = "true";
		}
		if ($type == 'Approver') {
			$_SESSION['approver'] = "true";
		}
		if ($type == 'Admin') {
			$_SESSION['admin'] = "true";
			$isAdmin = true;
		}
		$sql = "select * from suppliers";
		$suppliers = $connection->query($sql);

	}
?>
<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes" />
		<meta name="description" content="Hospital Main page" />
		<title>Private Hospital</title>
		<link rel="stylesheet" type="text/css" href="styles/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="styles/style.css">

		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
		<meta name="theme-color" content="#563d7c" />

	</head>
        <body>
        <header>
			<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-success">
				<a class="navbar-brand" href="home.php">Private Hospital</a>
				<span class='nav-text'>  Hello     <?php echo $username; ?>, </span>
				<a class="btn btn-dark" href="logout.php">Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php 
				$nresult = $connection->query("select * from NotificationMessages where userID='$userID' and isread='0'");
				if ($nresult->num_rows > 0) { 
					$total = "";
					while($row = $nresult->fetch_assoc()) {
						$total = $row['message'];
						break;
					}
				?>
					<label style="color:white;">New Message: <?php echo $total;?></label>
					&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a  style="color:black;" href="clear.php?uid=<?php echo $userID;?>">Clear Messages</a>
				<?php
				}
				?>

				
			</nav>
		</header>
            <main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
					
					<div class="carousel-inner">
						<div class="carousel-item active" style="background-image:url('img/slide-11.jpg')">
							
												<?php if($isAdmin) { ?>
													<h2>Add New Supplier</h2>
                         <form action="addsupplier.php" class="miniform">
  													<br><br>
  													<center>
											    <div>	
											    <label for="sup" class="lbl">Enter Supplier Name:</label>
												    <input type="text" id="sup" name="sup" class="supfield" required>&nbsp;&nbsp;&nbsp;&nbsp;
												    <input type="submit" class="supbutton" value="Add Supplier">
												  </div></center>
												    
												    </form>

												<?php } ?>
													<br><br>
                            <div class="table-responsive">
                                <form action="p1.php" method="post">
                                <table id="table">
                                  <thead>
                                    <tr>
                                    	<th>No</th>
                                      <th>Supplier Name</th>
                                      <th>Delete</th>
                                    </tr>
                                  </thead>
                                  <tbody>
									 <?php
        $conresult = GetConnection("localhost", "root", "", "test_db", 3306);
        $conn = $conresult[1];
        $sql = "SELECT * FROM `suppliers`";
        $result = $conn->query($sql);
        echo "Error ".$conn->error; 
        if ($result->num_rows > 0) {
        	$id = 1;
          while($row = $result->fetch_assoc()) {
          	$uname = $row["name"];
            $ids   = $row["id"];
         
          ?>
          <tr>
          	<td><?=$id;?></td>
            <td><?=$uname;?></td>
          
         <td>
              <a href= "deletesupplier.php?uname=<?php echo $uname;?>" 
                style="cursor: pointer;" >
                  Delete
                </a>
          </td>
        
  
          </tr>   
      <?php 
      		$id = $id + 1;
          }

        }
        
      ?>  
                                  </tbody>
                                </table>
                                </form>    
                                </div>
<?php
} else {
	header("Location: index.php");
	exit();
}
?>
