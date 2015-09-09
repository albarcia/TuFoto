<?php
// mostrarUsuario.php
require_once('../clases/usuario.php');
include('../includes/header.php');

if (isset($_SESSION['ficheroTemporal'])){
    unset($_SESSION['ficheroTemporal']);
}
if (isset($_SESSION['idUsuario'])){
    $idUsuario = $_SESSION['idUsuario'];
} else {
    ?><script>location.href='index.php';</script><?php
}
$usuario = new Usuario($idUsuario);
$camposCriterio = array("idUsuario" => $idUsuario);
$usuario->leerUsuario($camposCriterio);
$nombreUsuario = $usuario->getNombreUsuario();
$foto = 'imagenes/usuarios/perfilDefecto/perfilDefecto.jpg';
if ($usuario->getFoto() != 'defecto' && $usuario->getFoto() != ''){
    $foto = 'imagenes/usuarios/' . $nombreUsuario . '/perfil/' . $nombreUsuario . '.jpg'; 
}
list($ancho, $alto) = getimagesize('../' . $foto);
?>
<div id="contenedor">
    <div class="contenedorDatosTotal" id="ContenedorDatosTotalUsuario">
        <h1>Informaci&oacute;n de perfil - <?php echo $nombreUsuario; ?></h1>
        <div id="fotoDatosUsuario">
            <div class="marcoFoto" id="marcoFotoNuevaActivo" style="width: <?php echo $ancho; ?>px; height: <?php echo $alto; ?>px;">
                <!--<div id="mensajeErrorFotoNueva">&nbsp;</div>-->
                <img src="<?php echo $foto; ?>" id="fotoUsuario" style="opacity: 1;" width="<?php echo $ancho; ?>px" height="<?php echo $alto; ?>px"/>
            </div> 
            <div class="centradoMarcoFotoNueva" id="centradoMarcoFotoActivaUsuario">
                <form action="" Method="POST" id="fotoFormularioEditarUsuario" enctype="multipart/form-data">
                    <div class="customInputFile mosaicoBtn" id="cambiarFotoUsuarioBtn">
                        <input type="file" class="inputFile" id="nuevaFotoUsuario" onchange="enviarFotoUsuario('#nuevaFotoUsuario')"/>
                        Elige foto de usuario
                    </div>
                </form>
            </div>
            <div class="separador" id="separadorFotoDatosUsuario"></div>
            <div>
                <input type="button" class="mosaicoBtn" id="cambiarContrasenaBtn" value="Cambiar contrase&ntilde;a"/>
            </div>
            <div id="inputsCambiarContrasena">
                <div class="datoDato datoDatoContrasena">
                    <input type="password" class="inputDatoDato inputDatoDatoContrasena" id="datoDatoContrasena" name="contrasena" size="50" placeholder="Nueva contrase&ntilde;a"/>
                </div>
                <div class="datoDato datoDatoContrasena">
                    <input type="password" class="inputDatoDato inputDatoDatoContrasena" id="datoDatoContrasena2" name="contrasena2" size="50" placeholder="Repite nueva contrase&ntilde;a"/>
                </div>
            </div>
        </div>
        <div id="datosDatosUsuario">
            <div class="etiquetasDatos">
                <div class="etiquetaDato">Nombre: </div>
                <div class="datoDato">
                    <input type"text" class="inputDatoDato" id="datoDatoNombre" name="nombre" size="50" placeholder="¿Nos dices tu nombre?" value="<?php echo $usuario->getNombre(); ?>"/>
                </div>
                <div class="etiquetaDato">Apellidos: </div>
                <div class="datoDato">
                    <input type"text" class="inputDatoDato" id="datoDatoApellidos" name="apellidos" size="50" placeholder="¿Y tus apellidos?" value="<?php echo $usuario->getApellidos(); ?>"/>
                </div>
                <div class="etiquetaDato">email: </div>
                <div class="datoDato">
                    <input type"text" class="inputDatoDato" id="datoDatoEmail" name="email" size="50" placeholder="Dime tu correo electr&oacute;nico..." value="<?php echo $usuario->getEmail(); ?>"/>
                </div>
                <div id="datosUsuarioSegundoEmail">
                    <div class="etiquetaDato"> Repite email: </div>
                    <div class="datoDato">
                        <input type"text" class="inputDatoDato" id="datoDatoEmail2" name="email2" size="50" placeholder="Repite email..." value="<?php echo $usuario->getEmail(); ?>"/>
                    </div>
                </div>
                <div class="etiquetaDato">Url: </div>
                <div class="datoDato">
                    <input type"text" class="inputDatoDato" id="datoDatoUrl" name="url" size="50" placeholder="¿No tienes p&aacute;gina web?" value="<?php echo $usuario->getUrl(); ?>"/>
                </div>
                <div class="etiquetaDato">Acerca de: </div>
                <div class="datoDato">
                    <textarea class="inputDatoDato" name="acerca" id="datoDatoAcerca" rows="5" cols="40" placeholder="Dinos algo acerca de ti..."><?php echo $usuario->getAcerca(); ?></textarea>
                </div>
            </div>
            </div>
            <div class="blanco"></div>
            <div>
                <input type="button" class="likeFavoritoBtn eliminarImagenBtn" id="eliminarUsuarioBtn" value="Eliminar cuenta de usuario"/>
                <input type="button" class="mosaicoBtn" id="modificarUsuarioBtn" value="Modificar datos"/>
                <input type="hidden" id="idUsuario" value="<?php echo $idUsuario ?>"/>
                <input type="hidden" id="nombreUsuario" value="<?php echo $nombreUsuario ?>"/>
            </div>
        </div>
    </div>
</div>
<?php
include('../includes/footer.php');
?>