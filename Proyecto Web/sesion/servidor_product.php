<?php
date_default_timezone_set("America/Mexico_City");
$op = $_GET["opcion"];

$product = new Product();
switch ($op) {
	//obtiene todas las marcas
	case '1':
		echo $product->getMarcas();
		break;
	case '2':
		echo $product->getProducts();
		break;
	case '3':
		$id = $_GET["cod"];
		$name = $_GET["nombre"];
		$id_marca = $_GET["marca"];
		$cant = $_GET["cantidad"];
		$stock_max = $_GET["stock_max"];
		$stock_min = $_GET["stock_min"];
		$price = $_GET["pre"];
		$status = "Activo";
		$anterior = $_GET["cantidad"];
		$actual = $_GET["cantidad"];
		$tipoM = "E";
		$fecha = strftime( "%d-%m-%Y-%H-%M-%S", time() );
		if (!$product->isExistProduct($id)) {
			echo $product->addProduct($id, $name, $id_marca, $cant, $stock_max, $stock_min, $price, $status);
			echo $product->addMov($id,$tipoM,$anterior,$actual,$fecha);
		} else {
			echo "";
		}
		break;
	case '4':
		$id = $_GET["cod"];
		$status = $_GET["status"];
		echo $product->deleteProduct($id, $status);
		break;
	case '5':
		$id = $_GET["cod"];
		$name = $_GET["nombre"];
		$id_marca = $_GET["marca"];
		$cant = $_GET["cantidad"];
		$stock_max = $_GET["max"];
		$stock_min = $_GET["min"];
		$price = $_GET["pre"];
		$status = $_GET["status"];

		$anterior = $_GET["existencia"];
		$actual = $cant;
		$tipoM = "S";
		$fecha =  strftime( "%d-%m-%Y-%H-%M-%S", time() );
		//echo "si entro";
		echo $product->updateProduct($id, $name, $id_marca, $cant, $stock_max, $stock_min, $price, $status);
		echo $product->updateMov($id, $tipoM,$anterior,$actual,$fecha);
		//header("http://localhost/Web3/unity2/sesion/panelProducto.php");
		break;
	case '6':
		$id = $_GET["id_marca"];
		echo $product->getMarca($id);
		break;
	case '7':
		$id = $_GET["cod"];
		echo $product->getProduct($id);
		break;
	case '8':
		$id = $_GET["id"];
		$cant = $_GET["cant"];

		$anterior = "";
		$actual = $cant;
		$tipoM = "V";
		$fecha =  strftime( "%d-%m-%Y-%H-%M-%S", time() );
		echo $product->reduceCMov($id, $tipoM, $anterior, $actual, $fecha);
		echo $product->reduceCant($id, $cant);
		break;
	case '9':
		$id = $_GET["id"];
		$cant = $_GET["cant"];

		echo $product->have_quantity($id, $cant);
		break;

	default:
		
		break;
}

/**
 * 
 */
class Product
{
	function getMarca($id)
	{
		$content = file("file_marca.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				return $content[$i].";";
			}
		}
	}
	function getMarcas()
	{
		$data = "";
		$file = fopen("file_marca.txt", "r");
		while (!feof($file)) {
			$line = fgets($file);
			$data .= $line.";";
		}
		fclose($file);
		return $data;
	}

	function getProducts()
	{
		$data = "";
		$file = fopen("file_product.txt", "r");
		$activos = "";
		$inactivos = "";
		while (!feof($file)) {
			$line = fgets($file);
			$row = explode("@", $line);
			$row[7] = trim($row[7]); 
			if ($row[7] == "Activo") {
				$activos .= $line.";";
			}else{
				$inactivos .= $line.";";
			}
		}
		fclose($file);
		return $inactivos.$activos;
	}
	function addProduct($id, $name, $id_marca, $cant, $stock_max, $stock_min, $price, $status)
	{
		$line = $id."@".$name."@".$id_marca."@".$cant."@".$stock_max."@".$stock_min."@".$price."@".$status."\n";
		//$line = strval(str_replace("\0", "", $line));
		if ($file = fopen("file_product.txt", "a")) {
			fwrite($file, $line);
			fclose($file);
			return true;
		} else {
			return $line;
		}
	}
	function addMov($id,$tipoM,$anterior,$actual,$fecha)
	{
		$line2 = $id."@".$tipoM."@".$anterior."@".$actual."@".$fecha."\n";
		if ($file2 = fopen("file_movimiento.txt", "a")) {
			fwrite($file2, $line2);
			fclose($file2);
			return true;
		} else {
			return $line2;
		}
	}

	function deleteProduct($id, $status)
	{
		$content = file("file_product.txt");
		$flag = false;
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$flag = true;
				$row[7] = $status;
				$row_delete = $row[0]."@".$row[1]."@".$row[2]."@".$row[3]."@".$row[4]."@".$row[5]."@".$row[6]."@".$row[7]."\n";
				$content[$i] = $row_delete;
				break;
			}
		}
		
		if ($flag) {
			$file = fopen("file_product.txt", "w");
			foreach ($content as $row) {
				fwrite($file, $row);
			}
			fclose($file);
			return true;
		} else {
			return false;
		}
	}
	function updateMov($id, $tipoM,$anterior,$actual,$fecha)
	{
		$line3 = $id."@".$tipoM."@".$anterior."@".$actual."@".$fecha."\n";
		if ($file3 = fopen("file_movimiento.txt", "a")) {
			fwrite($file3, $line3);
			fclose($file3);
			return true;
		} else {
			return $line3;
		}
	}
	function updateProduct($id, $name, $id_marca, $cant, $stock_max, $stock_min, $price, $status)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$row_update = $id."@".$name."@".$id_marca."@".$cant."@".$stock_max."@".$stock_min."@".$price."@".$status."\n";
				//echo $row_update;
				$content[$i] = $row_update;
				break;
			}
		}
		/*
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
		*/
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
	function isExistProduct($id)
	{
		$content = file("file_product.txt");
		$flag = false;
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				return true;
			}
		}
		return false;	
	}
	function getProduct($id)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				return $content[$i].";";
			}
		}
	}
	function reduceCMov($id, $tipoM, $anterior, $actual, $fecha)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$anterior = $row[3];
				$actual = $row[3] - $actual;	
				$ventaMov = $id."@".$tipoM."@".$anterior."@".$actual."@".$fecha."\n";
				if ($file4 = fopen("file_movimiento.txt", "a")) {
					fwrite($file4, $ventaMov);
					fclose($file4);
					return true;
				} else {
					return $ventaMov;
				}
				
				break;
			}
		}
		
	}

	function reduceCant($id, $cant, $tipoM, $anterio, $actual, $fecha)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$anterior = $row[3];
				$cant = $row[3] - $cant;
				$actual = $cant;	
				$row_update = $id."@".$row[1]."@".$row[2]."@".$cant."@".$row[4]."@".$row[5]."@".$row[6]."@".$row[7];
				//echo $row_update;
				$content[$i] = $row_update;
			}
			
		}
		/*
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
		*/
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

	function have_quantity($id, $cant)
	{
		$content = file("file_product.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$cant = $row[3] - $cant;
				if ($cant >= 0) {
					return true;
				}
			}
		}
		return false;
	}
}
?>