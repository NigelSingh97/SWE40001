<?php
include("database_functions.php");

function EchoFormHeader(array $form) {
	if (empty($form)) {
		echo '<p>The form either does not exist, or you do not have sufficient privileges to view this form.';
	} else {
		$approvalString = '???';
		switch ($form['approvedState']) {
		case ApprovalStatus::Pending:
			$approvalString = 'Pending';
			break;
		case ApprovalStatus::Approved:
			$approvalString = 'Approved';
			break;
		case ApprovalStatus::Rejected:
			$approvalString = 'Rejected';
			break;
		}
		
		echo("<p id=\"strong\">Form ID: <strong>{$form['id']}</strong></p>
		<p id=\"strong\">Time Submitted: <strong>{$form['timeSubmitted']}</strong></p>
		<p id=\"strong\">Submitter: <strong>{$form['username']}</strong></p>
		<p id=\"strong\">Approval Status: <strong>$approvalString</strong></p>");
	}
}

function EchoFormDetails(array $form) {
	if (isset($form['rows'])) {
		for ($i = 0; $i < count($form['rows']); ++$i) {
			$index = $i+1;
			$row = $form['rows'][$i];
			$remarks = isset($row['remarks']) ? $row['remarks'] : '';
			
			echo "<tr>
				<td>$index</td>
				<td>{$row['item']}</td>
				<td>{$row['quantity']}</td>
				<td>{$row['unitPrice']}</td>
				<td>{$row['supplier']}</td>
				<td>$remarks</td>
			</tr>";
		}
	}
}

function CreateApprovalButtons(int $formID) {
	$approve = ApprovalStatus::Approved;
	$reject = ApprovalStatus::Rejected;
	
	echo "<a id=\"button\" href=\"p4.php?id=$formID&setApproval=$approve\">Approve</a>
	<a id=\"button\" href=\"p4.php?id=$formID&setApproval=$reject\">Reject</a>";
}

session_start();

$userID = $_SESSION['id'];

if (isset($userID)) {
	$username = "";
	$problemMessage = "";
	$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	if ($result[0] == 0) {
		$connection = $result[1];
		$result = GetUsername($connection, $userID);
		if ($result[0] == 0) $username = $result[1];
		
		// is the user an admin?
		$admin = false;
		$result = GetUserAccessLevel($connection, $userID);
		if ($result[0]==0) $admin = $result[1];
	
		// load form details
		$formID = $_GET['id'];
		$form = [];
		
		if (isset($formID)) {
			// approve / reject?
			if (isset($_GET['setApproval'])) {
				SetRequisitionFormApprovalStatus($connection, $formID, $userID, $_GET['setApproval']);
			}
			
			$result = GetRequisitionForm($connection, $formID);
			if ($result[0] == 0) {
				$form = $result[1];
				
				// Is the user actually allowed to view the form?
				$result = GetUserAccessLevel($connection, $userID);
				if ($form['userID']!=$userID and !$admin) {
					// nope
					$form = [];
				}
			}
		}
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
						<div class="carousel-item active" style="background-image:url('img/slide-31.jpg')">
							
							<h2>Digital Requisition Form</h2>
							<div class="table-responsive">
								<?php EchoFormHeader($form); ?>
								<table>
								  <thead>
									<tr>
									  <th>No</th>
									  <th>ITEM/ DESCRIPTION</th>
									  <th>QTY</th>
									  <th>Unit Price</th>
									  <th>Supplier</th>
									  <th>Remarks/ Date Required</th>
									</tr>
								  </thead>
								  <tbody>
									<?php EchoFormDetails($form); ?>
								  </tbody>
								</table>
								
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