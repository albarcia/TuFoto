<?php
session_start();

$nombreUsuario = $_SESSION['nombreUsuario'];
$extension = substr($_FILES['file-0']['name'], -3);
$nombreFichero = basename($_FILES['file-0']['name']);
$ficheroOriginalTemp = '../imagenes/temp/'. $nombreUsuario . 'Temp' . '.' . $extension;
$tipoMime = $_FILES['file-0']['type'];
$tamano = $_FILES['file-0']['size'];
$temporal = $_FILES['file-0']['tmp_name'];
$codigoError = $_FILES['file-0']['error'];

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
    // Le damos formato a la imagen subida.
    include_once('../libreriaPHP/imagenes.php');
    $ficheroMuestraTemp = "../imagenes/temp/". $nombreUsuario . "Temp01_muestra" . "." . $extension;
    if (redimensionar($ficheroOriginalTemp, $ficheroMuestraTemp, 700, 700, 'muestra')){
        //unlink($ficheroTemporal);
        //$ficheroTemporal = $rutaFinalTemp;
        list($ancho, $alto) = getimagesize($ficheroMuestraTemp);
    
	    $_SESSION['ficheroMuestraTemp'] = $ficheroMuestraTemp;
	    $_SESSION['ficheroOriginalTemp'] = $ficheroOriginalTemp;
	    // A la ruta de la imagen le quitamos los tres primeros caracteres (../) porque en PHP son necesarios
		// pero en HTML no, ya que tenemos configurada la etiqueta <base>.
		$respuesta = array('exito' => 'ok',
							'imagen' => substr($ficheroMuestraTemp, 3),
							'alto' => $alto,
							'ancho' => $ancho);
	} else{
		$error = 'Se ha producido un error en las operaciones posteriores a la subida del fichero.';
	}
}

if ($error != ''){
	$respuesta = array('exito' => 'nok',
						'error' => $error);
}
echo json_encode($respuesta);
?>