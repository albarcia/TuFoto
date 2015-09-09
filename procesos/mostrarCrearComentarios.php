<div class="blanco"></div>
<div id="contenedorComentarios">
	<?php
	// Si se ha iniciado una sesión de usuario permitimos añadir comentarios.
	if (isset($_SESSION['idUsuario'])){
		$idUsuario = $_SESSION['idUsuario'];
		$nombreUsuario = $_SESSION['nombreUsuario'];
		$usuario = new Usuario($idUsuario);
		$camposCriterio = array("idUsuario" => $idUsuario);
		$usuario->leerUsuario($camposCriterio);
		$fotoUsuario = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
		if ($usuario->getFoto() != "defecto"){
		    $fotoUsuario = 'imagenes/usuarios/' . $nombreUsuario . '/perfil/' . $nombreUsuario . 'Mini.jpg'; 
		}
		?>
		<div id="anadirComentario">
			<div class="fotoUsuarioPequena">
				<img id="imgFotoUsuarioActivo" src="<?php echo $fotoUsuario; ?>" alt="foto de usuario">
			</div>
			<div id="textoComentarioNuevo">
				<form action="" method="POST">
					<textarea id="nuevoComentario" name="nuevoComentario" placeholder="Escribe tu comentario..."></textarea>
					<input type="button" class="mosaicoBtn" id="enviarComentario" name="ok" value="Comentar" onclick="crearComentario()">
					<input type="hidden" name="control" value="crear">
				</form>
			</div>
		</div>
	<?php

	} // Cierre del if que comprueba si se ha iniciado una sesión de usuario.
	?>
	<div style="clear: both; margin-bottom:15px"></div>
	<div id="coleccionComentarios">

	<?php
	$colComentario = new ColComentario('idFoto', $idFoto);
	$coleccion = $colComentario->getColComentarios();

	foreach ($coleccion as $comentario) {
		$usuario = new Usuario($comentario->getIdUsuario());
		$camposCriterio = array('idUsuario' => $comentario->getIdUsuario());
		if (!$usuario->leerUsuario($camposCriterio)){
			$nombreUsuario = 'Usuario-' . $comentario->getIdUsuario();
			$fotoUsuario = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
			$idUsuarioComentario = '0';
			$claseNombreUsuarioComentario = 'noUsuarioComentario';
		} else {
			$fotoUsuario = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
			if ($usuario->getFoto() != "defecto"){
				$fotoUsuario = 'imagenes/usuarios/' . $usuario->getNombreUsuario() . '/perfil/' . $usuario->getNombreUsuario() . 'Mini.jpg';
			}
			$nombreUsuario = $usuario->getNombreUsuario();
			$idUsuarioComentario = $usuario->getIdUsuario();
			$claseNombreUsuarioComentario = 'nombreUsuarioComentario';
		}
		$fechaComentario = date('d-m-Y G:i:s', strtotime($comentario->getFecha()));
		$salida = "<div class='comentario'>
						<div class='fotoUsuarioPequena'>
							<img src='" . $fotoUsuario . "' alt='foto de usuario'>
						</div>
						<div class='datosComentario'>
							<p class='datosCreacionComentario'><span class='" . $claseNombreUsuarioComentario . "' id='" . $nombreUsuario . "'>" . $nombreUsuario . "</span><span class='fechaComentario'>" . $fechaComentario . "</span></p>
							<p class='textoComentario'>" . nl2br($comentario->getTexto()) . "</p>
						</div>
						<div style='clear: both'></div>
					</div>";
		echo $salida;
	}
	?>
		</div> <!-- Cierre del <div> con id="coleccionComentarios" -->
</div> <!-- Cierre del <div> con id="contenedorComentarios" -->