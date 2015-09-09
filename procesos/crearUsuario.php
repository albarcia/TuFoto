<?php
// crearUsuario.php
require_once('../clases/usuario.php');
include('../includes/header.php');
//session_start();

$camposFormulario = array('nombre', 'apellidos', 'nombreUsuarioNuevo', 'email', 'email2', 'url', 'acerca');
$camposRequeridos = array('nombreUsuarioNuevo', 'contrasena', 'contrasena2', 'email', 'email2');
$camposVacios = array();

$control = '';
if (isset ($_POST['control'])){
    $control = $_POST['control'];
}

switch ($control){
    case 'crear':
        
        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
        $apellidos = trim(filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING));
        $nombreUsuarioNuevo = trim(filter_input(INPUT_POST, 'nombreUsuarioNuevo', FILTER_SANITIZE_STRING));
        $contrasena = $_POST['contrasena'];
        $contrasena2 = $_POST['contrasena2'];
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $email2 = trim(filter_input(INPUT_POST, 'email2', FILTER_SANITIZE_EMAIL));
        $url = trim(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
        if (isset($url) && $url !=''){
            $_SESSION['url'] = $url;
            if (strtolower(substr($url,0,11)) != 'http://www.' && strtolower(substr($url,0,4)) != 'www.'){
                $url = 'http://www.' . $url;
            } elseif (strtolower(substr($url,0,7)) != 'http://'){
                $url = 'http://' . $url;
            }
        }
        $acerca = trim(filter_input(INPUT_POST, 'acerca', FILTER_SANITIZE_STRING));
        
        // Se comprueba si todos los campos requeridos se han rellenado.
        $ok = true;
        //$camposVacios = array();
        $reEmail = '/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/';
        foreach ($camposRequeridos as $campo){
            if (!isset($$campo) || $$campo == ''){
                $ok = false;
                $camposVacios[] = $campo;
                $campoColorBorde = $campo . 'ColorBorde';
                $_SESSION[$campoColorBorde] = 'red';
            } else if ($campo == 'email' && (!preg_match($reEmail, $email) || $email != $email2)){
                $ok = false;
                $_SESSION['emailColorBorde'] = 'red';
                $_SESSION['email2ColorBorde'] = 'red';
                $camposVacios[] = 'email';
                $camposVacios[] = 'email2';
            }
        }
        // Se comprueba que las repeticiones de contraseña coinciden.
            if ($contrasena != $contrasena2){
                $ok = false;
                $_SESSION['contrasenaColorBorde'] = 'red';
                $_SESSION['contrasena2ColorBorde'] = 'red';
                $camposVacios[] = 'contrasena';
                $camposVacios[] = 'contrasena2';
                }
        
        $_SESSION['camposVacios'] = $camposVacios;
        
        if ($ok == false){
            // Si alguno de los campos requeridos no se ha rellenado o las contraseñas o los emails no coinciden.
            
            // Se rellenan las variables de sesión con los valores obtenidos del formulario.
            foreach ($camposFormulario as $campo){
                // La variable de sesión 'url' ya se ha asignado antes de ser modificada.
                if ($campo != "url"){
                    $_SESSION[$campo] = $$campo;
                }
            }
            echo '<div id="mensajeErrorFotoNueva" style="opacity: 1; margin-top: 200px">Faltan datos requeridos. En 3 segundos se recargar&aacute; el formulario</div>'
            ?><script>setTimeout(function() {location.href='procesos/crearUsuario.php';}, 3000);</script> <?php
        } else {
            // Si se han rellenado todos los campos requeridos.

            // Si tenemos la variable de sesión "ficheroTemporal" es porque el usuario ha cargado una imagen de perfil
            // en caso contrario el usuario no ha cargado imagen de perfil y se usa la imagen por defecto.
            if (isset($_SESSION['ficheroTemporal'])) {
            	$extension = substr($_SESSION['ficheroTemporal'], -3);
                $ficheroTemporal = $_SESSION['ficheroTemporal'];
                $nombreFichero = $nombreUsuarioNuevo . '.' . $extension;
                $nombreFicheroMini = $nombreUsuarioNuevo . 'Mini.' . $extension;
            } else {
                $nombreFichero = 'defecto';
            }
        
            // Se crea un nuevo usuario.
            $usuario = new Usuario(0, $nombre, $apellidos, $nombreUsuarioNuevo, $contrasena, $email, $url, $acerca, $nombreFichero);
            if (!$usuario->identificarUsuario('insertar')){
                // y se inserta en la base de datos.
                if ($usuario->insertarUsuario()){

                    // Cargamos los datos de usuario en el objeto.
                    $camposCriterio = array('nombreUsuario' => $nombreUsuarioNuevo);
                    $usuario->leerUsuario($camposCriterio);
                    $_SESSION['objetoUsuario'] = serialize($usuario);

                    // Creamos la estructura de carpetas para el nuevo usuario.
                    $carpetaUsuario = '../imagenes/usuarios/' . $nombreUsuarioNuevo;
                    mkdir($carpetaUsuario . '/originales', 0777, true);
                    mkdir($carpetaUsuario . '/muestra', 0777, false);
                    mkdir($carpetaUsuario . '/miniaturas', 0777, false);
                    mkdir($carpetaUsuario . '/perfil', 0777, false);

                    // Se copia la imagen de perfil desde la carpeta temporal a la carpeta del usuario. Siempre y cuando 
                    // haya seleccionado una y no utilice la imagen por defecto.
                    if ($nombreFichero != 'defecto'){
                        $rutaFinalFotoPerfil = $carpetaUsuario . '/perfil/' . $nombreFichero;
                        $rutaFinalFotoPerfilMini = $carpetaUsuario . '/perfil/' . $nombreFicheroMini;
                        if (!copy($ficheroTemporal, $rutaFinalFotoPerfil)){
                            $camposValores = array('foto' => 'defecto');
                            $usuario->modificarUsuario($camposValores);
                            //echo 'Se ha producido un error al copiar la imagen de perfil. Se ha asignado la imagen por defecto.';
                        } else {
                            include_once('../libreriaPHP/imagenes.php');
                            redimensionar($ficheroTemporal, $rutaFinalFotoPerfilMini, 50, 50, 'mini');
                        }
                        unlink($_SESSION['ficheroTemporal']);
                        unset($_SESSION['ficheroTemporal']);
                    }
                    // Se inicia sesión con el usuario recién creado.
                    $_SESSION['idUsuario'] = $usuario->getIdUsuario();
                    $_SESSION['nombreUsuario'] = $usuario->getNombreUsuario();
                    $_SESSION['objetoUsuario'] = serialize($usuario);
                    // Se informa que se ha insertado el usuario en la bdd
                    //echo '<div id="mensajeConfirmacionUsuarioNuevo" style="opacity: 1; margin-top: 200px">Usuario a&ntilde;adido. En 3 segundos ser&aacute; redirigido a la p&aacute;gina de su perfil.</div>'
                    ?><script>setTimeout(function() {location.href='procesos/mostrarUsuario.php';}, 3000);</script> <?php
                }
            } else {
                echo '<div id="mensajeErrorFotoNueva" style="opacity: 1; margin-top: 200px">El nombre de usuario ya existe. En 3 segundos se recargar&aacute; el formulario</div>'
            ?><script>setTimeout(function() {location.href='procesos/crearUsuario.php';}, 3000);</script> <?php
            }
        }
        break;
    default:
        if (isset($_SESSION['ficheroTemporal'])){
            unset($_SESSION['ficheroTemporal']);
        }
        $foto = 'imagenes/usuarios/perfilDefecto/perfilDefecto.jpg';
        $imagen = 'defecto';
        if (isset($nombreFichero)){
        	$imagen = 'usuario';
            $foto = $nombreFichero;
        }
        if (isset($_SESSION['camposVacios'])){
            $camposVacios = $_SESSION['camposVacios'];
        }
        list($ancho, $alto) = getimagesize('../' . $foto);
?>
<div id="contenedor">
    <div class="contenedorDatosTotal">
        <h1>Usuario nuevo</h1>
        <div class="lugarFotoComentarios">
            <div class="marcoFoto" id="marcoFotoNuevaActivo" style="width: <?php echo $ancho; ?>px; height: <?php echo $alto; ?>px;">
                <div id="mensajeErrorFotoNueva">&nbsp;</div>
                <img src="<?php echo $foto; ?>" id="fotoUsuario" style="opacity: 1;" width="<?php echo $ancho; ?>px" height="<?php echo $alto; ?>px"/>
            </div>
            <div class="centradoMarcoFotoNueva" id="centradoMarcoFotoNuevaUsuario">
                <form action="" Method="POST" id="fotoFormulario" enctype="multipart/form-data">
                    <div class="customInputFile mosaicoBtn" id="crearFotoUsuarioBtn">
                        <input type="file" class="inputFile" id="nuevaFotoUsuarioNuevo" onchange="enviarFotoUsuario('#nuevaFotoUsuarioNuevo')"/>
                        Elige foto de usuario
                    </div>
                </form>
            </div> 
        </div>
        <aside>
            <div id="externoDatosFoto">
                <div id="datosFoto">
                    <form action="procesos/crearUsuario.php" method="POST">
                
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="nombre" size="60" value="<?php echo $_SESSION['nombre'] ?>" placeholder="Nombre">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="apellidos" size="60" value="<?php echo $_SESSION['apellidos'] ?>" placeholder="Apellidos">
                        </div>    
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="nombreUsuarioNuevo" size="60" value="<?php echo $_SESSION['nombreUsuarioNuevo'] ?>" style="border-color: <?php echo $_SESSION['nombreUsuarioNuevoColorBorde'] ?>;" 
                            placeholder="Nombre de usuario*">
                        </div>    
                        <div class="datoDatosFoto">    
                            <input type="password" class="inputDatosFoto" name="contrasena" size="60" style="border-color: <?php echo $_SESSION['contrasenaColorBorde'] ?>;" placeholder="Contrase&ntilde;a*">
                        </div>    
                        <div class="datoDatosFoto">    
                            <input type="password" class="inputDatosFoto" name="contrasena2" size="60" style="border-color: <?php echo $_SESSION['contrasena2ColorBorde'] ?>;" placeholder="Repita contrase&ntilde;a*">
                        </div>    
                        <div class="datoDatosFoto">    
                            <input type="text" class="inputDatosFoto" name="email" size="60" value="<?php echo $_SESSION['email'] ?>" style="border-color: <?php echo $_SESSION['emailColorBorde'] ?>;" placeholder="Email*">
                        </div>    
                        <div class="datoDatosFoto">    
                            <input type="text" class="inputDatosFoto" name="email2" size="60" value="<?php echo $_SESSION['email2'] ?>" style="border-color: <?php echo $_SESSION['email2ColorBorde'] ?>;" placeholder="Repita email*">
                        </div>    
                        <div class="datoDatosFoto">    
                            <input type="text" class="inputDatosFoto" name="url" size="60" value="<?php echo $_SESSION['url'] ?>" placeholder="URL">
                        </div>    
                        <div class="datoDatosFoto">    
                            <textarea name="acerca" class="inputDatosFoto inputDatosFotoTextarea" rows="5" cols="40" style="resize: none;" placeholder="Acerca de..."><?php echo $_SESSION['acerca'] ?></textarea>
                        </div>    
                            <input type="submit" class="mosaicoBtn" id="crearUsuario" value="Crear usuario">
                        <input type="hidden" name="control" value="crear">
                        <input type="hidden" name="foto" value="<?php echo $foto; ?>">
                        <input type="hidden" name="imagen" value="<?php echo $imagen ?>">
                    </form>
                </div>
            </div>   
        </aside>
        <div class="blanco"></div>
    </div><!-- contenedorDatosTotal -->
</div><!-- contenedor -->
<?php
            // Vaciamos las variables de sesión.
            foreach ($camposFormulario as $campo){
                $_SESSION[$campo] = "";
            }
            
            // Restablecemos el color del borde de los campos que estaban vacíos.
            if (!empty($camposVacios)){
                foreach ($camposVacios as $campo){
                    $campoColorBorde = $campo . 'ColorBorde';
                    $_SESSION[$campoColorBorde] = '';
                }
                // Vaciamos la lista de campos vacíos o erróneos.
                unset($_SESSION['camposVacios']);
                //unset($camposVacios);
            }
}
include('../includes/footer.php');
?>