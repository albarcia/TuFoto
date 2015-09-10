<?php
// editarUsuario.php
require_once('../clases/usuario.php');
include('../includes/header.php');

$usuario = new Usuario($_SESSION['idUsuario']);
$camposCriterio = array('idUsuario' => $_SESSION['idUsuario']);
$usuario->leerUsuario($camposCriterio);

$camposFormulario = array('email', 'email2', 'url', 'acerca', 'preguntaContrasena', 'respuestaContrasena');
$camposVacios = array();
$camposValores = array();

$control = '';
if (isset ($_POST['control'])){
    $control = $_POST['control'];
}

switch ($control){
    case 'modificar':
          
        $contrasena = $_POST['contrasena'];
        $contrasena2 = $_POST['contrasena2'];
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $email2 = trim(filter_input(INPUT_POST, 'email2', FILTER_SANITIZE_EMAIL));
        $url = trim(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
        if (!empty($url)){
            $_SESSION['url'] = $url;
            if (strtolower(substr($url,0,11)) != 'http://www.' && strtolower(substr($url,0,4)) != 'www.'){
                $url = 'http://www.' . $url;
            } elseif (strtolower(substr($url,0,7)) != 'http://'){
                $url = "http://" . $url;
            }
            $camposValores['url'] = $url;
        }
        $acerca = trim(filter_input(INPUT_POST, 'acerca', FILTER_SANITIZE_STRING));
        $preguntaContrasena = trim(filter_input(INPUT_POST, 'preguntaContrasena', FILTER_SANITIZE_STRING));
        $respuestaContrasena = trim(filter_input(INPUT_POST, 'respuestaContrasena', FILTER_SANITIZE_STRING));
        
        // Se comprueba si todos los campos requeridos se han rellenado.
        $ok = true;

        // Se comprueba que las repeticiones de contraseña y email coinciden.
        if (!empty($contrasena)){
            if (empty($contrasena2) || (!empty($contrasena2) && ($contrasena != $contrasena2))){
                $ok = false;
                $_SESSION['contrasenaColorBorde'] = 'red';
                $_SESSION['contrasena2ColorBorde'] = 'red';
                $camposVacios[] = 'contrasena';
                $camposVacios[] = 'contrasena2';
            } else {
            	$camposValores['contrasena'] = $contrasena;
            }
        }

        if (!empty($email)){
            if (empty($email2) || (!empty($email2) && ($email != $email2))){
                $ok = false;
                $_SESSION['emailColorBorde'] = 'red';
                $_SESSION['email2ColorBorde'] = 'red';
                $camposVacios[] = 'email';
                $camposVacios[] = 'email2';
            } else {
            	$camposValores['email'] = $email;
            }
        }

        if (!empty($acerca)){
            $camposValores['acerca'] = $acerca;
        }

        if (!empty($preguntaContrasena)){
            if (empty($respuestaContrasena)){
                $ok = false;
                $_SESSION['preguntaContrasenaColorBorde'] = 'red';
                $_SESSION['respuestaContrasenaColorBorde'] = 'red';
                $camposVacios[] = 'preguntaContrasena';
                $camposVacios[] = 'respuestaContrasena';
            } else {
                $camposValores['preguntaContrasena'] = $preguntaContrasena;
                $camposValores['respuestaContrasena'] = $respuestaContrasena;
            }
        }

        $_SESSION['camposVacios'] = $camposVacios;
        
        if ($ok == false){
            // Si alguno de los campos requeridos no se ha rellenado o las contraseñas o los emails no coinciden.
            
            // Se rellenan las variables de sesión con los valores obtenidos del formulario.
            foreach ($camposFormulario as $campo){
                // La variable de sesión 'url' ya se ha asignado antes de ser modificada tras comprobar que el campo 'url' 
                // no estaba vacío.
                if ($campo != 'url'){
                    $_SESSION[$campo] = $$campo;
                }
            }
            ?><script>setTimeout(function() {location.href='procesos/editarUsuario.php';}, 3000);</script> <?php
        } else {
            // Si se han rellenado todos los campos requeridos.

            $extension = substr($_FILES['archivo']['name'], -3);
            $ficheroDestino = '../imagenes/' . $nombreUsuario . '/perfil/' .$nombreUsuario . '.' . $extension;
            if ($_POST['imagen'] == "usuario") {
            	
            	copy($_POST['foto'], $ficheroDestino);
            	unlink($_POST['foto']);
            }
            
            // Se modifican el usuario en al bdd.
            if ($usuario->modificarUsuario($camposValores)){
                unset($usuario);
                ?><script>setTimeout(function() {location.href='procesos/mostrarUsuario.php';}, 3000);</script> <?php
            }
        }
        break;
    
    default:
        $foto = 'imagenes/usuarios/perfilDefecto/perfilDefecto.jpg';
        $imagen = 'defecto';
        if ($usuario->getFoto() != 'defecto'){
            $foto = 'imagenes/usuarios/' . $usuario->getNombreUsuario() . '/perfil/' . $usuario->getFoto();
        }
        if (isset($nombreFichero)){
        	$imagen = 'usuario';
            $foto = $nombreFichero;
        }
        $camposVacios = $_SESSION['camposVacios'];

?>
<div id="temp" style="float: left; padding-top: 60px; text-align:center; width: 20%; height: 500px">
    <div style="margin: auto; border: 1px solid black; padding:3px; width: 100px; height: 150px;">
        <img src='<?php echo $foto; ?>' id="fotoUsuario" width="100px" height="150px" alt="imagen de perfil">
    </div>
    <br>
    <form action="procesos/fotoUsuario.php" Method="POST" id="fotoFormulario" enctype="multipart/form-data">
         <input type="file" id="foto" name="foto" value="" accept="image/jpg,image/gif,image/png" style="width: 120px;" onchange="enviar()"/><br>
    </form>
</div>
<form action="procesos/editarUsuario.php" method="POST">
    <h3 style="text-align: center;">Modificaci&oacute;n de datos de usuario</h3>
    <div style="float: left; width: 18%;">
        <table style="width: 100%;">
            <tr class="labelFormularioTemp"><td>Nombre: </td></tr>
            <tr class="labelFormularioTemp"><td>Apellidos: </td></tr>
            <tr class="labelFormularioTemp"><td>Nombre de usuario: </td></tr>
            <tr class="labelFormularioTemp"><td>Contrase&ntilde;a: </td></tr>
            <tr class="labelFormularioTemp"><td>Repita contrase&ntilde;a: </td></tr>
            <tr class="labelFormularioTemp"><td>email: </td></tr>
            <tr class="labelFormularioTemp"><td>Repita email: </td></tr>
            <tr class="labelFormularioTemp"><td>Url: </td></tr>
            <tr style="height: 80px; text-align: right; vertical-align: top;"><td>Acerca de: </td></tr>
            <tr class="labelFormularioTemp"><td>Pregunta de seguridad: </td></tr>
            <tr class="labelFormularioTemp"><td>Respuesta de seguridad: </td></tr>
        </table>
    </div>
    <div style="float: left; width: 60%; text-align: left;">
        <table style="width: 100%;">
            <tr><td><?php echo $usuario->getNombre(); ?></td></tr>
            <tr><td><?php echo $usuario->getApellidos(); ?></td></tr>
            <tr><td><?php echo $usuario->getNombreUsuario(); ?></td></tr>
            <tr><td><input type="text" name="contrasena" size="60" style="border-color: <?php echo $_SESSION['contrasenaColorBorde'] ?>;"  placeholder="Nueva contrase&ntilde;a"></td></tr>
            <tr><td><input type="text" name="contrasena2" size="60" style="border-color: <?php echo $_SESSION['contrasena2ColorBorde'] ?>;"  placeholder="Repita nueva contrase&ntilde;a"></td></tr>
            <tr><td><input type="text" name="email" size="60" style="border-color: <?php echo $_SESSION['emailColorBorde'] ?>;"  value="<?php echo $usuario->getEmail(); ?>"></td></tr>
            <tr><td><input type="text" name="email2" size="60" style="border-color: <?php echo $_SESSION['email2ColorBorde'] ?>;" placeholder="Repita nuevo email"></td></tr>
            <tr><td><input type="text" name="url" size="60" value="<?php echo $usuario->getUrl(); ?>" placeholder="URL"></td></tr>
            <tr><td><textarea name="acerca" rows="5" cols="40" style="resize: none;" placeholder="Acerca de..."><?php echo $usuario->getAcerca(); ?></textarea></td></tr>
            <tr><td><input type="text" name="preguntaContrasena" size="80" placeholder="Nueva pregunta de seguridad"></td></tr>
            <tr><td><input type="text" name="respuestaContrasena" size="80" style="border-color: <?php echo $_SESSION['respuestaContrasenaColorBorde'] ?>;"  placeholder="Nueva respuesta de seguridad"></td></tr>
            <tr><td><input type="submit" value="OK"></td></tr>
        </table>
    </div>
    <input type="hidden" name="control" value="modificar">
    <input type="hidden" name="foto" value="<?php echo $foto; ?>">
    <input type="hidden" name="imagen" value="<?php echo $imagen ?>">
</form>

<?php
} // Cierre del switch.
print ('<p><a href="index.php">Inicio</a></p>');
include('../includes/footer.php');
?>