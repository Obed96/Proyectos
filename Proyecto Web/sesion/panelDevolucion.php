<?php 
	session_start();
	$_SESSION["detalleVenta"];
	$_SESSION["venta"];
	$_SESSION["devolver_producto"];
	$_SESSION["id_venta"];
	$base_url = "http://localhost/sesion/";

	if ($_GET["close"]=="ok") {
		session_destroy();
		header("Location:index.php");
	}

	//$base_url = "http://192.168.43.51/Web3/unity2/sesion/";
	$base_url = "http://localhost/sesion/";

	if (isset($_POST["search"])) {
		$id = $_POST["id"];
		$url = $base_url."servidor_devolucion.php?opcion=3&id=".$id;
		$url = str_replace(" ", "%20", $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);
		unset($_SESSION["venta"]);
		unset($_SESSION["detalleVenta"]);
		unset($_SESSION["devolver_producto"]);
		if ($salida) {
			$_SESSION["id_venta"] = $id;
			$url = $base_url."servidor_devolucion.php?opcion=1&id=".$id;
			$url = str_replace(" ", "%20", $url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida = curl_exec($ch);
			$_SESSION["venta"] = $salida;
			if (empty($salida) ) {
				echo "<div class='alert alert-danger' role='alert'>
				  Folio no encontrado!!..
				   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
	    			<span aria-hidden='true'>&times;</span>
	  				</button>
				</div>";
			}

		}else{
			echo "<div class='alert alert-warning' role='alert'>
				  Folio no encontrado o con devolucion registrada!!..
				   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
	    			<span aria-hidden='true'>&times;</span>
	  				</button>
				</div>";
		}
		
		
	}

	if (isset($_POST["product"])) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $base_url."servidor_product.php?opcion=7&cod=".$_POST["product"]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$product = curl_exec($ch);
		$product = explode("@", $product);
	}
	if (isset($_POST['asignar'])){
		$alert = "";
		if (intval($_POST["cantidad"]) <= intval($_POST["cant"])) {
			$id = $_SESSION["id"];
			$_SESSION["id"] = $id+1;
			$sub_total = intval($_POST["cantidad"]) * floatval($_POST["pu"]);
			$salida = $_SESSION["id"]."@".$_POST["cantidad"]."@".$_POST["product"]."@".$_POST["pu"]."@".$sub_total.";";
			$detalle = $_SESSION["detalleVenta"];
			$detalle .= $salida; 
			$_SESSION["detalleVenta"] = $detalle;
		}else{
			$alert = "<div class='alert alert-danger' role='alert'>
					  No existe cantidad suficiente;
					   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		    			<span aria-hidden='true'>&times;</span>
		  				</button>
					</div>";
		}	
	}



	if (isset($_POST['delete'])) {
		$id = $_POST['id'];
		$salida = $_SESSION["detalleVenta"];
		$salida = explode(";", $salida);
		$aux = "";
		for($i = 0; $i<count($salida); $i++) {
			$datos = explode("@", $salida[$i]);
			if ($datos[0] == $id) {
			}else{
			$aux .= $salida[$i].";";
			}
		}
		$_SESSION["detalleVenta"] = $aux;
	}

	if (is_string($_POST["return"])) {
		$_SESSION["devolver_producto"] .= $_POST["devolver_producto"].";";
		$dev_producto = explode("@", $_POST["devolver_producto"]);
		$venta = explode("|", $_SESSION["venta"]);
		$venta_productos = $venta[1];
		$venta_productos = explode(";", $venta_productos);
		$aux = "";
		for ($i=0; $i < count($venta_productos)-1 ; $i++) { 
			$producto = explode("@", $venta_productos[$i]);
			if ($producto[0] == $dev_producto[0]) {
			 	
			 }else{
			 	$aux .= $venta_productos[$i].";";
			 } 
		}
		$_SESSION["venta"] = $venta[0]."|".$aux;
	}


	/*redifiniendo el pago*/
	$total_venta = explode("|", $_SESSION["venta"]);
	$venta = explode("@", $total_venta[0]);
	$total_venta = $venta[2];
	$total_nuevo = 0.0;
	if ($_SESSION["detalleVenta"]) {
		$productos = explode(";", $_SESSION["detalleVenta"]);
		for ($i=0; $i < count($productos) ; $i++) { 
			$producto = explode("@", $productos[$i]);
			$total_nuevo = $total_nuevo + $producto[4];
		}
	}
	if ($_SESSION["devolver_producto"]) {
		$productos = explode(";", $_SESSION["devolver_producto"]);
		for ($i=0; $i < count($productos) ; $i++) { 
			$producto = explode("@", $productos[$i]);
			$total_nuevo = $total_nuevo - $producto[4];
		}
	}
	$total = $total_venta + $total_nuevo;
	$regresar = 0.0;
	$cobrar = 0.0;
	if ($total < $total_venta) {
		$regresar = $total_venta-$total;
	}elseif ($total > $total_venta) {
		$cobrar = $total - $total_venta;
	}


	if (isset($_POST["save"])) {
		$url = $base_url."servidor_devolucion.php?opcion=3&id=".$_SESSION["id_venta"];
		$url = str_replace(" ", "%20", $url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida_devolucion = curl_exec($ch);
		if ($salida_devolucion) {
			$ch = curl_init();
			$url = $base_url."servidor_devolucion.php?opcion=2&venta=".$_SESSION["venta"]."&nuevos=".$_SESSION["detalleVenta"]."&devueltos=".$_SESSION["devolver_producto"];
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida = curl_exec($ch);
			header("PanelVenta.php");
		}else{
			var_dump($salida_devolucion);
			echo("entro");
			echo "<div class='alert alert-warning' role='alert'>
				  Folio con devolucion registrada!!..
				   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
	    			<span aria-hidden='true'>&times;</span>
	  				</button>
			</div>";
			header("PanelVenta.php");
		}
		
		unset($_SESSION["venta"]);
		unset($_SESSION["detalleVenta"]);
		unset($_SESSION["devolver_producto"]);
		unset($_SESSION["id_venta"]);
		header("PanelVenta.php");
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
 	 
 		<div class="container mt-4" align="center">
			<form class="form-inline my-2 my-lg-0" action="" method="POST">
		      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="id">
		      <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
		    </form>	
		</div> 	
 	 
	
 	 

 	 <?php  if (!isset($_GET["mensaje"])) { ?>
 	 
 	 <div class="container">
 		<table class="table table-striped table-hover mt-4">
 			
			<tr>
				<th>Nombre</th>
				<th>Cantidad</th>
				<th>Costo/u</th>
				<th>SubTotal</th>
				<th>Accion</th>
			</tr>
			
			<?php 
				if (!$salida_devolucion) {
				$venta = explode("|", $_SESSION["venta"]);
				$detalle = explode("@", $venta[0]);
				?>
				<div>
					<p>Usuario: <?php
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=4&id_cliente=".$detalle[1]);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$salida = curl_exec($ch);
						echo($salida);
					?></p>
					<p>Precio $: <?= $detalle[2] ?></p>
					<p>Fecha: <?= $detalle[3] ?></p>
				</div>
				<?php
				$venta_productos = $venta[1];
				$venta_productos = explode(";", $venta_productos);
				for ($i=0; $i < count($venta_productos)-1 ; $i++) { 
					$producto = explode("@", $venta_productos[$i]); 
					
					?>

					<form action="" method="POST">
 						<tr>
 							<input type="hidden" name="devolver_producto" value="<?php echo $venta_productos[$i]; ?>">
 							<input type="hidden" name="devolver" value="1">
							<td><?php 
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=3&id_producto=".$producto[2]);
								curl_setopt($ch, CURLOPT_POST, true);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								$salida = curl_exec($ch);
								echo $salida;
							 ?></td>
							<td><?php echo $producto[1]; ?></td>
							<td><?php echo $producto[3]; ?></td>
							<td><?php echo $producto[4]; ?></td>
							<td >
							<div>
							<input type="submit" value="Devolver" class="btn btn-sm btn-warning" name="return">
							</div></td>
						</tr>
 					</form>

				
			<?php } ?>
				<tr>
					<table style="border:none;">
						<?php  
			 	 			$salida = $_SESSION["detalleVenta"];
			 	 			$salida = explode(";", $salida);
			 	 			$total = 0.0;
			 	 			foreach ($salida as $key => $value) {
			 	 				$datos = explode("@", $value);
			 	 				if ($datos[0] != "") {
			 	 					$total = $total + $datos[4];

			 	 		?>
			 	 		<form method="POST">
								<div class="col-md-4">
									<input type="hidden" name="id" value="<?= $datos[0] ?>" readonly="">
									<input type="hidden" name="cantidad" value="<?= $datos[1] ?>" readonly="">		
								</div>
								<div class="col-md-3">
									<input type="hidden" name="producto" value="<?= $datos[2] ?>" readonly="">		
								</div>
								<div class="col-md-1">
									<input type="hidden" name="pu" value="<?= $datos[3] ?>" readonly="">		
								</div>
								<div class="col-md-1">
									<input type="hidden" name="subtotal" value="<?= $datos[4] ?>" readonly="">		
								</div>

								<tr>
									<?php
										$ch = curl_init();
										$url = trim($base_url."servidor_product.php?opcion=7&cod=".$datos[2]);
										curl_setopt($ch, CURLOPT_URL, $url);
										curl_setopt($ch, CURLOPT_POST, true);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										$salida = curl_exec($ch);
										$pro = explode("@", $salida);
										if ($flag) {
											
										
									?>
									<td class="col-md-4 table-danger"><?= $pro[1] ?></td>
									<td class="col-md-1 table-danger"><?= $datos[1] ?></td>
									<td class="col-md-3 table-danger"><?= $datos[3] ?></td>
									<td class="col-md-1 table-danger"><?= $datos[4] ?></td>
									<td class="col-md-1 table-danger" style="<?= $text ?>">
										<p>Producto no vendido</p>
									</td>
									<?php }else{?>
										<td class="col-md-4"><?= $pro[1] ?></td>
										<td class="col-md-1"><?= $datos[1] ?></td>
										<td class="col-md-3"><?= $datos[3] ?></td>
										<td class="col-md-1"><?= $datos[4] ?></td>
										<td class="col-md-1" style="<?= $ac ?>">
											<input type="submit" value="Eliminar" class="btn btn-sm btn-danger" name="delete">
										</td>
									<?php }?>
								</tr>
				 	 		</form>
			 	 		<?php
			 	 				}
			 	 			}
			 	 			if ($flag) {
			 	 				unset($_SESSION["detalleVenta"]);
			 	 			}
			 	 		?>
						<form action="" method="post" name="formulario">
							<div class="row">		
								<tr>
									<td >
										<select name="product" class="form-control" onchange="formulario.submit();">
										<option value="0">.:Seleccione un producto:.</option>
										<?php 
										$ch = curl_init();
										curl_setopt($ch, CURLOPT_URL, $base_url."servidor_product.php?opcion=2");
										curl_setopt($ch, CURLOPT_POST, true);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										$salida = curl_exec($ch);
										$salida = explode(";", $salida);
										foreach ($salida as $key => $value) {
											$datos = explode("@", $value);
											$activo = trim($datos[7]);
											if ($datos[0] != "" && $activo == "Activo") {
												$selected = '';
												if ($datos[0] == $_POST['product']) {
													$selected = 'selected';
												}
										?>
											<option value="<?= $datos[0] ?>" <?= $selected ?>><?= $datos[1] ?></option>
											
							            <?php
							            	}
							            }
							            ?>
						            	</select>
									</td>
									<td ><input type="number" class="form-control" id="cantidad" name="cantidad" required=""  min="1" max="<?= $product[3] ?>" title="tienes <?= $product[3] ?>"></td>
									<td class="col-md-2">
										<input type="hidden" name="cont" value='<?php echo $cont; ?>'>
										<input type="number" id="pu" name="pu"  value="<?= $product[6] ?>" readonly>
										<input type="hidden" name="cant" value="<?= $product[3] ?>" readonly="">
									</td>
									
									<td class="col-md-2">
										<div>
											<input type="submit" value="Asignar" class="btn btn-primary" name="asignar">	
										</div>
									</td>
								</tr>
							</div>
						</form>
					</table>
				</tr>
				
		</table>
		<table class="table table-striped table-hover mt-4">
			<tr>
				<th>Producto</th>
				<th>cantidad</th>
				<th>p/u</th>
				<th>sub total</th>
			</tr>
			<?php
				$devolver = explode(";", $_SESSION["devolver_producto"]);
				for ($i=0; $i < count($devolver) ; $i++) { 
					$product = explode("@", $devolver[$i]);		
			?>
			<tr>
				<td><?php 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $base_url."servidor_venta.php?opcion=3&id_producto=".$product[2]);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$salida = curl_exec($ch);
					echo $salida;
				 ?></td>
				<td><?php echo $product[1]; ?></td>
				<td><?php echo $product[3]; ?></td>
				<td><?php echo $product[4]; ?></td>
			</tr>
			<?php } ?>
		</table>

		<center>
			<label>Regresar:$ <?php echo $regresar; ?></label>
			<label>Cobrar:$ <?php echo $cobrar; ?></label>
			<form method="POST">
				<input type="submit" name="save" value="Guardar" class="btn btn-success">
			</form>
		</center>
 	
 	 </div>
	
	
	<?php
		}
	 }else{
		echo("No está registrado ");
	} ?>
	
 </body>
 </html>