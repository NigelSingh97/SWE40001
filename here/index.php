<!DOCTYPE html>
<?php
include("database_functions.php");
$result = GetConnection("localhost", "root", "", "requisitionDatabase", 3306);

?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Hospital Login</title>
	<link rel="stylesheet" type="text/css" href="styles/style.css">
</head>
<body>
     <form class="loginform" action="login.php" method="post">
     	<h2>Hospital Login</h2>
     	<?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	<?php } ?>
     	<label class="loginlabel">User Name</label>
     	<input class="logininput" type="text" name="uname" placeholder="User Name"><br>

     	<label class="loginlabel">Password</label>
     	<input class="logininput" type="password" name="password" placeholder="Password"><br>

     	<button type="submit">Login</button>
     </form>
</body>
</html>
