<?php
// login.php
$rutaA = '//Applications/XAMPP/xamppfiles/htdocs/fotografia/';

$control = 'Vacio';
if (isset($_SESSION['control'])){
	$control = $_SESSION['control'];
} elseif (isset($_SESSION['idUsuario']) && isset($_SESSION['nombreUsuario'])) {
	$nombreUsuario = $_SESSION['nombreUsuario'];
	$idUsuario = $_SESSION['idUsuario'];
	$control = 'logeado';
}

switch ($control) {
    case 'logeado':
    $nombreUsuario = $_SESSION['nombreUsuario'];
    $usuario = unserialize($_SESSION['objetoUsuario']);
    $foto = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
	if ($usuario->getFoto() != 'defecto' && $usuario->getFoto() != ''){
	    $foto = 'imagenes/usuarios/' . $nombreUsuario . '/perfil/' . $nombreUsuario . 'Mini.jpg'; 
	}
    ?>
    	<div id="cajaLogeado">
			<div id="fotoLogeado" class="fotoUsuarioPequena">
				<img src="<?php echo $foto; ?>" alt="fotografia de <?php  echo $nombreUsuario; ?>"/>
			</div>
			<div id="nombreUsuarioLogeado">
				<?php echo $nombreUsuario; ?>
				<input type="hidden" id="idUsuarioLogeado" value="<?php echo $usuario->getIdUsuario(); ?>"/>
			</div>
			<div class="flechaMenu"><!-- flecha --></div>
			<ul class="menuCabecera" id="menuUsuario">
				<li class="itemMenuUsuario"><a href="procesos/crearFoto.php">Subir fotograf&iacute;a</a></li>
				<li class="itemMenuUsuario"><a href="procesos/mostrarUsuario.php">Ver informaci&oacute;n de perfil</a></li>
				<li class="itemMenuUsuario itemMenuUsuarioNoEnlace menuCabeceraUltimoItem" id="menuUsuarioLogout">Cerrar sesi&oacute;n</li>
			</ul>
		</div>
		
    <?php	
		break;

	default:
		?>
		<div id="cajaRegistroLogin">
			<input type="button" class="registroLoginBtn" id="login" value="Identif&iacute;cate"/>
			<input type="button" class="registroLoginBtn" id="registro" value="Reg&iacute;strate"/>
		</div>
		

<?php
} // Llave de cierre del switch.
?>