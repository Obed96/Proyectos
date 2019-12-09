<?php
date_default_timezone_set("America/Mexico_City");
$op = $_GET["opcion"];

$venta = new Venta();
switch ($op) {
	//guardar ventas
	case '1':
		$id = $venta->getId();
		$id_user = $_GET["id_user"];
		$total = $_GET["total"];
		$fecha =  strftime( "%d-%m-%Y-%H-%M-%S", time() );
		$detalle_venta = $_GET["detalle_venta"];
		//$tipoM = "V";
		echo $venta->addVenta($id, $id_user, $total, $fecha, $detalle_venta);
		//echo $venta->addVMov($id, $tipoM, $fecha, $detalle_venta);
		break;
	default:
	// listar ventas
	case '2':
		$data = "";
		$file = fopen("file_venta.txt", "r");
		while (!feof($file)) {
			$line = fgets($file);
			$data .= $line."$";
		}
		echo $data;
		fclose($file);
		break;
	// obtener nombres de productos
	case '3':
		$id = $_GET['id_producto'];
		echo $venta->getNameProduct($id);
		break;
	case '4':
		$id = $_GET['id_cliente'];
		echo $venta->getNameClient($id);
		break;
	case '66':
		$info = $_GET["info"];
		$info = explode("|", $info);
		$id = $info[0];

		$venta->removeVenta($id);

		$products = $info[1];
		$products = explode(";", $products);
		$flag = true;
		$anterior = "";
		$tipoM = "DE";
		$fecha = strftime( "%d-%m-%Y-%H-%M-%S", time() );
		foreach ($products as $key => $value) {
			$val = explode("@", $value);
			$id_product = $val[0];
			$qty = $val[1];
			$actual = $qty;

			$venta->increaseQuantityM($id_product,$anterior, $actual, $tipoM, $fecha);
			$venta->increaseQuantity($id_product, $qty);	
		}

		echo($flag);

		break;
	case '67':
		$id = $_GET["id"];
		$qty = $_GET["qty"];

		$anterior = "";
		$actual = $qty;
		$tipoM = "DE";
		$fecha = strftime( "%d-%m-%Y-%H-%M-%S", time() );
		echo $venta->increaseQuantityM($id,$anterior, $actual, $tipoM, $fecha);
		echo $venta->increaseQuantity($id, $qty);
		break;
	break;
}

/**
 * 
 */

class Venta {
	
	function increaseQuantityM($id, $anterior, $actual, $tipoM, $fecha)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$anterior = $row[3];
				$incr = $actual;
				//$incr = $row[3]+$actual;
				$row_dev = $id."@".$tipoM."@".$anterior."@".$incr."@".$fecha."\n";
				if ($file = fopen("file_movimiento.txt", "a")) {
					fwrite($file, $row_dev);
					fclose($file);
					return true;
				} else {
					return $row_dev;
				}
				break;
			}
		}
		
	}
	function increaseQuantity($id, $qty)
	{	
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$incr = $row[3]+$qty;
				$row_update = $id."@".$row[1]."@".$row[2]."@".$incr."@".$row[4]."@".$row[5]."@".$row[6]."@".$row[7];
				
				$content[$i] = $row_update;
			}
		}
		if ($file = fopen("file_product.txt", "w")) {
			foreach ($content as $row) {
				fwrite($file, $row);
			}	
			fclose($file);
			return true;
		} else {
			return false;
		}

	}

	function removeVenta($id)
	{
		$content = file("file_venta.txt");
		$flag = false;
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$flag = true;
				unset($content[$i]);
			}
		}
		if ($flag) {
			$file = fopen("file_venta.txt", "w");
			foreach ($content as $row) {
				fwrite($file, $row);
			}
			fclose($file);
			return true;
		} else {
			return false;
		}	
	}

	function getNameProduct($id)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$result = $content[$i];
				$result = explode("@", $result);
				return $result[1];
			}
		}
	}

	function getNameClient($id)
	{
		$content = file("file.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$result = $content[$i];
				$result = explode("@", $result);
				return $result[1];
			}
		}
	}
	function getId()
	{
		$file = fopen("file_venta.txt", "r");
		$id = '';
		while (!feof($file)) {
			$line = fgets($file);
			$datos = explode("@", $line);
			if ($datos[0] != '') {
				$id = $datos[0];	
			}
		}
		fclose($file);
		if (!$id) {
			return $id = 1;
		}else{
			return ++$id;
		}
	}
	function addVenta($id, $id_user, $total, $fecha, $detalle_venta)
	{
		$salida = $detalle_venta;
		$aux_detalle = "";
		$unsold_products = "";
		$aux_total = 0.0;
		$salida = explode(";", $salida);
		foreach ($salida as $key => $value) {
			$datos = explode("@", $value);
			$ch = curl_init();
			$url = "http://localhost/sesion/servidor_product.php?opcion=9&id=".$datos[2]."&cant=".$datos[1];
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$quantity = curl_exec($ch);
			
			if ($quantity) {
				
				$ch = curl_init();
				$url = "http://localhost/sesion/servidor_product.php?opcion=8&id=".$datos[2]."&cant=".$datos[1];
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$salida = curl_exec($ch);
				$aux_detalle .= $value.";";
				$aux_total = $aux_total + $datos[4];
			} else {
				$unsold_products .= $value.";";
			}
			
		}
		if ($aux_detalle) {
			$line = $id."@".$id_user."@".$aux_total."@".$fecha."@Activo"."|".$aux_detalle."$"."\n";
			if ($file = fopen("file_venta.txt", "a")) {
				fwrite($file, $line);
				fclose($file);
				return $unsold_products;
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	
	}
}
?>