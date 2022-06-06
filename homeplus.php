<?php
include("database_functions.php");

session_start();

$userID = $_SESSION['id'];

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
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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
				<a class="navbar-brand" href="#">Private Hospital</a>
				<span class='nav-text'>  Hello     <?php echo $username; ?>, </span>
				<a class="btn btn-dark" href="logout.php">Logout</a>
			</nav>
		</header>

		<main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-controls">
						<ol class="carousel-indicators">
							<li data-target="#carousel" data-slide-to="0" style="background-image:url('img/slide-21.jpg')"></li>
							<li data-target="#carousel" data-slide-to="1" style="background-image:url('img/slide-31.jpg')"></li>
						</ol>
						<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
							<img src="img/left-arrow.svg" alt="Prev">
						</a>
						<a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
							<img src="img/right-arrow.svg" alt="Next">
						</a>
					</div>
					<div class="carousel-inner">
						<div class="carousel-item active" style="background-image:url('img/slide-21.jpg')">
							<div class="container">
                                <a href="p2.php"><div class="container">
								<h2>View Digital Requisition Forms</h2>
                                    </div></a>
						</div>
                        </div>
						<div class="carousel-item" style="background-image:url('img/slide-31.jpg')">
							<div class="container">
                                <a href="p3.php">
								<h2>View Past Requisition Form</h2>
                                   </a> </div>
						</div>
                        <?php if($isAdmin) { ?>
                                <div class="carousel-item" style="background-image:url('img/slide-41.jpg')">
							<div class="container">
                                <a href="addusers.php">
								<h2>Add User</h2>
                                   </a> </div>
						</div>
                            <?php } ?>
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
