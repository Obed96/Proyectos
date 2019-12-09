<?php 
	session_start();
	$base_url = "http://localhost/sesion/";

	if (isset($_POST["search"])) {
		$id_producto = $_POST["id"];

		$ch = curl_init();
		$url = trim($base_url."servidor_product.php?opcion=7&cod=".$id_producto);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$producto = curl_exec($ch);
	}

	if (isset($_POST["save"])) {
		$existencia = $_POST["existencia"];
		$cod = $_POST["id"];
		$nombre = $_POST["nombre"];
		$marca = $_POST["marca"];
		$cantidad = $_POST["cantidad"] + $existencia;
		$stock_min = $_POST["stock_min"];
		$stock_max = $_POST["stock_max"];
		$pre = $_POST["precio"];
		$status = $_POST["status"];
		$status = str_replace(" ", "", $status);
		$status = trim($status);
		$url = $base_url."servidor_product.php?opcion=5&nombre=".$nombre."&cod=".$cod."&marca=".$marca."&cantidad=".$cantidad."&min=".$stock_min."&max=".$stock_max."&pre=".$pre."&status=".$status."&existencia=".$existencia;
		
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

 ?>

<!DOCTYPE html>
<html>
<head>
	<title>Inventario</title>
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
	<div class="container mt-4" align="center">
		<form class="form-inline my-2 my-lg-0" action="" method="POST">
	      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="id">
	      <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
	    </form>	
	</div> 	

	<?php  if (!isset($_GET["mensaje"])) { ?>
		<?php if($producto ){
			$producto = explode("@", $producto);
			$producto[7] = str_replace(";", "", $producto[7]);
			$producto[7] = trim($producto[7]);
			if ($producto[7] == "Activo") {
		?>
			<div class="container mt-5 col-md-3" align="center">
				<form action="" method="post">
					<input type="hidden" class="form-control" name="id" value="<?= $producto[0] ?>">
					<label for="nombre">Nombre</label>
					<input type="text" class="form-control" name="nombre" value="<?= $producto[1] ?>" readonly="">
					<label for="marca">Marca</label>
					<input type="text" class="form-control" name="marca" value="<?= $producto[2] ?>" readonly="">
					<label for="existencia">Existencia</label>
					<input type="number" class="form-control" name="existencia" value="<?= $producto[3] ?>" readonly>
					<label for="cantidad">Cantidad</label>
					<input type="number" class="form-control" min="1" name="cantidad">
					<input type="hidden" class="form-control" name="stock_max" value="<?= $producto[4] ?>" readonly="">
					<input type="hidden" class="form-control" name="stock_min" value="<?= $producto[5] ?>" readonly="">
					<label for="precio">Precio</label>
					<input type="text" class="form-control" name="precio" value="<?= $producto[6] ?>" readonly="">
					<label for="status">Status</label>
					<input type="text" class="form-control" name="status" value="<?= $producto[7] ?>" readonly="">
					<input type="submit" class="btn btn-success mt-3" value="Guardar" name="save">
				</form>	
			</div>
			
	<?php }
} ?>
	<?php } ?>
</body>
</html>