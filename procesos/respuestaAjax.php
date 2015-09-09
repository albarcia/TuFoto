<?php

// Scripts de respuesta a las peticiones ajax.

session_start();
$rutaRaiz = $_SESSION['rutaRaiz'];
$selector = $_POST['selector'];

switch ($selector) {
	case 'latLong':
	// Carga en variables de sesión la latitud y la longitud obtenidas en el mapa al posicionar una foto.
		$_SESSION['latitud'] = $_POST['latitud'];
		$_SESSION['longitud'] = $_POST['longitud'];

		break;

	case 'crearComentario':
	// Se guarda un nuevo comentario en la base de datos y se muestra sin recargar la página.
		$textoComentario = nl2br(trim(filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_STRING)));
		$fotoUsuario = $_POST['fotoUsuario'];
		$idUsuario = $_SESSION['idUsuario'];
		$idFoto = $_SESSION['idFoto'];
		$nombreUsuario = $_SESSION['nombreUsuario'];

		if ($textoComentario != ""){
			require_once('../clases/comentario.php');

			$comentario = new Comentario($idFoto, $idUsuario, 0, $_POST['comentario']);
			if ($comentario->insertarComentario()){
				$ahora = date("d-m-Y G:i");
				$salida = "<div class='nuevoComentarioCreado'>
								<div class='fotoUsuarioPequena'>
									<img src='" . $fotoUsuario . "' alt='foto de usuario'/>
								</div>
								<div class='datosComentario'>
									<p class='datosCreacionComentario'><span class='nombreUsuarioComentario' id='" . $nombreUsuario . "'>" . $nombreUsuario . "</span><span class='fechaComentario'>" . $ahora . "</span></p>
									<p class='textoComentario'>" . $textoComentario . "</p>
								</div>
								<div style='clear: both'></div>
							</div>";
			} else {

				$salida = 'Se ha producido un error en la gestión de su comentario.';
			}

			echo $salida;
		}
		break;

	case 'sumaFavorito':
		$idFoto = $_POST['idFoto'];
		$idUsuario = $_POST['idUsuario'];
		require_once('../clases/favorito.php');
		$favorito = new Favorito(0, $idFoto, $idUsuario);
		$favorito->InsertarFavorito();
		require_once('../clases/fotografia.php');
		$foto = new Fotografia($idFoto);
		$camposCriterio = array("idFoto" => $idFoto);
		$foto->leerFotografia($camposCriterio);
		$respuesta = $foto->sumaFavorito();
		// El tipo indica si se trata de un favorito (tipo 'f') o un me gusta (tipo 'l' de like).
		$respuesta['tipo'] = 'f';
		echo json_encode($respuesta);
		break;

	case 'sumaMeGusta':
		$idFoto = $_POST['idFoto'];
		$idUsuario = $_POST['idUsuario'];
		require_once('../clases/like.php');
		$like = new Like(0, $idFoto, $idUsuario);
		$like->InsertarLike();
		require_once('../clases/fotografia.php');
		$foto = new Fotografia($idFoto);
		$camposCriterio = array("idFoto" => $idFoto);
		$foto->leerFotografia($camposCriterio);
		$respuesta = $foto->sumaMeGusta();
		// El tipo indica si se trata de un favorito (tipo 'f') o un me gusta (tipo 'l' de like).
		$respuesta['tipo'] = 'l';
		echo json_encode($respuesta);
		break;

	case 'login':
		include('../clases/usuario.php');
		$loginNombreUsuario = trim(filter_input(INPUT_POST, 'loginNombreUsuario', FILTER_SANITIZE_STRING));
		$loginContrasena = trim(filter_input(INPUT_POST, 'loginContrasena', FILTER_SANITIZE_STRING));
		// Se crea un nuevo usuario.
        $usuario = new Usuario(0, "", "", $loginNombreUsuario, $loginContrasena, "", "", "",
                               "", "", "", true);
        $respuesta = false;
        // Se comprueba si está dado de alta en la base de datos y si la contraseña es correcta.
        $motivo = 'login';
        if ($usuario->identificarUsuario($motivo)){
        	$_SESSION['idUsuario'] = $usuario->getIdUsuario();
        	$_SESSION['nombreUsuario'] = $usuario->getNombreUsuario();
        	$_SESSION['objetoUsuario'] = serialize($usuario);
        	$respuesta = true;
    	}
    	echo $respuesta;
    	break;

	case 'logout':
		// Si se utiliza una cookie para propagar la sesión hay que eliminarla.
		if (ini_get('session.use_cookies')) {
		    $parametros = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
	        $parametros['path'], $parammetros['domain'],
	        $parametros['secure'], $parametros['httponly']
    		);
		}
		$_SESSION = array();
		session_destroy();
		break;

	case 'mosaicoUsuario':
		include('../includes/mosaico.php');
		if (isset($_POST['claveBusqueda']) && isset($_POST['valorBusqueda'])){
			$claveBusqueda = $_POST['claveBusqueda'];
			$valorBusqueda = utf8_decode($_POST['valorBusqueda']);
		}
		$frameMosaico = cargaMosaico('fechaSubida', $claveBusqueda, $valorBusqueda);
		if ($frameMosaico == 'no Fotos'){
			$respuesta = array('frameMosaico' => 'no Fotos');
		} else {
			$respuesta = array('criterioOrdenacion' => $_POST['criterioOrdenacion'],
								'limite' => $_POST['limite'],
								'pagina' => $_POST['pagina'],
								'claveBusqueda' => $_POST['claveBusqueda'],
								'valorBusqueda' => $_POST['valorBusqueda'],
								'frameMosaico' => $frameMosaico);
		}
		echo json_encode($respuesta);
		break;

	case 'eliminarFoto':
		include_once('../clases/fotografia.php');
		$idFoto = $_POST['idFoto'];
		$foto = new Fotografia($idFoto);
		$camposCriterio = array("idFoto" => $idFoto);
		$foto->leerFotografia($camposCriterio);
		$resultado = $foto->eliminarFoto();
		echo $resultado;
		break;

	case 'eliminarUsuario':
		include_once('../clases/usuario.php');
		$idUsuario = $_POST['idUsuario'];
		$usuario = new Usuario($idUsuario);
		$camposCriterio = array("idUsuario" => $idUsuario);
		$usuario->leerUsuario($camposCriterio);
		$resultado = $usuario->eliminarUsuario();
		echo $resultado;
		break;

	case 'permisoDescargaFoto':
		$idFoto = $_POST['idFoto'];
		$permiso = $_POST['permiso']; // Si $permiso=true se permite descarga. Si permiso=false no se permite descarga.
		require_once('../clases/fotografia.php');
		$foto = new Fotografia($idFoto);
		$camposCriterio = array("idFoto" => $idFoto);
		$foto->leerFotografia($camposCriterio);
		$camposValores = array('descarga' => $permiso);
		$resultado = false;
        if ($foto->modificarFotografia($camposValores)){
        	$resultado = true;
        }
        echo $resultado;
		break;

	case 'descargarFoto':
		$idFoto = $_POST['idFoto'];
		$idUsuario = $_POST['idUsuario'];
		$nombreUsuarioPropietarioFoto = $_POST['nombreUsuarioPropietarioFoto'];
		require_once('../clases/descarga.php');
		$descarga = new Descarga(0, $idFoto, $idUsuario);
		$descarga->InsertarDescarga();
		require_once('../clases/fotografia.php');
		$foto = new Fotografia($idFoto);
		$camposCriterio = array("idFoto" => $idFoto);
		$foto->leerFotografia($camposCriterio);
		$nombreFicheroFoto = $foto->getNombreFichero();
		$foto->sumaDescarga();

		$respuesta = array('fichero' => $nombreFicheroFoto,
							'usuario' => $nombreUsuarioPropietarioFoto);

		echo json_encode($respuesta);
		break;

	case 'modificarUsuario':
		$idUsuario = $_POST['idUsuario'];
		$nombreUsuario = $_POST['nombreUsuario'];
		$camposValores = array();
		$camposModificables = array('nombre', 'apellidos', 'email', 'url', 'acerca', 'contrasena');
		foreach($camposModificables as $campo){
			if ($campo != 'contrasena'){
				$camposValores[$campo] = $_POST[$campo];
			} elseif ($_POST['contrasena'] != ''){
				$camposValores['contrasena'] = $_POST['contrasena'];
			}
		}
		$fotoCambiada = false;
		if (isset($_SESSION['ficheroTemporal'])) {
			$fotoCambiada = true;
        	$extension = substr($_SESSION['ficheroTemporal'], -3);
            $ficheroTemporal = $_SESSION['ficheroTemporal'];
            $nombreFichero = $nombreUsuario . '.' . $extension;
            $nombreFicheroMini = $nombreUsuario . 'Mini.' . $extension;
            $camposValores['foto'] = $nombreFichero;
        }
        include_once('../clases/usuario.php');
        $usuario = new Usuario($idUsuario);
        $resultado = false;
        if ($usuario->modificarUsuario($camposValores)){
        	$resultado = true;
        	if ($fotoCambiada){
	        	$carpetaUsuario = '../imagenes/usuarios/' . $nombreUsuario;
	        	$rutaFinalFotoPerfil = $carpetaUsuario . '/perfil/' . $nombreFichero;
	        	unlink($rutaFinalFotoPerfil);
	        	copy($ficheroTemporal, $rutaFinalFotoPerfil);
	            $rutaFinalFotoPerfilMini = $carpetaUsuario . '/perfil/' . $nombreFicheroMini;
	            unlink($rutaFinalFotoPerfilMini);
	            include_once('../libreriaPHP/imagenes.php');
	            redimensionar($ficheroTemporal, $rutaFinalFotoPerfilMini, 50, 50, 'mini');
	            unlink($_SESSION['ficheroTemporal']);
	            unset($_SESSION['ficheroTemporal']);
	        }
        }
        if (resultado){
        	$camposCriterio = array('idUsuario'=> $idUsuario);
        	$usuario->leerUsuario($camposCriterio);
        	$_SESSION['objetoUsuario'] = serialize($usuario);
        }
        echo $resultado;
	break;
}
?>