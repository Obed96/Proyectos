<?php 
	session_start();

	$_SESSION["detalleVenta"];
	$_SESSION["unsold_products"];
	$_SESSION["id"];

	$ac = "";
	$text = "display: none;";
	$flag = false;
	//$base_url = "http://localhost/Web3/unity2/sesion/";
	$base_url = "http://localhost/sesion/";
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
			$sub_total = intval($_POST["cantidad"]) * floatval($_POST["pu"]);
			$products_sale = $_SESSION["detalleVenta"];
			if ($products_sale) {
				$products_sale = explode(";", $products_sale);
				$line_aux = "";
				$flag_sale = false;
				for ($i=0; $i < count($products_sale)-1; $i++) { 
					$product_sale = explode("@", $products_sale[$i]);
					if ($product_sale[2] == $_POST["product"]) {
						$flag_sale = true;
						$line_aux .= $product_sale[0]."@".($_POST["cantidad"] + $product_sale[1])."@".$product_sale[2]."@".$product_sale[3]."@".($sub_total + $product_sale[4]).";";
					}else{
						$line_aux .= $products_sale[$i].";";
					}
				}
			}
			if ($flag_sale) {
				$_SESSION["detalleVenta"] = $line_aux;
			} else {
				$id = $_SESSION["id"];
				$_SESSION["id"] = $id+1;
				$salida = $_SESSION["id"]."@".$_POST["cantidad"]."@".$_POST["product"]."@".$_POST["pu"]."@".$sub_total.";";
				$detalle = $_SESSION["detalleVenta"];
				$detalle .= $salida; 
				$_SESSION["detalleVenta"] = $detalle;
			}
			
			
			//var_dump($_SESSION["detalleVenta"]);
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

	if (isset($_POST["guardarVenta"])) {
		if (isset($_SESSION["detalleVenta"])) {
			$ch = curl_init();
			$url = $base_url."servidor_venta.php?opcion=1&id_user=".$_POST["usuario"]."&total=".$_POST["total"]."&detalle_venta=".$_SESSION["detalleVenta"];
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$salida_venta = curl_exec($ch);

			if ($salida_venta != -1) {
					if ($salida_venta == ";") {
						unset($_SESSION["detalleVenta"]);
						$alert = "<div class='alert alert-primary' role='alert'>
						  Venta agregada con exito!!
						   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			    			<span aria-hidden='true'>&times;</span>
			  				</button>
						</div>";
					} else {
						unset($_SESSION["detalleVenta"]);
						$alert = "<div class='alert alert-primary' role='alert'>
						  Venta agregada con exito (Aunque unos productos no se pudieron vender a falta de existencia en el inventario)!!
						   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			    			<span aria-hidden='true'>&times;</span>
			  				</button>
						</div>";
						$_SESSION["detalleVenta"] = $salida_venta;
						$ac = "display: none;";
						$text = "";
						$flag = true;
					}
					
			}else{
				$alert = "<div class='alert alert-danger' role='alert'>
						 Error al agregar la venta!!;
						   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			    			<span aria-hidden='true'>&times;</span>
			  				</button>
						</div>";
			}	
		}else{
			$alert = "<div class='alert alert-danger' role='alert'>
						No tienes productos registrados!!;
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

 	<br/>
 	<?php echo $alert; ?>
 	<div class="container" id="table">	
 	 	<table class="table table-striped table-hover">
 	 		<thead>
 	 			<tr>
 	 				<th><div class="col-md-1">Producto</div></th>
	 	 			<th><div class="col-md-2">Cantidad</div></th>
	 	 			<th><div class="col-md-1">Pu</div></th>
	 	 			<th><div class="col-md-2">SubTotal</div></th>
	 	 			<th><div class="col-md-6">Acción</div></th>
 	 			</tr>
 	 		</thead>
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
			<form method="POST" name="formulario">
				<div class="row">		
					<tr>
						<td class="col-md-3">
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
						<td class="col-md-1"><input type="number" class="form-control" id="cantidad" name="cantidad" required=""  min="1" max="<?= $product[3] ?>" title="tienes <?= $product[3] ?>"></td>
						<td class="col-md-2">
							<input type="hidden" name="cont" value='<?php echo $cont; ?>'>
							<input type="number" class="form-control" id="pu" name="pu"  value="<?= $product[6] ?>" readonly>
							<input type="hidden" name="cant" value="<?= $product[3] ?>" readonly="">
						</td>
						
						<td class="col-md-3">
							<div>
								<input type="submit" value="Asignar" class="btn btn-primary" name="asignar">	
							</div>
						</td>
					</tr>
				</div>
			</form>	
		</table>
	</div>
	<center>
		<div class="container">
			<form method="post">
				<h4>Datos de la Venta</h4>
				<div class="row col-md-4 form-group" id="updateData">
					<div class="form-group col-md-12">
						<label for="cod" class="" >Cliente:</label>
						<select name="usuario" class="form-control">
						<?php 
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $base_url."servidor_2.php?opcion=6");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$salida = curl_exec($ch);
						$salida = explode(";", $salida);
						foreach ($salida as $key => $value) {
							$datos = explode("@", $value);
							if ($datos[0] != "") {
						?>
							<option value="<?= $datos[0] ?>"><?= $datos[1] ?></option>
			            <?php
			            	}
			            }
			            ?>
			            </select>
			        </div>
					
			        <div class="form-group col-md-12">
			            <label for="nombre" class="col-form-label">Total: $</label>
			            <input type="number" step="0.1" readonly="" value="<?= $total ?>" class="form-control" id="nombre" name="total">
			        </div>
    			</div>
    			<div class="footer">
			        <button type="submit" class="btn btn-primary" name="guardarVenta">Guardar</button>
			        <button type="submit" class="btn btn-primary" name="#">Cancelar</button>
			    </div>
			</form>
		</div>	
	</center>
</body>
</html>

<!--
	<input type="submit" value="Eliminar" class="btn btn-sm btn-warning" name="#">
-->