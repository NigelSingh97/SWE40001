<?php
include("database_functions.php");

function EchoFormDetails(array $details) {
	/* expected array format:
	[
		'id'=>int,
		'timeSubmitted'=>string,
		'userID'=>int,
		'username'=>string,
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
	
	echo "<tr>
		<td>$id</td>
		<td>$timeSubmitted</td>
		<td>$username</td>
		<td>
			<span class=\"badge warning\">$approvalStatus</span>
		</td>
		<td>
			<span class=\"badge success\"><a href=\"p5.php?id=$id\">View</a></span>
		</td>
	</tr>";
}

session_start();

$userID = $_SESSION['id'];

if (isset($userID)) {
	$username = "";
	$connectionResult = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);
	if ($connectionResult[0] == 0) {
		$connection = $connectionResult[1];
		$result = GetUsername($connection, $userID);
		if ($result[0] == 0) $username = $result[1];
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
				<a class="btn btn-dark" href="logout.php">Logout</a>
			</nav>
		</header>
             <main role="main" class="home">
			
				<div id="carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">

                   <div class="carousel-item active" style="background-image:url('img/slide-31.jpg')">
								<h2>View Past Requisition Form</h2>
                                <div class="table-responsive02">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Form ID</th>
                                        <th>Order date</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php
									if ($connectionResult == 1) {
										echo("<tr><td>Database connection failed.</td></tr>");
									} else {
										$result = GetRequisitionForms($connection, ['approvedState'=>ApprovalStatus::ApprovedRejected]);
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