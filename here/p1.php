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

												<?php } 
												else {

												?>

 												<h2>File a Digital Requisition Form</h2>
                         <form action="add.php" class="rcorners">
                                                <?php
                                                    $formid = -1;
                                                    if (!empty($_GET["formid"]))
                                                        $formid = $_GET["formid"]; 
                                                    echo "<input type=\"hidden\" name=\"formid\" value=\"$formid\">";
                                                        
                                                    ?>
  													<br><br>
											    <label for="product" class="lbl">Product:</label>
												    <input type="text" id="product" name="product" class="rcorners1" required>
												  
												  <label for="supplier" class="lbl">Supplier:</label>
												  <select id="supplier" name="supplier" class="rcorners1">
												  	<?php 
												  	while ($row = $suppliers->fetch_assoc()) {
															$sname = $row['name']; ?>
															<option value="<?php echo $sname;?>"><?php echo $sname;?></option>	
														<?php
														}
												  	?>
												  </select>

												  <label for="qty" class="lbl">Quantity:</label>
												  <input type="text" id="qty" name="qty" class="rcorners1" required >

												  <label for="price" class="lbl">Price:</label>
												  <input type="text" id="price" name="price" class="rcorners1" required>


												  <label for="desc" class="lbl">Item Description:</label>
												  <input type="text" id="desc" name="desc" class="rcornersitem" required><br>

												<br><br>

												  <label for="remarks" class="lbl">Remarks:</label><br><br> 
												  <textarea class="remarks" id="remarks" name="remarks" rows="5"  required></textarea><br>
												  <br><br>
												  <center><input type="submit" class="buttonrcorners" value="Add"></center>
													</form> 
												<?php } ?>
													<br><br>
                                <div class="table-responsive">
                                <form action="p2.php" method="post">
                                <table id="table">
                                  <thead>
                                    <tr>
                                      <th>No</th>
                                      <th>Product</th>
                                      <th>Item Description</th>
                                      <th>QTY</th>
                                      <th>Unit Price</th>
                                      <th>Supplier</th>
                                      <th>Remarks</th>
                                      <th>Edit</th>
                                      <?php if ($isAdmin) {?>

                                      <th>Delete</th>

                                    <?php } ?>
                                    </tr>
                                  </thead>
                                  <tbody>
									 <?php
        $conresult = GetConnection("localhost", "root", "", "test_db", 3306);
        $conn = $conresult[1];
        $sql = "";
        if ($isAdmin || $_SESSION["executive"] == "true") {
        	$sql = "select * from RequisitionFormLines";
      	}
      	else {
					$sql = "select * from RequisitionFormLines where formID =$formid";
      	}
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
        	$type = "Fetched forms";
          while($row = $result->fetch_assoc()) {
          	$uid = $row["id"];
          	$fid = $row["formID"];
            $product = $row["product"];
            $desc = $row["item"];
            $qty = $row["quantity"];
            $price = $row["unitPrice"];
            $supplier = $row["supplier"];
            $remarks = $row["remarks"];

          ?>
          <tr>
          	<td><?=$fid;?></td>
            <td><?=$product;?></td>
            <td><?=$desc;?></td>
            <td><?=$qty; ?></td>
            <td><?=$price;?></td>
            <td><?=$supplier;?></td>
            <td><?=$remarks;?></td>
          <td>
            <a style="cursor: pointer;" href="edit.php?product=<?php echo $product;?>&desc=<?php echo $desc;?>&qty=<?php echo $qty;?>&price=<?php echo $price;?>
            &supplier=<?php echo $supplier;?>&remarks=<?php echo $remarks;?>&formID=<?php echo $fid;?>" >
                Edit
            </a>
          </td>
          <?php if ($isAdmin) {?>
         <td>
              <a href= "delete.php?fid=<?php echo $fid;?>&rid=<?php echo $uid;?>" 
                style="cursor: pointer;" >
                  Delete
                </a>
          </td>
        <?php } ?>
  
          </tr>   
      <?php 
      		
          }
        }
        else{
        	$type ="EMPTY RESULT";
        }
        
      ?>  
                                  </tbody>
                                </table>
                                    <!-- after submit button is clicked the information should be stored in the database and should be shown in the view req form -->
                                   <center> <input type="submit" id="submit" value="Submit"></center>
                                </form>    
                                </div>
                                
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
