<?php
/**
 * 
 */
$op = $_GET["opcion"];
//$op = '1';
$fileData = new FileData();
switch ($op) {
	/*Listado general de los usuarios*/
	case '1':
		$data = "";
		$file = fopen("file.txt", "r");
		while (!feof($file)) {
			$line = fgets($file);
			$data .= $line.";";
		}
		echo $data;
		fclose($file);
		break;
	/*Agregar un nuevo usuario al archivo existente, si no está se intenta crear*/	
	case '2':
		/*datos que entran desde la peticion con POST*/
		$nombre = $_GET["name"];
		$dir = $_GET["address"];
		$tel = $_GET["phone"];
		$new_id = $fileData->getId();
		echo $fileData->addUser($new_id, $nombre, $dir, $tel);
		break;
	/*Modificacion de una linea del archivo*/
	case '3':
		/*datos que entran desde la peticion con POST*/
		$nombre = $_GET["name"];
		$dir = $_GET["address"];
		$tel = $_GET["phone"];
		$id = $_GET["id"];
		echo $fileData->updateUser($id, $nombre, $dir, $tel);
		break;
	case '4':
		/*datos que entran desde la peticion con POST*/
		$id = $_GET["id"];
		echo $fileData->removeUser($id);
		break;
	case '5':
		/*datos que entran desde la peticion con POST*/
		$id = '3';
		echo $fileData->getUser($id);
		break;
	case '6':
		echo $fileData->getUsers();
		break;	
	default:
		# code...
		break;
}

/**
 * 
 */
class FileData
{
	
	function getId()
	{
		$file = fopen("file.txt", "r");
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
	function addUser($id, $nombre, $dir, $tel)
	{
		$line = $id."@".$nombre."@".$dir."@".$tel."\n";
		//$line = strval(str_replace("\0", "", $line));
		if ($file = fopen("file.txt", "a")) {
			fwrite($file, $line);
			fclose($file);
			return true;
		} else {
			return false;
		}
		
		
	}
	function updateUser($id, $nombre, $dir, $tel)
	{
		$content = file("file.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$row_update = $id."@".$nombre."@".$dir."@".$tel."\n";
				$content[$i] = $row_update;
			}
		}
		/*
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
		*/
		if ($file = fopen("file.txt", "w")) {
			foreach ($content as $row) {
				fwrite($file, $row);
			}	
			fclose($file);
			return true;
		} else {
			return false;
		}
	}
	function removeUser($id)
	{
		$content = file("file.txt");
		$flag = false;
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				$flag = true;
				unset($content[$i]);
			}
		}
		if ($flag) {
			$file = fopen("file.txt", "w");
			foreach ($content as $row) {
				fwrite($file, $row);
			}
			fclose($file);
			return true;
		} else {
			return false;
		}	
	}
	function getUser($id)
	{
		$content = file("file.txt");
		for ($i=0; $i < count($content) ; $i++) {
			$row = explode("@", $content[$i]); 
			if ($row[0] == $id) {
				return $content[$i].";";
			}
		}
	}
	function getUsers()
	{
		$file = fopen("file.txt", "r");
		$data = "";
		while (!feof($file)) {
			$data .= $line = fgets($file).";";
		}
		return $data;
	}
}
?>