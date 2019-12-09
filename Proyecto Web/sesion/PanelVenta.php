<?php 
	date_default_timezone_set("America/Mexico_City");
	session_start();
	if ($_GET["close"]=="ok") {
		session_destroy();
		header("Location:index.php");
	}
	//$base_url = "http://192.168.43.51/Web3/unity2/sesion/";
	$base_url = "http://localhost/sesion/";
	if(isset($_POST["devolver"])){
		$datosDevolver = $_POST["datosDevolver"];
		//echo($datosDevolver);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=66&info=".$datosDevolver);
		curl_setopt($ch, CURLOPT_POST, true);

		//set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);		
	}
	
 ?>



 <!DOCTYPE html>
 <html>
 <head>
 	<title></title>
 	<link rel="stylesheet" href="bootstrap/bootstrap.min.css">
 	<script type="text/javascript" src="bootstrap/jquery-3.2.1.js"></script>
	<script type="text/javascript" src="bootstrap/propper.1.12.9.js"></script>
	<script type="text/javascript" src="bootsrap/bootstrap.js"></script>
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
 	 	<center>
 	 		
 	 		<form  action="Panel_VentaDetalle.php">
 	 			<button type="submit" class="btn btn-success" >Nuevo</button>
 	 		</form>
 	 		
 	 	</center>
 	 	<br/><br />
 	<div class="container" id="table">
 	 	<table class="table table-striped table-hover">
 	 		<thead>
 	 			<tr>
 	 				<th><div class="col-md-1">Id</div></th>
	 	 			<th><div class="col-md-4">Cliente</div></th>
	 	 			<th><div class="col-md-3">Total</div></th>
	 	 			<th><div class="col-md-1">Fecha</div></th>
	 	 			<th><div class="col-md-3">Acciones</div></th>
 	 			</tr>
 	 		</thead>
		<?php 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=2");
			curl_setopt($ch, CURLOPT_POST, true);

			//set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($data_string))
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida = curl_exec($ch);
			$salida = explode("$", $salida);
			$fecha =  strftime( "%d%m%Y", time() );

			foreach ($salida as $key => $value) {
				$datos = explode("|", $value);
				if ($datos[0] != "") {
					$datos_venta = explode("@", $datos[0]);	
					$datos_venta[0] = trim($datos_venta[0]);
					$current_date = explode("-", $datos_venta[3]);
					$current_date = $current_date[0].$current_date[1].$current_date[2];
					if ($datos_venta[0]!="" && $fecha == $current_date) {
						
			?>
			<form action="" method="post">
			<div class="row">
				<div class="col-md-1">
					<input type="hidden" name="id" value="<?= $datos_venta[0] ?>" readonly="">		
				</div>
				<div class="col-md-4">
					<input type="hidden" name="cliente" value="<?= $datos_venta[1] ?>" readonly="">		
				</div>
				<div class="col-md-3">
					<input type="hidden" name="total" value="<?= $datos_venta[2] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="fecha" value="<?= $datos_venta[3] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="detalles" value="<?= $datos[1] ?>" readonly="">		
				</div>
				</div>
				<tr>
					<td class="col-md-1"><?= $datos_venta[0] ?></td>
					<td class="col-md-4"><?php 
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=4&id_cliente=".$datos_venta[1]);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$salida = curl_exec($ch);
						echo($salida);
					 ?></td>
					<td class="col-md-3"><?= $datos_venta[2] ?></td>
					<td class="col-md-2"><?= $datos_venta[3] ?></td>
					<td class="col-md-1">
				<div>
					<input type="submit" value="Detalles" class="btn btn-sm btn-light" name="more">
				</div></td>
				</tr>
			</div>
			</form>
			<?php
					}
				}
			}
		 	?>	
 		</table>
	</div>	


	<?php  if (isset($_POST["more"])) { ?>
	<br>
	<center>
		<div class="container" id="table">
		<form action="" method="post">			
 	 	<table class="table table-striped table-hover">
 	 		<thead>
 	 			<tr>
 	 				<th><div class="col-md-1">Id</div></th>
	 	 			<th><div class="col-md-4">Cantidad</div></th>
	 	 			<th><div class="col-md-3">Producto</div></th>
	 	 			<th><div class="col-md-1">Costo</div></th>
	 	 			<th><div class="col-md-3">Subtotal</div></th>
 	 			</tr>
 	 		</thead>
		<?php 
			
			$detalles = $_POST["detalles"];
			
			$salida = explode(";", $detalles);
			$devoluciones = "";
			foreach ($salida as $key => $value) {
				$datos = explode("@", $value);
				if ($datos[0] != "") {
						
			?>
			
			<div class="row">
				<div class="col-md-1">
					<input type="hidden" name="id" value="<?= $datos[0] ?>" readonly="">		
				</div>
				<div class="col-md-4">
					<input type="hidden" name="cantidad" value="<?= $datos[1] ?>" readonly="">		
				</div>
				<div class="col-md-3">
					<input type="hidden" name="producto" value="<?= $datos[2] ?>" readonly="">		
				</div>
				<?php $devoluciones.=$datos[2]."@".$datos[1].";" ?>
				<div class="col-md-1">
					<input type="hidden" name="costo" value="<?= $datos[3] ?>" readonly="">		
				</div>
				<div class="col-md-1">
					<input type="hidden" name="subtotal" value="<?= $datos[4] ?>" readonly="">		
				</div>
				</div>
				<tr>
					<td class="col-md-2"><?= $datos[0] ?></td>
					<td class="col-md-1"><?= $datos[1] ?></td>
					<td class="col-md-4"><?php 
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=3&id_producto=".$datos[2]);
						curl_setopt($ch, CURLOPT_POST, true);

						//set the content type to application/json
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						    'Content-Type: application/json',
						    'Content-Length: ' . strlen($data_string))
						);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$salida = curl_exec($ch);
						echo $salida;
					?></td>
					<td class="col-md-2"><?= $datos[3] ?></td>
					<td class="col-md-3"><?= $datos[4] ?></td>
				</tr>

			</div>
		
			<?php
				}

					
				
			}
			$url = 	$base_url."servidor_devolucion.php?opcion=4&id=".$_POST['id'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida = curl_exec($ch);
			$salida = explode(";", $salida);
			foreach ($salida as $key => $value) {
				$producto = explode("@", $value);
				$producto[0] = trim($producto[0]);

				if ($producto[0]!="$") { 
			?>

			<tr style="background: #F39720">
				<td ><?= $producto[2]  ?></td>
				<td><?= $producto[1]  ?></td>
				<td><?php
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=3&id_producto=".$producto[2]);
						curl_setopt($ch, CURLOPT_POST, true);

						//set the content type to application/json
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						    'Content-Type: application/json',
						    'Content-Length: ' . strlen($data_string))
						);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$salida = curl_exec($ch);
						echo $salida;
				  ?></td>
				<td><?= $producto[3]  ?></td>
				<td><?= $producto[4]  ?></td>	
			</tr>
			

			<?php
					
				}
			}
		 	?>	
 		</table>
 		<div class="row">
 			<label for="id_venta" class="col-md-1">Id</label>
	 		<input type="text" class="form-control col-md-1" name="id_venta" value="<?= $_POST['id'] ?>" readonly="">
	 		<label for="cliente" class="col-md-1">Cliente</label>
	 		<input type="hidden" name="cliente" class="col-md-3" value="<?= $_POST['cliente'] ?>" readonly="">	
	 		<input type="text" name="cliente_nombre" class="col-md-3" value="<?php
	 		 	$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=4&id_cliente=".$_POST['cliente'] );
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$salida = curl_exec($ch);
				echo $salida;
	 		 ?>" readonly="">	
	 		<label for="total" class="col-md-1">Total</label>
	 		<input type="text" name="total" class="col-md-1" value="<?= $_POST['total'] ?>" readonly="">	
	 		<label for="fecha" class="col-md-1">fecha</label>
	 		<input type="text" name="fecha" class="col-md-2" value="<?= $_POST['fecha'] ?>" readonly="">	

	 		<input type="hidden" name="datosDevolver" class="col-md-2" value="<?= $_POST['id']."|".$devoluciones ?>" readonly="">	
			 <div class="col text-center mt-3">
      			<input type="submit" name="devolver" class="col-md-6 btn btn-warning " value="Cancelar">	
		    </div>	
 		</div>
 		
 			</form>
	</div>	
	</center>
	<?php } ?>
	
	<?php }else{
		echo("No está registrado ");
	} ?>

</body>
</html>