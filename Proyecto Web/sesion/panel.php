<?php 
	session_start();
	if ($_GET["close"]=="ok") {
		session_destroy();
		header("Location:index.php");
	}

	//$base_url = "192.168.43.51/Web3/unity2/sesion/";
	$base_url = "http://localhost/sesion/";

	if (isset($_POST["addUser"])) {
		$name = $_POST["name"];
		$address = $_POST["address"];
		$phone = $_POST["phone"];
		$url = $base_url."servidor_2.php?opcion=2&name=".$name."&address=".$address."&phone=".$phone;
		$url = str_replace(" ", "%20", $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);

		//set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);
		echo "<div class='alert alert-primary' role='alert'>
			  Usuario agregado con exito!!..
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";
	}

	if (isset($_POST["edit"])) {
		$name = $_POST["name"];
		$address = $_POST["address"];
		$phone = $_POST["phone"];
		$id = $_POST["id"];
		$url = $base_url."servidor_2.php?opcion=3&name=".$name."&address=".$address."&phone=".$phone."&id=".$id;

		$url = str_replace(" ", "%20", $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);

		//set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);
		echo "<div class='alert alert-primary' role='alert'>
			  El registro ah sido editado con exito!!
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";	
	}

	if (isset($_POST["delete"])) {
		$id = $_POST["id"];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $base_url."servidor_2.php?opcion=4&id=".$id);
		curl_setopt($ch, CURLOPT_POST, true);

		//set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);
		echo "<div class='alert alert-primary' role='alert'>
			  eliminado con exito
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";		
	} 

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title></title>
 	<link rel="stylesheet" href="bootstrap/bootstrap.min.css">
 	<script type="text/javascript" src="bootstrap/jquery-3.2.1.js"></script>
	<script type="text/javascript" src="bootstrap/propper.1.12.9.js"></script>
	<script type="text/javascript" src="bootstrap/bootstrap.js"></script>
	<style type="text/css">
		body{
			background: #F0F0DF;
		}
		#updateData{
			background: #DDDBD5;
		}
		#list{
			background: #E6E3E3;
			border: 1px solid #a49545;
		}
		#table{
			width: 100%;
			height: 500px;
			overflow: auto;
		}
		table{
			border: 2px solid #a49545;
			border-radius: 2%;
		}
	</style>
 </head>
 <body>
 	<?php include 'menu.php'; ?>


 	 <?php  if (!isset($_GET["mensaje"])) { ?>
 	 	<br/>
 	 	<center><button type="button" class="btn btn-mg btn-success" data-toggle="modal" data-target="#addUser" data-whatever="@mdo">Nuevo</button></center><br/>
 	 <div class="container" id="table">
 	 	<table class="table table-striped table-hover">
 	 		<thead>
 	 			<tr>
 	 				<th><div class="col-md-1">Id</div></th>
	 	 			<th><div class="col-md-4">Nombre</div></th>
	 	 			<th><div class="col-md-3">Direccion</div></th>
	 	 			<th><div class="col-md-1">Telefono</div></th>
	 	 			<th><div class="col-md-3">Acciones</div></th>
 	 			</tr>
 	 		</thead>
		<?php 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $base_url."servidor_2.php?opcion=1");
			curl_setopt($ch, CURLOPT_POST, true);

			//set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($data_string))
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida = curl_exec($ch);
			$salida = explode(";", $salida);
			foreach ($salida as $key => $value) {
				$datos = explode("@", $value);
				if ($datos[0] != "") {
			?>
			<form action="" method="post">
			<div class="row">
				<div class="col-md-1">
					<input type="hidden" name="id" value="<?= $datos[0] ?>" readonly="">		
				</div>
				<div class="col-md-4">
					<input type="hidden" name="name" value="<?= $datos[1] ?>" readonly="">		
				</div>
				<div class="col-md-3">
					<input type="hidden" name="address" value="<?= $datos[2] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="phone" value="<?= $datos[3] ?>" readonly="">		
				</div>
				<tr>
					<td class="col-md-1"><?= $datos[0] ?></td>
					<td class="col-md-4"><?= $datos[1] ?></td>
					<td class="col-md-3"><?= $datos[2] ?></td>
					<td class="col-md-1"><?= $datos[3] ?></td>
					<td class="col-md-3">
				<div>
					<input type="submit" value="Detalles" class="btn btn-sm btn-light" name="more">
					<input type="submit" value="Modificar" class="btn btn-sm btn-warning" name="update">
					<input type="submit" value="Eliminar" class="btn btn-sm btn-danger" name="delete">
				</div></td>
				</tr>
			</div>
			</form>
			<?php
				}
			}
		 	?>	
 		</table>
	</div>	
	
	<?php  if (isset($_POST["update"])) { ?>
		<center>
			<div class="container">
				<form action="" method="post">
					<h4>Editar datos</h4>
					<div class="row col-md-4 form-group row" id="updateData">
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="id" value="<?= $_POST["id"] ?>" readonly="">	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="name" value="<?= $_POST["name"] ?>" >	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="address" value="<?= $_POST["address"] ?>" >	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="phone" value="<?= $_POST["phone"] ?>" >	
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" align="center">
							<input type="submit" value="Guardar" class="btn btn-mg btn-success" name="edit">
						</div>
					</div>
				</form>
			</div>
		</center>
	<?php } ?>

	<?php  if (isset($_POST["more"])) { ?>
		<center>
			<div class="container">
				<form action="" method="post">
					<h4>Detalles</h4>
					<div class="row col-md-4 form-group row" id="updateData">
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="id" value="<?= $_POST["id"] ?> "  readonly="">	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="name" value="<?= $_POST["name"] ?>" readonly="">	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="address" value="<?= $_POST["address"]  ?>" readonly="">	
						</div>
						<div class="col-md-12 form-group">
							<input type="text" class="form-control" name="phone" value="<?= $_POST["phone"] ?>" readonly="">	
						</div>
					</div>
				</form>
			</div>
		</center>
	<?php } ?>
	
	<?php }else{
		echo("No está registrado ");
	} ?>
	

		
	<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Crear nuevo usuario</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form action="panel.php" method="post">
	          <div class="form-group">
	            <label for="name" class="col-form-label">Nombre:</label>
	            <input type="text" class="form-control" id="name" name="name" required="">
	          </div>
	          <div class="form-group">
	            <label for="address" class="col-form-label">Direcion:</label>
	            <input type="text" class="form-control" id="address" name="address" required="">
	          </div>
	          <div class="form-group">
	            <label for="phone" class="col-form-label">Telefono:</label>
	            <input type="text" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" required="">
	          </div>
	          <div class="modal-footer">
		        <button type="submit" class="btn btn-primary" name="addUser">Enviar</button>
		      </div>
	        </form>
	      </div>
	    </div>
	  </div>
	</div>
 </body>
 </html>