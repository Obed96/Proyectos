<?php 
	session_start();
	if ($_GET["close"]=="ok") {
		session_destroy();
		header("Location:index.php");
	}

	//$base_url = "http://192.168.43.51/Web3/unity2/sesion/";
	$base_url = "http://localhost/sesion/";

	if (isset($_POST["addProduct"])) {
		$cod = $_POST["cod"];
		$name = $_POST["nombre"];
		$marca = $_POST["marca"];
		$cantidad = $_POST["cantidad"];
		$stock_max = $_POST["stock_max"];
		$stock_min = $_POST["stock_min"];
		$pre = $_POST["pre"];
		$url = $base_url."servidor_product.php?opcion=3&cod=".$cod."&nombre=".$name."&marca=".$marca."&cantidad=".$cantidad."&stock_max=".$stock_max."&stock_min=".$stock_min."&pre=".$pre;
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
			  Producto agregado con exito!!..
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";
	}

	if (isset($_POST["edit"])) {
		$cod = $_POST["cod"];
		$nombre = $_POST["nombre"];
		$marca = $_POST["marca"];
		$cantidad = $_POST["cantidad"];
		$stock_min = $_POST["stock_min"];
		$stock_max = $_POST["stock_max"];
		$pre = $_POST["pre"];
		$status = $_POST["status"];
		$status = str_replace(" ", "", $status);
		$status = trim($status);
		$url = $base_url."servidor_product.php?opcion=5&nombre=".$nombre."&cod=".$cod."&marca=".$marca."&cantidad=".$cantidad."&min=".$stock_min."&max=".$stock_max."&pre=".$pre."&status=".$status;
		
		$url = str_replace(" ", "%20", $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($ch);
		if ($out) {
			echo "<div class='alert alert-primary' role='alert'>
			   El registro ah sido actualizadpo con exito!!
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";
		} else {
			echo "<div class='alert alert-danger' role='alert'>
			   Error al actualizar el registro!!
			   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    			<span aria-hidden='true'>&times;</span>
  				</button>
			</div>";
		}
	}

	if (isset($_POST["change_status"])) {
		$cod = $_POST["cod"];
		$accion = $_POST["change_status"];
		$status = "";
		if ($accion == "Activar") {
			$status = "Activo"	;
		}else{
			$status = "Inactivo";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $base_url."servidor_product.php?opcion=4&cod=".$cod."&status=".$status);
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
			height: 400px;
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
 	 	<center><button type="button" class="btn btn-mg btn-success" data-toggle="modal" data-target="#addProduct" data-whatever="@mdo">Nuevo</button></center><br/>
 	 <div class="container" id="table">
 	 	<table class="table table-striped table-hover">
 	 		<thead>
 	 			<tr>
 	 				<th><div class="col-md-1">Codigo</div></th>
	 	 			<th><div class="col-md-4">Nombre</div></th>
	 	 			<th><div class="col-md-3">Cantidad</div></th>
	 	 			<th><div class="col-md-1">Marca</div></th>
	 	 			<th><div class="col-md-3">Acciones</div></th>
 	 			</tr>
 	 		</thead>
		<?php 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $base_url."servidor_product.php?opcion=2");
			curl_setopt($ch, CURLOPT_POST, true);
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
					<input type="hidden" name="cod" value="<?= $datos[0] ?>" readonly="">		
				</div>
				<div class="col-md-4">
					<input type="hidden" name="nombre" value="<?= $datos[1] ?>" readonly="">		
				</div>
				<div class="col-md-3">
					<input type="hidden" name="marca" value="<?= $datos[2] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="cantidad" value="<?= $datos[3] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="stock_max" value="<?= $datos[4] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="stock_min" value="<?= $datos[5] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="pre" value="<?= $datos[6] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="status" value="<?= $datos[7] ?>" readonly="">		
				</div>
				<tr>
					<!--
						0->cod
						1->nombre
						2->marca
						3->cantidad
						4->stock_max
						5->stock_min
						6->pre
						7->status
					-->
					<td class="col-md-1"><?= $datos[0] ?></td>
					<td class="col-md-4"><?= $datos[1] ?></td>
					<td class="col-md-2"><?= $datos[3] ?></td>
					<td class="col-md-1"><?php 
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_product.php?opcion=6&id_marca=".$datos[2]);
						curl_setopt($ch, CURLOPT_POST, true);

						//set the content type to application/json
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						    'Content-Type: application/json',
						    'Content-Length: ' . strlen($data_string))
						);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$nombre_marca = curl_exec($ch);
						$nombre_marca = explode("@", $nombre_marca);
						$nombre_marca = str_replace(";", "", $nombre_marca);
						echo $nombre_marca[1];
					?>
					</td>
					<td class="col-md-3">
				<div>
					<input type="submit" value="Detalles" class="btn btn-sm btn-light" name="more">
					<input type="submit" value="Modificar" class="btn btn-sm btn-warning" name="update">

					<?php 
						$accion = "";
						$datos[7] = trim($datos[7]);
						$estilo = "";

						if($datos[7]=="Inactivo"){
							$accion = "Activar";
							$estilo = "btn-success";
						} else{
							$accion = "Desactivar"; 
							$estilo = "btn-danger";
						}
					?>
					<input type="submit" value="<?= $accion ?>" class="btn btn-sm <?= $estilo ?>" name="change_status">
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
	
	<?php  if (isset($_POST["update"])) {?>
		<center>
			<div class="container">
				<form action="" method="post">
					<h4>Editar datos</h4>
					<div class="row col-md-4 form-group row" id="updateData">
						<div class="col-md-6 form-group">
							<label for="cod" class="col-form-label">Codigo de baras:</label>
							<input type="text" class="form-control" name="cod" value="<?= $_POST["cod"] ?>" readonly="">	
						</div>
						<div class="col-md-6 form-group">
							<label for="nombre" class="col-form-label">Nombre:</label>
							<input type="text" class="form-control" name="nombre" value="<?= $_POST["nombre"] ?>" >	
						</div>
						<div class="col-md-6 form-group">
							<label for="marca" class="col-form-label">Marca:</label>
				            <select class="form-control" name="marca" id="">
				            	<?php 
				            		$url = $base_url."servidor_product.php?opcion=1";

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
									$salida = explode(";", $salida);
									foreach ($salida as $key => $value) {
										$salida1 = explode("@", $value);
										if ($salida1[0] == $_POST["marca"]) {
											echo "<option  value='$salida1[0]' selected>$salida1[1]</option>";
										} else {
											echo "<option value='$salida1[0]'>$salida1[1]</option>";
										}	
									}
									
				            	 ?>
				            	
				            </select>
						</div>
						<div class="col-md-6 form-group">
							<label for="cantidad" class="col-form-label">Cantidad:</label>
							<input type="number" class="form-control" name="cantidad" value="<?= $_POST["cantidad"] ?>" >	
						</div>
						<div class="col-md-6 form-group">
							<label for="stock_max" class="col-form-label">Stock maximo:</label>
							<input type="number" class="form-control" name="stock_max" value="<?= $_POST["stock_max"] ?>" >	
						</div>
						<div class="col-md-6 form-group">
							<label for="stock_min" class="col-form-label">Stock minimo:</label>
							<input type="number" class="form-control" name="stock_min" value="<?= $_POST["stock_min"] ?>" >	
						</div>
						<div class="col-md-12 form-group">
							<label for="pre" class="col-form-label">Precio:</label>
							<input type="number" class="form-control" name="pre" value="<?= $_POST["pre"] ?>" >	
						</div>

						<input type="hidden" class="form-control" name="status" value="<?= $_POST["status"] ?>" >	
						
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
					<h4>Editar datos</h4>
					<div class="row col-md-4 form-group row" id="updateData">
						<div class="col-md-6 form-group">
							<label for="cod" class="col-form-label">Codigo de baras:</label>
							<input type="text" class="form-control" name="cod" value="<?= $_POST["cod"] ?>" readonly="">	
						</div>
						<div class="col-md-6 form-group">
							<label for="nombre" class="col-form-label">Nombre:</label>
							<input type="text" class="form-control" name="nombre" value="<?= $_POST["nombre"] ?>" readonly="">	
						</div>
						<div class="col-md-6 form-group">
							<label for="marca" class="col-form-label">Marca:</label>
				            	<?php 
				            		$url = "http://localhost/Web3/unity2/sesion/servidor_product.php?opcion=1";

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
									$salida = explode(";", $salida);
									foreach ($salida as $key => $value) {
										$salida1 = explode("@", $value);
										if ($salida1[0] == $_POST["marca"]) {
											echo "<input type='text' class='form-control' readonly  value='$salida1[1]' readonly/>";
										} else {
											//echo "<option value='$salida1[0]'>$salida1[1]</option>";
										}	
									}
									
				            	 ?>
						</div>
						<div class="col-md-6 form-group">
							<label for="cantidad" class="col-form-label">Cantidad:</label>
							<input type="number" class="form-control" name="cantidad" value="<?= $_POST["cantidad"] ?>" readonly="">	
						</div>
						<div class="col-md-6 form-group">
							<label for="stock_max" class="col-form-label">Stock maximo:</label>
							<input type="number" class="form-control" name="stock_max" value="<?= $_POST["stock_max"] ?>" readonly="">	
						</div>
						<div class="col-md-6 form-group">
							<label for="stock_min" class="col-form-label">Stock minimo:</label>
							<input type="number" class="form-control" name="stock_min" value="<?= $_POST["stock_min"] ?>" readonly="">	
						</div>
						<div class="col-md-12 form-group">
							<label for="pre" class="col-form-label">Precio:</label>
							<input type="number" class="form-control" name="pre" value="<?= $_POST["pre"] ?>" readonly="">	
						</div>
						<input type="hidden" class="form-control" name="pre" value="<?= $_POST["status"] ?>" >	
						
					</div>
				</form>
			</div>
		</center>
	<?php } ?>
	
	<?php }else{
		echo("No está registrado ");
	} ?>
	

		
	<div class="modal fade" id="addProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Crear nuevo producto</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form action="" method="post">
	        	<div class="form-group">
	            <label for="cod" class="col-form-label">Codigo de baras:</label>
	            <input type="text" class="form-control" id="cod" name="cod" maxlength="13" required="">
	          </div>
	          <div class="form-group">
	            <label for="nombre" class="col-form-label">Nombre:</label>
	            <input type="text" class="form-control" id="nombre" name="nombre" required="">
	          </div>
	          <div class="form-group">
	            <label for="marca" class="col-form-label">Marca:</label>
	            <select name="marca" id="">
	            	<?php 
	            		$url = $base_url."servidor_product.php?opcion=1";

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
						$salida = explode(";", $salida);
						foreach ($salida as $key => $value) {
							$salida1 = explode("@", $value);
							echo "<option value='$salida1[0]'>$salida1[1]</option>";	
						}
						
	            	 ?>
	            	
	            </select>
	          </div>
	          <div class="form-group">
	            <input type="hidden" value="0" class="form-control" id="cantidad" name="cantidad">
	          </div>
	          <div class="form-group">
	            <label for="stock_max" class="col-form-label">Stock maximo:</label>
	            <input type="number" class="form-control" id="stock_max" name="stock_max" required="">
	          </div>
	          <div class="form-group">
	            <label for="stock_min" class="col-form-label">Stock minimo:</label>
	            <input type="number" class="form-control" id="stock_min" name="stock_min" required="">
	          </div>
	          <div class="form-group">
	            <label for="pre" class="col-form-label">Precio:</label>
	            <input type="number" step="0.1" class="form-control" id="pre" name="pre" required="" min="1">
	          </div>
	          <div class="modal-footer">
		        <button type="submit" class="btn btn-primary" name="addProduct">Enviar</button>
		      </div>
	        </form>
	      </div>
	    </div>
	  </div>
	</div>
 </body>
 </html>