<?php
include("database_functions.php");

session_start();
$isAdmin = true;
$userID = $_SESSION['id'];
if (isset($userID)) {
	$isAdmin = false;
	$username = "";
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	if ($result[0] == 0) {
		$connection = $result[1];
		$result = GetUsername($connection, $userID);
		if ($result[0] == 0) $username = $result[1];
	}
?>
<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes" />
		<meta name="description" content="Hospital Edit User page" />
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
				<a class="btn btn-dark" href="logout.php">Logout</a>
			</nav>
		</header>
            <main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
					
					<div class="carousel-inner">
						<div class="carousel-item active" style="background-image:url('img/slide-11.jpg')">
							

 												<h2>Edit User Details</h2>
                         <form action="updateuser.php" class="rcorners">
  													<br><br>
											    <label for="uname" class="lbl">UserName:</label>
												    <input type="text" id="uname" name="uname" class="rcorners1" required value="<?php echo $_GET['uname']?>">
												  
												  <label for="type" class="lbl">User Type:</label>
												  <select id="type" name="type" class="rcorners1">
												    <option value="Regular User" 
												    <?php 
												    	if ($_GET['type'] == 'Regular User') { ?>
												    		selected
												    <?php	}
												    ?>
												    >Regular User</option>
												    <option value="Executive" 												    <?php 
												    	if ($_GET['type'] == 'Executive') { ?>
												    		selected
												    <?php	}
												    ?>
>Executive</option>
												    <option value="Validator" 												    <?php 
												    	if ($_GET['type'] == 'Validator') { ?>
												    		selected
												    <?php	}
												    ?>
>Validator</option>
												    <option value="Approver" 												    <?php 
												    	if ($_GET['type'] == 'Approver') { ?>
												    		selected
												    <?php	}
												    ?>
>Approver</option>
												  </select>

												  <label for="pwd" class="lbl">Password:</label>
												  <input type="password" id="pwd" name="pwd" class="rcorners1" required value="<?php echo $_GET['pwd']?>">
												<br><br>

												  <center><input type="submit" class="buttonrcorners" value="Update User"></center>
													</form> 
													<br><br>
                                
                                
                                </div>
							</div>
                            </div>
			
		</main>
		<script src="scripts/bootstrap.bundle.min.js"></script>
        </body>
    </html>
<?php
} else {
	header("Location: index.php");
	exit();
}
?>