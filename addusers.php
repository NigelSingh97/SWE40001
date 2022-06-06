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
		<meta name="description" content="Hospital Admin page" />
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
							

 												<h2>Add New Users</h2>
                         <form action="adduser.php" class="rcorners">
  													<br><br>
  													<center>
  													<div>
											    <label for="uname" class="lbl">UserName:</label>
												    <input type="text" id="uname" name="uname" class="rcorners1" required>
												  </div>
												</center>
												  <br>
												  <br>
												  <center><div>
												  <label for="type" class="lbl">User Type:</label>
												  <select id="type" name="type" class="rcorners1" style="width:150px">
												    <option value="Regular User">Regular User</option>
												    <option value="Executive">Executive</option>
												    <option value="Validator">Validator</option>
												    <option value="Approver">Approver</option>
												  </select>
												</div> </center>
												<br> <br>
												<center><div>
												  <label for="pwd" class="lbl">Password:</label>
												  <input type="password" id="pwd" name="pwd" class="rcorners1" required >
												 </div> </center>
												<br><br>

												  <center><input type="submit" class="buttonrcorners" value="Add User"></center>
													</form> 
													<br><br>
                                <div class="table-responsive">
                                <form action="p1.php" method="post">
                                <table id="table">
                                  <thead>
                                    <tr>
                                    	<th>No</th>
                                      <th>UserName</th>
                                      <th>Password</th>
                                      <th>UserType</th>
                                      
                                      <th>Edit</th>
                                      <th>Delete</th>
                                    </tr>
                                  </thead>
                                  <tbody>
									 <?php
        $conresult = GetConnection("localhost", "root", "", "test_db", 3306);
        $conn = $conresult[1];
        $sql = "select * from Users where admin='0'";
        $result = $conn->query($sql);
        echo "Error ".$conn->error; 
        if ($result->num_rows > 0) {
        	$id = 1;
          while($row = $result->fetch_assoc()) {
          	$uname = $row["userName"];
          	$password = $row["password"];
            $type = $row["type"];
         
          ?>
          <tr>
          	<td><?=$id;?></td>
            <td><?=$uname;?></td>
            <td><?=$password;?></td>
            <td><?=$type; ?></td>

          <td>
            <a style="cursor: pointer;" href="edituser.php?uname=<?php echo $uname;?>&pwd=<?php echo $password;?>&type=<?php echo $type;?>" >
                Edit
            </a>
          </td>
          
         <td>
              <a href= "deleteuser.php?uname=<?php echo $uname;?>&pwd=<?php echo $password;?>" 
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