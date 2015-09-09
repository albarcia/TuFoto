<?php
// principal.php
//($rutaA = $_SESSION['rutaRaiz'];
include('../libreriaPHP/imagenes.php');
include_once('../clases/colFotografia.php');
session_start();
//include_once('../clases/fotografia.php');
if (!isset($_POST['criterioOrdenacion']) && !isset($_POST['limite'])){
	$criterioOrdenacion = 'fechaSubida';
	$limite = 16;
} else {
	$criterioOrdenacion = $_POST['criterioOrdenacion'];
	$limite = $_POST['limite'];
	$comienzo = ($_POST['pagina'] - 1) * $limite;
}
// Si el criterio de ordenación es por fecha de subida se necesita más espacio en el div de datos de 
// la foto para informar de cuántos días hace que se subió la foto. Si los criterios de ordenación
// son la valoración o el número de visitas, el espacio necesario es menor y, por tanto, podemos
// disponer de más espacio para el título.
if ($criterioOrdenacion == 'fechaSubida'){
	$anchoTituloFoto = '40%';
	$anchoValoracionFoto = '59%';
} else {
	$anchoTituloFoto = '60%';
	$anchoValoracionFoto = '49%';
}

if ((isset($_POST['claveBusqueda']) && isset($_POST['valorBusqueda'])) &&
	(!empty($_POST['claveBusqueda']) && !empty($_POST['valorBusqueda']))) {
	$camposCriterio = array($_POST['claveBusqueda'] => $_POST['valorBusqueda']);
	$_SESSION['claveBusqueda'] = $_POST['claveBusqueda'];
	$_SESSION['valorBusqueda'] = $_POST['valorBusqueda'];
} elseif ((isset($_SESSION['claveBusqueda']) && isset($_SESSION['valorBusqueda'])) && 
	(!empty($_SESSION['claveBusqueda']) && !empty($_SESSION['valorBusqueda']))) {
	$camposCriterio = array($_SESSION['claveBusqueda'] => $_SESSION['valorBusqueda']);
} else {
	$camposCriterio = array(); // Vacío para que nos recupere todas los registros de la tabla.
}

// Creamos una colección de fotografías ordenadas según el criterio seleccionado.
// Si nos han pasado un criterio de búsqueda se crea la colección según ese criterio. Si no es así
// se crea una colección con todos los registros de la tabla.
$colFotografias = new ColFotografia($camposCriterio, $criterioOrdenacion, $comienzo, $limite);
// Construimos el mosaico.
$salida = '<div id="mosaico">';
$salida .= '<input type="hidden" id="criterioOrdenacion" value="' . $criterioOrdenacion . '"/>';

$clase = array('fotoMosaicoEstrecho', 'fotoMosaicoAncho');
$cantidadFotos = count($colFotografias->getColFotos());
$contador = 0;
$contadorDeFotos = 0;
$fila = 1;
$colFotografiaTemp = array(); // Array temporal para guardar las fotos serializadas que se van a guardar en sesión.
foreach($colFotografias->getColFotos() as $foto){
	/*if ($foto->getIdFoto() == 29){
		var_dump($foto);
	}*/
	// Serializamos cada foto para poder cargarlas en un array de sesión.
	$colFotografiasTemp[] = serialize($foto);
	$rutaFotografia = "imagenes/usuarios/" . $foto->getNombreUsuario() . "/muestra/" . $foto->getNombreFichero();
	list($ancho, $alto) = getimagesize('../' . $rutaFotografia);
	// Mostar las fotos un 25% desplazadas de su origen en altura y un 10% en anchura.
	$margenSuperiorImg = (-0.25) * $alto;
	// Si al calcular el desplazamiento del margen superior la parte visible de la imagen es menor que el alto del div
	// fijamos el desplazamiento del margen superior en 0px.
	if (($alto + $margenSuperiorImg) < 290){
		$margenSuperiorImg = 0;
	}
	$margenIzquierdoImg = (-0.10) * $ancho;
	$idFoto = $foto->getIdFoto();
	$tituloFoto = $foto->getTitulo();
	// fijamos la información de la barra inferior de información según el criterio de ordenación solicitado.
	$valor = '';
	switch ($criterioOrdenacion){
		case 'fechaSubida':
			$criterio = 'Subida';
			$horasDesdeSubida = (strtotime(date('Y-m-d H:i:s')) - strtotime($foto->getFechaSubida())) / 3600;
			$dias = floor($horasDesdeSubida / 24);
			$horas = fmod($horasDesdeSubida, 24);
			$valor = 'Hace ' . $dias . ' d&iacute;as';
			if ($dias < 1){
				//$valor = 'Hoy';
				if ($horas < 1){
					$valor = 'Hace un momento';
				} else {
					$valor = 'Hace unas horas';
				}
			} elseif ($dias > 0 && $dias < 2){
				$valor = 'Ayer';
			}
			break;
		case 'puntuacion':
			$criterio = 'Valoraci&oacute;n';
			$valor = $foto->getPuntuacion();
			break;
		case 'visitas':
			$criterio = 'Visitas';
			$valor = $foto->getVisitas();

	}
	$sw = marcoAleatorio($fila);
	// Estableciendo los márgenes para las fotos que no están en los extremos de la fila.
	$margenIzquierdoDiv = "10";
	$margenDerechoDiv = "10";
	// Anulando los márgenes al comienzo de la fila (si es la primera imagen de la fila) y al final (si es la última).
	if ($fila == 1){
		$margenIzquierdoDiv = "0";
	} elseif (($fila >2 && $sw == 1) || ($fila > 3 && $sw == 0)){
		$margenDerechoDiv = "0";
	}
	// Si el ancho calculado a mostrar de la foto es inferior al del marco ancho del mosaico, lo que dejaría un espacio vacío dentro del marco, se le asigna 'manualmente' un marco estrecho ($sw=0).
	if (($ancho - ($ancho * 0.10)) < 590){
		$sw = 0;
	}
	$salida .= '<article>';
	$salida .= '<div class="fotoMosaico ' . $clase[$sw] . '" style="margin-left: ' . $margenIzquierdoDiv . 'px; margin-right: ' . $margenDerechoDiv . 'px;">
					<div class="fotoMosaicoInterior">
						<a href="procesos/mostrarFotografia.php?idFoto=' . $idFoto . '">
							<img id="' . $idFoto . '" src="recursos/ajax-loader_line.gif" class="foto" alt="' . $tituloFoto . '" style="margin-top: ' . $margenSuperiorImg . 'px; margin-left: ' . $margenIzquierdoImg . 'px;">
							<script>cargaFoto("' . $rutaFotografia . ' ", ' . $idFoto . ');</script>
						</a>
					</div>
					<div id="datos' . $idFoto . '" class="datosFoto">
						<div class="tituloFoto" style="width:' . $anchoTituloFoto . ';" >
							' . $tituloFoto . '
						</div>
						<div id="valoracion' . $idFoto . '" class="valoracionFoto" style="width: ' . $anchoValoracionFoto . ' s ">' 
							. $criterio . ':&nbsp;' . $valor . '
						</div>
					</div>
				</div>';
	$salida .= '</article>';
	if ($sw == 0){
		$fila ++;
	} else {
		$fila += 2;
	}
	$contador++;
	$contadorDeFotos++;
	if ($fila > 4){
		$contador = 0;
		$fila = 1;
	}
} // Llave de cierre del foreach.
// Guardamos la colección de fotos en una variable de sesión.
$_SESSION['colFotografias'] = $colFotografiasTemp;
$salida .= '<div class="blanco"></div></div>';

echo $salida;
?>