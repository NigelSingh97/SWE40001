<?php
include("database_functions.php");

session_start();

$userID = $_SESSION['id'];

if (isset($userID)) {
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
				<a class="btn btn-dark" href="logout.php">Logout</a>
			</nav>
		</header>
            <main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
					
					<div class="carousel-inner">
						<div class="carousel-item active" style="background-image:url('img/slide-11.jpg')">
							

 <h2>Update Digital Requisition Form</h2>
                         <form action="update.php" class="rcorners">
  													<br><br>
  												<input type="hidden" id="formID" name="formID" value="<?php echo $_GET['formID']?>">
  													
											    <label for="product" class="lbl">Product:</label>
												    <input type="text" id="product" name="product" class="rcorners1" value="<?php echo $_GET['product']?>">
												  
												  <label for="supplier" class="lbl">Supplier:</label>
												  <select id="supplier" name="supplier" class="rcorners1">
												    <option value="supplier1">supplier1</option>
												    <option value="supplier2">supplier2</option>
												    <option value="supplier3">supplier3</option>
												    <option value="supplier4">supplier4</option>
												    <option value="supplier5">supplier5</option>
												  </select>

												  <label for="qty" class="lbl">Quantity:</label>
												  <input type="text" id="qty" name="qty" class="rcorners1" required value="<?php echo $_GET['qty']?>">

												  <label for="price" class="lbl">Price:</label>
												  <input type="text" id="price" name="price" class="rcorners1" value="<?php echo $_GET['price']?>">


												  <label for="desc" class="lbl">Item Description:</label>
												  <input type="text" id="desc" name="desc" class="rcornersitem"  value="<?php echo $_GET['desc']?>"><br>

												<br><br>

												  <label for="remarks" class="lbl">Remarks:</label><br><br> 
												  <textarea class="remarks" id="remarks" name="remarks" rows="5" ><?php echo $_GET['remarks']?></textarea><br>
												  <br><br>
												  <center><input type="submit" class="buttonrcorners" value="Update"></center>
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