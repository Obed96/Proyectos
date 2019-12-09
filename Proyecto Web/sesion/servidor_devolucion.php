<?php 
$op = $_GET["opcion"];

$devolucion = new Devolucion();

switch ($op) {
	case '1':
		echo($devolucion->buscar($_GET["id"]));
		break;
	case '2':
		$venta = explode("|", $_GET["venta"]);
		$id = explode("@", $venta[0]);
		$id = $id[0];
		$nuevos = $_GET["nuevos"];
		$devueltos = $_GET["devueltos"];
		echo($devolucion->redifinirVenta($id, $nuevos, $devueltos));
		break;
	case '3':
		$id = $_GET["id"];
		echo $devolucion->getDevolucion($id);
		break;

	case '4':
		$id = $_GET["id"];
		echo $devolucion->getLineDevolucion($id);
		break;
	default:
		# code...
		break;
}



class Devolucion
{
	function buscar($id){
		$data = "";
		$file = fopen("file_venta.txt", "r");
		
		while (!feof($file)) {
			$line = fgets($file);
			$row = explode("@", $line);
			if ($row[0] == $id) {
				return $line;
			}
		}
		fclose($file);
		return false;	
	}

	function redifinirVenta($id_venta, $nuevos, $devueltos)
	{
		/*quitar los prodcutos devueltos*/
		$file = file("file_venta.txt");
		$aux = "";
		$total_aux =0.0;
		$total = 0.0;
		$producto_novendidos;
		for ($i = 0; $i < count($file); $i++) {
			$line = $file[$i]; 
			$venta = explode("|", $line);
			$venta_id = explode("@", $venta[0]);
			if ($venta_id[0] == $id_venta) {
				$aux_line = "";
				$total = $venta_id[2];
				$productos = explode(";", $venta[1]);
				for ($j = 0; $j< count($productos)-1; $j++) {
					$producto = explode("@", $productos[$j]);
					$id_pro = $producto[2];
					$productos_dev = explode(";", $devueltos);
					$flag = false;
					foreach ($productos_dev as $key => $dev) {
						$pro_dev = explode("@", $dev);
						if ($id_pro == $pro_dev[2]) {
							$flag = true;
							$this->incrementarProducto($pro_dev[2], $pro_dev[1]);
							$total_aux = $total_aux + $pro_dev[4];
							//var_dump($pro_dev[1]);
							
						}	
					}
					if (!$flag) {
						$aux_line .= $productos[$j].";";
					}
				}
				if ($nuevos) {
					$data = $this->agregarLines($nuevos);
					$data = explode("%", $data);
					$producto_novendidos = $data[1];
				}
				$venta_aux = $venta_id[0]."@".$venta_id[1]."@".($total - $total_aux+$data[2])."@".$venta_id[3];
				$aux .= $venta_aux."@Inactiva"."|".$aux_line.$data[0]."$"."\n";
				$add = $venta_aux."|".$devueltos."$"."\n";
			}else{
				$aux .= $line;
			}
		}
		if($file = fopen("file_devolucion.txt", "a")){
			fwrite($file, $add);
			fclose($file);
		}
		if ($file = fopen("file_venta.txt", "w")) {
			fwrite($file, $aux);
			fclose($file);
			return $producto_novendidos;
		}
		return $producto_novendidos;
	}


	function incrementarProducto($id_pro, $cant)
	{
		$base_url = "http://localhost/sesion/";
		$ch = curl_init();
		$url = $base_url."servidor_venta.php?opcion=67&id=".$id_pro."&qty=".$cant;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$salida = curl_exec($ch);
	}

	function agregarLines($nuevos)
	{
			$salida = explode(";", $nuevos);
			$data = "";
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
				$data .= $aux_detalle;
			} else {
				$unsold_products .= $value.";";
			}
			
		}
		return $data .= "%".$unsold_products."%".$aux_total;

	}

	function getDevolucion($id)
	{
		$file = file("file_venta.txt");
		for ($i=0; $i < count($file); $i++) { 
			$line = explode("|", $file[$i]);
			$venta = explode("@", $line[0]);
			if ($venta[0] == $id) {
				if ($venta[4] == "Activo") {
					return true;
				}
			}
		}
		return false;
	}

	function getLineDevolucion($id){
		$file = file("file_devolucion.txt");
		for ($i=0; $i < count($file); $i++) { 
			$line = explode("|", $file[$i]);
			$venta = explode("@", $line[0]);
			if ($venta[0] == $id) {
				return $line[1];
			}
		}
		return false;
	}
}

 ?>