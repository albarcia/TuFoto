<?php
session_start();

$extension = substr($_FILES['file-0']['name'], -3);
$nombreFichero = basename($_FILES['file-0']['name']);
$tipoMime = $_FILES['file-0']['type'];
$tamano = $_FILES['file-0']['size'];
$temporal = $_FILES['file-0']['tmp_name'];
$codigoError = $_FILES['file-0']['error'];

// Se asignan números consecutivos a los ficheros temporales de imágenes de perfil para evitar que, si
// dos usuarios seleccionan su foto de perfil a la vez, no se grabe una por encima de la otra. Para 
// conservar la secuencia se utiliza un fichero de texto donde se guarda el último número utilizado.
if ($fichero = fopen('../imagenes/temp/lasttemp.dat', 'a+')){
    rewind($fichero);
    $ultimoNombre = fgets($fichero);
    if (!$ultimoNombre ){
        $ultimoNombre = 1;
	}
	fclose($fichero);
} else {
	$ultimoNombre = 1;
}

$ficheroOriginalTemp = '../imagenes/temp/' . 'Perfil_' . $ultimoNombre . 'Temp.' . $extension;

$Subido = false;
$error = '';
if ($cofigoError == UPLOAD_ERR_OK){
    if ($tipoMime != 'image/jpeg' && $tipoMime != 'image/jpg' && $tipoMime != 'image/gif' && $tipoMime != 'image/png'){
        $error = 'El archivo subido tiene un formato incorrecto';
    } elseif (preg_match("/[^0-9a-zA-Z_.-]/", $nombreFichero)){
        $error = 'El nombre del archivo contiene caracteres no válidos.';
    } else {
        $subido = move_uploaded_file($_FILES['file-0']['tmp_name'], $ficheroOriginalTemp);
    }
}
if (!$subido){
    $error = 'Se ha producido un error en la carga del archivo: ' . $error;
} else {
	include_once('../libreriaPHP/imagenes.php');
	$ficheroTemporal = '../imagenes/temp/' . 'Perfil_' . $ultimoNombre . '.' . $extension;
	if (redimensionar($ficheroOriginalTemp, $ficheroTemporal, 200, 200)){
		unlink($ficheroOriginalTemp);
		
		$ultimoNombre++;
		$fichero = fopen('../imagenes/temp/lasttemp.dat', 'w');
		fwrite($fichero, $ultimoNombre);
		fclose($fichero);

		list($ancho, $alto) = getimagesize($ficheroTemporal);

	    $_SESSION['ficheroTemporal'] = $ficheroTemporal;
	    // A la ruta de la imagen le quitamos los tres primeros caracteres (../) porque en PHP son necesarios
		// pero en HTML no, ya que tenemos configurada la etiqueta <base>.
		$respuesta = array('exito' => 'ok',
							'imagen' => substr($ficheroTemporal, 3),
							'alto' => $alto,
							'ancho' => $ancho);
	} else {
		$error = 'Se ha producido un error en las operaciones posteriores a la subida del fichero.';
	}
}

if ($error != ''){
$respuesta = array('exito' => 'nok',
					'error' => $error);
}

echo json_encode($respuesta);
?>