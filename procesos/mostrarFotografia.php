<?php
// mostrarFotografia.php
require_once('../clases/fotografia.php');
include_once('../clases/usuario.php');
include('../includes/header.php');

// Recuperamos la colección de fotografías desde la variable de sesión.
if (isset($_SESSION['colFotografias'])){
    $colFotografias = $_SESSION['colFotografias'];
    $existeColFotografias = true;
}
// Recuperamos el id de la foto.
if (isset($_GET['idFoto'])){
    $idFoto = $_GET['idFoto'];
} elseif (isset($_POST['idFoto'])){
    $idFoto = $_POST['idFoto'];
}
$_SESSION['idFoto'] = $idFoto;
$foto = new Fotografia($idFoto);
// Sumamos una visita más al contador de visitas.
$camposCriterio = array("idFoto" => $idFoto);
$foto->leerFotografia($camposCriterio);
$sesionUsuarioActiva = false;
if (isset($_SESSION['idUsuario'])){
    $sesionUsuarioActiva = true;
    $idUsuario = $_SESSION['idUsuario'];
}
// Si hay una sesión de usuario activa y el usuario activo es diferente del usuario propietario de la foto
// se suma una visita. Impedimos que las visitas del propietario se sumen para evitar que adultere el 
// contador de visitas.
if ($sesionUsuarioActiva && $idUsuario != $foto->getIdUsuario()){
    $foto->sumaVisita();
}

// Si el usuario activo es el mismo que el propietario de la foto y ya ha dado permiso de descarga de la foto
// activamos el botón para denegar ese permiso. Si no ha dado permiso, activamos el botón para darlo.
if ($sesionUsuarioActiva && $idUsuario == $foto->getIdUsuario()){
    if ($foto->getDescarga()){
        $idBotonPermisoDescarga = 'denegarDescargaFotoBtn';
        $textoBotonPermisoDescarga = 'Denegar descarga fotograf&iacute;a';
    } else {
        $idBotonPermisoDescarga = 'permitirDescargaFotoBtn';
        $textoBotonPermisoDescarga = 'Autorizar descarga fotograf&iacute;a';
    }
}
// Leemos la información de la fotografía de nuevo para que contenga el número de visitas actualizado.
$foto->leerFotografia($camposCriterio);
$usuario = new Usuario($foto->getIdUsuario());
$camposCriterio = array("idUsuario" => $foto->getIdUsuario());
$usuario->leerUsuario($camposCriterio);
$nombreUsuario = $foto->getNombreUsuario();
$fotoUsuarioFoto = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
if ($usuario->getFoto() != "defecto"){
    $fotoUsuarioFoto = 'imagenes/usuarios/' . $nombreUsuario . '/perfil/' . $nombreUsuario . 'Mini.jpg'; 
}
// Si no existe la fotografía personaizada del usuario cargamos la foto de perfil por defecto.
if (!file_exists('../' . $fotoUsuario)){
    $fotoUsuarioFoto = 'imagenes/usuarios/perfilDefecto/perfilDefectoMini.jpg';
}
$rutaFotografia = 'imagenes/usuarios/' . $nombreUsuario . '/muestra/' . $foto->getNombreFichero();
// Calculamos las dimensiones de la fotografía.
list($ancho, $alto) = getimagesize("../" . $rutaFotografia);

// --------------------------------------------------********---------------------------------------------
//echo serialize($foto);
// --------------------------------------------------********---------------------------------------------
?>
<div id="contenedor">
    <div class="contenedorDatosTotal">
        <h1 title="<?php echo $foto->getTitulo(); ?>"><?php echo $foto->getTitulo(); ?></h1>
        <div class="lugarFotoComentarios">
            <div class="marcoFoto" style="width: <?php echo $ancho; ?>px; height: <?php echo $alto; ?>px;">
                <img src="<?php echo $rutaFotografia; ?>" id="<?php echo $foto->getIdFoto(); ?>" width="<?php echo $ancho; ?>" height="<?php echo $alto; ?>px" alt="<?php $foto->getTitulo(); ?>px">
                <?php // Comprobaciones para los diferentes modos del botón de descarga de la fotografía.
                if ($sesionUsuarioActiva){
                    if ($idUsuario != $foto->getIdUsuario()){
                        if ($foto->getDescarga()){ ?>
                            <div id="descargaFoto" style="width: <?php echo $ancho; ?>px;">
                                <input type="button" class="mosaicoBtn" id="descargarFotoBtn" value="Descargar fotograf&iacute;a"/>
                            </div>
                        <?php }
                    } else { ?>
                        <div id="descargaFoto"style="width: <?php echo $ancho; ?>px;">
                            <input type="button" class="mosaicoBtn permisoDescargaFotoBtn" id="<?php echo $idBotonPermisoDescarga; ?>" value="<?php echo $textoBotonPermisoDescarga; ?>"/>
                        </div>
                <?php } 
                } // Cierre del if que comprueba si hay sesión activa ?>
            </div>
            
            <?php include ("mostrarCrearComentarios.php"); ?>
        </div>
            <aside>
                <div id="externoDatosFoto">
                    <div id="datosFoto">
                        <div class="seccionDatosFoto" id="fotoNombreUsuario">
                            <div class="fotoUsuarioPequena">
                                <img src="<?php echo $fotoUsuarioFoto; ?>" alt="Foto de usuario"/> 
                            </div>
                            <div class="nombreUsuarioDatosFoto" id="<?php echo $foto->getNombreUsuario(); ?>">
                                <?php echo $foto->getNombreUsuario(); ?>
                            </div>
                        </div>
                        <div class="blanco"></div>
                        <div class="seccionDatosFoto" id="numerosFoto">
                            <div id="ApartadoValoracion">
                                <div>Valoraci&oacute;n</div>
                                <div id="valoracionNumero"><?php echo $foto->getPuntuacion(); ?></div>
                            </div>
                        
                            <div id="likeFavoritoVisita">
                                <div class="blanco"></div>
                                <div class="likeFavoritoVisitaNumero" id="likes"><?php echo $foto->getLikes(); ?></div><div>Me gusta</div>
                                <div class="blanco"></div>
                                <div class="likeFavoritoVisitaNumero" id="favoritos"><?php echo $foto->getFavoritos(); ?></div><div>Favoritos</div>
                                <div class="blanco"></div>
                                <div class="likeFavoritoVisitaNumero" id="visitas"><?php echo $foto->getVisitas(); ?></div><div>Visitas</div>
                            </div>
                            <div class="blanco"></div>
                        </div>
                        <?php
                        // Si el usuario logeado es el mismo que el propietario de la foto no se le muestran los botones de  'like' y 'favorito'.
                        if ($sesionUsuarioActiva && ($idUsuario != $foto->getIdUsuario())){
                            // Comprobamos si a este usuario ya le gusta esta foto. Si es así deshabilitamos el botón 'Me gusta'.
                            //include_once ('../clases/like.php');
                            $like = new Like(0, $idFoto, $idUsuario);
                            $camposCriterio = array("idFoto" => $idFoto, "idUsuario" => $idUsuario);
                            $disabledLike = '';
                            $classLike = 'likeFavoritoBtnEnabled likeBtnEnabled';
                            if ($like->leerLike($camposCriterio)){
                                $disabledLike = 'disabled="disabled"';
                                $classLike = 'likeBtnDisabled';
                            }
                            // Comprobamos si esta foto ya está entre las favoritas de este usuario. Si es así deshabilitamos el botón 'Favorito'.
                            //include_once ('../clases/Favorito.php');
                            $favorito = new Favorito(0, $idFoto, $idUsuario);
                            $camposCriterio = array("idFoto" => $idFoto, "idUsuario" => $idUsuario);
                            $disabledFavorito = '';
                            $classFavorito = 'likeFavoritoBtnEnabled favoritoBtnEnabled';
                            if ($favorito->leerFavorito($camposCriterio)){
                                $disabledFavorito = 'disabled="disabled"';
                                $classFavorito = 'favoritoBtnDisabled';
                            }
                        ?>
                           <div class="seccionDatosFoto" id="likeFavoritoBotones">
                                <input type="hidden" id="idUsuario" value="<?php echo $idUsuario; ?>"/>
                                <input type="button" id="g<?php echo $idFoto; ?>" class="likeFavoritoBtn <?php echo $classLike; ?>" value="Me gusta" <?php echo $disabledLike ?>/>
                                <input type="button" id="f<?php echo $idFoto; ?>" class="likeFavoritoBtn <?php echo $classFavorito; ?>" value="&#9829" <?php echo $disabledFavorito ?>/>
                                <div class="blanco"></div>
                            </div>
                        <?php
                        } // Llave de cierre del 'if' anterior donde se comprueba si el usuario logueado es el mismo que el propietario de la foto.
                        if ($foto->getDescripcion()){
                        ?>
                            <div class="seccionDatosFoto" id="seccionDescripcion">
                                <div id="descripcionDatosFotoExterno">
                                    <div id="descripcionDatosFoto">
                                        <?php echo $foto->getDescripcion(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        } // Cierre del if anterior.
                        if ($foto->getLatitud() && $foto->getLongitud()){
                        ?>
                            <div class="seccionDatosFoto" id="seccionMapa">
                                <div class="mapaDatosFoto" id="divMapaDatosFoto">
                                    <!-- Aquí se coloca el mapa. -->
                                    <script>mostrarMapa(<?php echo $foto->getLatitud(); ?>, <?php echo $foto->getLongitud(); ?>);</script>
                                </div>
                            </div>
                        <?php
                        } // Cierre del if anterior.
                        if ($foto->getEtiquetas()){
                        ?>
                        <div class="seccionDatosFoto" id="etiquetasDatosFoto">
                            <div id="tituloEtiquetasDatosFoto"></div>
                            <div class="blanco"></div>
                            <?php
                            // Cargamos las etiquetas en un array para mostrarlas como enlaces.
                            $etiquetas = explode(',', $foto->getEtiquetas());
                            $i = 0;
                            foreach($etiquetas as $etiqueta){
                                echo '<span class="etiqueta" id="' . $etiqueta . '">' . $etiqueta . '</span>';
                                if ($i < (count($etiquetas) - 1)){
                                    echo ',';
                                }
                                $i++;
                            }
                            ?>
                        </div>
                        <?php
                        } // Cierre del if anterior.
                        ?>
                        <div class="seccionDatosFoto" id="seccionOtrosDatos">
                            <?php
                                if ($foto->getCategoria() != 'sinCategoria'){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Categor&iacute;a</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getCategoria() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getFechaTomada() != '0000-00-00'){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Tomada</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . date('d-m-Y', strtotime($foto->getFechaTomada())) . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getFechaSubida() != '0000-00-00'){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Subida</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . date('d-m-Y', strtotime($foto->getFechaSubida())) . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getCamara()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">C&aacute;mara</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getCamara() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getLente()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Lente</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getLente() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getDistanciaFocal()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Distancia focal</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getDistanciafocal() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getVelocidad()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Velocidad</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getVelocidad() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getApertura()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Apertura</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getApertura() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                                if ($foto->getIso()){
                                    echo '<div class="textoOtrosDatos tituloOtrosDatos">Iso</div>';
                                    echo '<div class="textoOtrosDatos datoOtrosDatos">' . $foto->getIso() . '</div>';
                                    echo '<div class="blanco"></div>';
                                }
                            ?>
                        </div>
                        <?php
                        if ($existeColFotografias){
                        ?>
                        <div class='seccionDatosFoto' id='seccionColeccionActiva'>
                            <div id='tiraImagenesExterior'>
                                <div id='tiraImagenesInterior' style='width: <?php echo (count($colFotografias) + 1) * 50; ?>px;'>
                                    <?php
                                    foreach ($colFotografias as $fotoTemp){
                                        $fotoMosaico = unserialize($fotoTemp);
                                        ?>
                                        <div class="imagenTira">
                                            <a href="procesos/mostrarFotografia.php?idFoto=<?php echo $fotoMosaico->getIdFoto(); ?>">
                                                <img src="imagenes/usuarios/<?php echo $fotoMosaico->getNombreUsuario(); ?>/miniaturas/<?php echo $fotoMosaico->getNombreFichero(); ?>"/>
                                            </a>
                                        </div>
                                    <?php
                                    } // Cierre del foreach.
                                    ?>
                                </div>
                            </div>
                            <div class="blanco"></div>
                        </div>
                        <?php
                        } // cierre del if.
                        if ($sesionUsuarioActiva && ($idUsuario == $foto->getIdUsuario())){
                        ?>
                            <div class="seccionDatosFoto" id="seccionEliminarImagen">
                                <input type="button" id="<?php echo $idFoto; ?>" class="likeFavoritoBtn eliminarImagenBtn eliminarImagen" value="Eliminar fotograf&iacute;a"/>
                            </div>
                        <?php
                        } // Cierre del if.
                        ?>
                    </div>
                </div>
            </aside>
            <div class="blanco"></div>
        </div> <!-- Cierre del div con id="contenedorFotoTotal" -->

    </div> <!-- Cierre del div con id="contenedor" -->
    <div class="blanco"></div>
<?php
include('../includes/footer.php');
?>