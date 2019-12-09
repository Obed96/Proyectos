<?php 
	session_start();
	$datos["usuarios"]["1"]["mail"] = "ulises@gmail.com";
	$datos["usuarios"]["1"]["pass"] = "123";
	$datos["usuarios"]["2"]["mail"] = "juan@gmail.com";
	$datos["usuarios"]["2"]["pass"] = "123";
	$datos["usuarios"]["3"]["mail"] = "jose@gmail.com";
	$datos["usuarios"]["3"]["pass"] = "123";
	$datos["usuarios"]["4"]["mail"] = "martin@gmail.com";
	$datos["usuarios"]["4"]["pass"] = "123";
	$datos["usuarios"]["5"]["mail"] = "maria@gmail.com";
	$datos["usuarios"]["5"]["pass"] = "123";

	$size = count($datos["usuarios"]);
		
	if (isset($_POST["enviar"])) {
		$emai = $_POST["mail"];
		$pass = $_POST["pass"];
		$flag = false;

		echo $emai."<br>";
		echo $pass."<br>";
		for ($i=1; $i <= $size; $i++) { 
			if ($emai == $datos["usuarios"][$i]["mail"] && $pass == $datos["usuarios"][$i]["pass"]) {
				$flag = true;
				break;
			}
		}
		if ($flag == true) {
			$_SESSION["email"] = $emai;	
			header("Location:panel.php");
		}else{
			header("Location:panel.php?mensaje=ok");	
		}	
	}

 ?>