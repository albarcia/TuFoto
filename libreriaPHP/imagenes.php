<?php
// imagenes.php
// Librería de funciones para trabajar con imágenes.

// Redimensiona la imagen original segun los valores máximos pasados como parámetros.
function redimensionar($rutaImagenOriginal, $rutaFinal, $maxAncho, $maxAlto, $tamano='muestra'){

	// Creamos una variable imagen a partir de la imagen original.
	$nombreImagen = basename($rutaImagenOriginal);
	// Obtenemos la extensión de la imagen original para saber de qué tipo es.
	$extension = strtolower(substr($nombreImagen, -3));

	$operacionRealizada = true;
	$ok = true;
	switch ($extension) {
		case 'jpg':
			if (!$imagenOriginal = imagecreatefromjpeg($rutaImagenOriginal)){
				$ok = false;
			} else{
				$calidad = 80;
			}
			break;

		case 'png':
			if (!$imagenOriginal = imagecreatefrompng($rutaImagenOriginal)){
				$ok = false;
			} else{
				$calidad = 8;
			}
			break;

		case 'gif':
			if (!$imagenOriginal = imagecreatefromgif($rutaImagenOriginal)){
				$ok = false;
			}
	}
	
	if ($ok){
		// Ancho y alto de la imagen original.
		list($ancho, $alto)=getimagesize($rutaImagenOriginal);
		// Se calcula ancho y alto de la imagen final.
		$x_ratio = $maxAncho / $ancho;
		$y_ratio = $maxAlto / $alto;

		// Si el ancho y el alto de la imagen no superan los máximos,
		// ancho final y alto final son los que tiene actualmente.
		if( ($ancho <= $maxAncho) && ($alto <= $maxAlto) ){
			$anchoFinal = $ancho;
			$altoFinal = $alto;
		}

		/*
		* Si proporción horizontal*alto mayor que el alto máximo,
		* alto final es alto por la proporción horizontal
		* es decir, le quitamos al ancho, la misma proporción que
		* le quitamos al alto.
		*
		*/
		elseif (($x_ratio * $alto) < $maxAlto){
			if ($tamano != 'mini'){ // Si NO estamos redimensionando para miniatura.
				$altoFinal = ceil($x_ratio * $alto);
				$anchoFinal = $maxAncho;
			} else {
				$anchoFinal = ceil($y_ratio * $ancho);
				$altoFinal = $maxAlto;
			}
		}

		// Igual que antes pero a la inversa.
		else{
			if ($tamano != 'mini'){ // Si NO estamos redimensionando para miniatura.
				$anchoFinal = ceil($y_ratio * $ancho);
				$altoFinal = $maxAlto;
			} else {
				$altoFinal = ceil($x_ratio * $alto);
				$anchoFinal = $maxAncho;
			}
		}

		// Creamos una imagen en blanco de tamaño $anchoFinal*$altoFinal.
		$imagenTemporal = imagecreatetruecolor($anchoFinal, $altoFinal);

		// Copiamos $imagenOriginal sobre la imagen que acabamos de crear en blanco ($imagenTemporal)
		imagecopyresampled($imagenTemporal, $imagenOriginal, 0, 0, 0, 0, $anchoFinal, $altoFinal, $ancho, $alto);

		// Se destruye variable $imagenOriginal para liberar memoria
		imagedestroy($imagenOriginal);

		// Definimos la ruta de la imagen final.
		$rutaImagenFinal = $rutaFinal;

		// Se crea la imagen final en el directorio indicado. Todas muestras imágenes de trabajo serán jpeg.
		if (!imagejpeg($imagenTemporal, $rutaImagenFinal, $calidad)){
			echo "Se ha producido un error al grabar en disco la imagen redimensionada en imagenes.php -> función: redimensionar()";
			$operacionRealizada = false;
		}
	} else {
		echo "Se ha producido un error al crear la imagen temporal en imagenes.php -> función: redimensionar()";
		$operacionRealizada = false;
	}

	return $operacionRealizada;

}

// Decisión aleatoria del tamaño del marco (grande o pequeño) para el mosaico.
function marcoAleatorio($fila){
	if ($fila > 3){
		$sw = 0;
	} else {
		$sw = mt_rand(0,1);
	}
	return $sw;
}

function descargarFotografia($rutaFoto){
	header('Content-Type: image/jpeg');
	header('Content-Disposition: attachment; filename="' . $rutaFoto . '"');
	readfile($rutaFoto);
}

?>