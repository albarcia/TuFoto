<?php
if (!isset($_GET['fichero']) || empty($_GET['fichero']) ||
    !isset($_GET['usuario']) || empty($_GET['usuario'])) {
    exit();
}
$rutaRaiz = "/homepages/12/d416622347/htdocs/fotografia/";
// Nos protegemos por si nos introducen una ruta diferente en la URL.
$fichero = basename($_GET['fichero']);
$rutaFichero = $rutaRaiz . 'imagenes/usuarios/' . $_GET['usuario'] . '/originales/' . $fichero;
if (is_file($rutaFichero)) {
    $tamano = filesize($rutaFichero);
    // Enviar headers.
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename= imagen' . $fichero);
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . $tamano);
    readfile($rutaFichero);
} else {
	echo 'No encuentro el fichero solicitado.';
}
?>