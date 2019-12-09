<?php 
	session_start();
	if (isset($_SESSION["email"])) {
		header("Location:panel.php");
	}
 ?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="boostrap/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<form action="verifica.php" method="post">
					<div class="row"><input type="email" name="mail" placeholder="email"> <br></div>
					<div class="row"><input type="password" name="pass" placeholder="password"> <br></div>
					<div class="row"><input type="submit" name="enviar" value="enviar" class="btn btn-success"></div>
				</form>			
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
</body>
</html>
