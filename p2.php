<?php
include("database_functions.php");
session_start();

$_SESSION['user'] = "";
$_SESSION['executive'] = "";
$_SESSION['validator'] = "";
$_SESSION['approver'] = "";
$_SESSION['admin'] = "";

function EchoFormDetails(array $details) {
	/* expected array format:
	[
		'id'=>int,
		'timeSubmitted'=>string,
		'userID'=>int,
		'username'=>string,
        'product'=>string,
		'item'=>string,
		'unitPrice'=>string,
		'quantity'=>string,
		'supplierName'=>string,
		'supplierAddress'=>string,
		'remarks'=>string|null,
		'approvedState'=>int (see ApprovalStatus)
	]
	*/
	$id = $details['id'];
	$timeSubmitted = $details['timeSubmitted'];
	$username = $details['username'];
	
	$approvalStatus = 'Unknown';
	switch ($details['approvedState']) {
	case ApprovalStatus::Pending:
		$approvalStatus = 'Pending';
		break;
	case ApprovalStatus::Approved:
		$approvalStatus = 'Approved';
		break;
	case ApprovalStatus::Rejected:
		$approvalStatus = 'Rejected';
		break;
	}
	
	
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
    $conn = $result[1];
    
    $commentSql = "select comment from RequisitionFormComments where formID='$id'";
    $results = $conn->query($commentSql);
    $fetch_assoc = $results->fetch_assoc();
    if ($fetch_assoc){
        $comment = $fetch_assoc["comment"];
    }
    else 
        $comment = "";
    $levelSql = "select levelStatus from RequisitionForms where id='$id'";
    $levelResults = $conn->query($levelSql);
    $levelStatus = $levelResults->fetch_assoc()["levelStatus"];


    if ($_SESSION["approver"] == "true") {	
    if ($levelStatus == "validated") {
		echo "<tr>";
	
		if ($_SESSION["validator"] == "true") {	

			echo "<td><input type='checkbox' name='checkbox[]' value='$id' id='checkbox'></td>";
		}
		
		echo "<td>$id</td>
		<td>$timeSubmitted</td>
		<td>$username</td>
		<td>
			<span class=\"badge warning\">$approvalStatus</span>
		</td>
		<td>$comment
		</td>";
	if ($_SESSION["approver"] == "true" || $_SESSION["admin"] == "true") {		

	echo"<td>
			<span class=\"badge success\"><a href=\"approvecomment.php?formID=$id\">Approve</a></span>
		</td>";
	}

	if ($_SESSION["approver"] == "true" || $_SESSION["admin"] == "true" ||
$_SESSION['validator'] == "true") {	
	echo "		
		<td>
			<span class=\"badge success\"><a href=\"rejectcomment.php?formID=$id\">Reject</a></span>
		</td>";

	}	
	echo "		
		<td>
			<span class=\"badge success\"><a href=\"p4.php?id=$id\">View</a></span>
		</td>
		";

	echo"</tr>";
}
}


else {	
    
		echo "<tr>";
	
		if ($_SESSION["validator"] == "true") {	
			if ($levelStatus == "validated") {
				echo "<td><input type='checkbox' name='checkbox[]' value='$id' id='checkbox' checked></td>";
			}
			else{
				echo "<td><input type='checkbox' name='checkbox[]' value='$id' id='checkbox'></td>";	
			}
		}
		
		echo "<td>$id</td>
		<td>$timeSubmitted</td>
		<td>$username</td>
		<td>
			<span class=\"badge warning\">$approvalStatus</span>
		</td>
		<td>$comment
		</td>";
	if ($_SESSION["approver"] == "true" || $_SESSION["admin"] == "true") {		

	echo"<td>
			<span class=\"badge success\"><a href=\"approvecomment.php?formID=$id\">Approve</a></span>
		</td>";
	}

	if ($_SESSION["approver"] == "true" || $_SESSION["admin"] == "true" ||
$_SESSION['validator'] == "true") {	
	echo "		
		<td>
			<span class=\"badge success\"><a href=\"rejectcomment.php?formID=$id\">Reject</a></span>
		</td>";

	}	
	echo "		
		<td>
			<span class=\"badge success\"><a href=\"p4.php?id=$id\">View</a></span>
		</td>
		";

	echo"</tr>";
}

}


$userID = $_SESSION['id'];

if (isset($userID)) {
	$username = "";
	$isAdmin = false;
	$connectionResult = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	$connection = $connectionResult[1];
	if ($connectionResult[0] == 0) {
		$connection = $connectionResult[1];
		$result = GetUsername($connection, $userID);
		if ($result[0] == 0) $username = $result[1];

		if (!empty($_POST['item'])) {
			$rows = [];
			for ($i = 0; $i < 24; ++$i) {
				$item = $_POST['item'][$i];
				if ($item != "") {
					$row = [
						'item'=>$item,
						'quantity'=>$_POST['quantity'][$i],
						'unitPrice'=>$_POST['unitPrice'][$i],
						'supplier'=>$_POST['supplier'][$i],
						'remarks'=>$_POST['remarks'][$i]
					];
					$rows[] = $row;
				}
			}
			
			AddRequisitionForm($connection, $userID, $rows);
		}
		
		$result = GetUserAccessLevel($connection, $userID);
		if ($result[0] == 0) $isAdmin = $result[1];
	}

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
				<a class="navbar-brand" href="home.php">Private Hospital</a>
				<span class='nav-text'>  Hello     <?php echo $username; ?>, </span>
				<a class="btn btn-dark" href="logout.php">Logout</a>&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<?php 
				if ($_SESSION['validator']=="true" || $_SESSION['approver']=="true") {
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
					<a style="color:black;" href="clear.php?uid=<?php echo $userID;?>">Clear Messages</a>
				<?php
				}
				}
				?>

				
			</nav>
		</header>
             <main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">

                   <div class="carousel-item active" style="background-image:url('img/slide-21.jpg')">
						<h2>View Digital Requisition Forms</h2>
						<div class="table-responsive02">
							<form action="validate.php" method="post">
                            <table>
                                <thead>
                                    <tr>
                                    <?php if ($_SESSION['validator'] == "true"){ ?> 
                                    	<th>Select</th>
                                    <?php }?>
                                        <th>Form ID</th>
                                        <th>Time Submitted</th>
                                        <th>username</th>
                                        <th>Status</th>
                                        <th>Comment</th>
                                        <?php if ($_SESSION['approver'] == "true" ||
                                         $_SESSION['admin'] == "true"  ){ ?> 
                                        <th>Approve</th>
                                    <?php } ?>
                                        <?php if ($_SESSION['approver'] == "true" ||
                                         $_SESSION['admin'] == "true" ||
                                         $_SESSION['validator'] == "true"){ ?> 
                                        <th>Reject</th>
                                    <?php } ?>
                                    	<th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php
									if ($connectionResult == 1) {
										echo("<tr><td>Database connection failed.</td></tr>");
									} else {
										if ($_SESSION['user'] == "true"){
											$result = GetRequisitionForms($connection, ['approvedState'=>ApprovalStatus::Pending, 'username'=>$username]);
										}
										else {
											$result = GetRequisitionForms($connection, ['approvedState'=>ApprovalStatus::Pending]);
										}
										if ($result[0] == 2) {
											echo("<tr><td>Data retrieval failed.</td></tr>");
										} else {
											foreach ($result[1] as $formData) {
												EchoFormDetails($formData);
											}
										}
									}
									?>
                                </tbody>
                            </table>
                            <?php if ($_SESSION['validator'] == "true") {?>
                            <center><label style="color:yellow;">*Checked entries are validated already </label> </center>		
                              <center> <input type="submit" id="submit" value="Validate Selected Entries"></center>

                            <?php } ?>  
                        </form>
                        </div>
							</div>
                 </div>
                 </div>
		</main>        
        </body>
    
	</html>
<?php
} else {
	header("Location: index.php");
	exit();
}
?>
