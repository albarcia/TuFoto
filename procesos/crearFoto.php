<?php
// crearFoto.php
session_start();
require_once('../clases/categoria.php');
require_once('../clases/fotografia.php');
require_once('../libreriaPHP/imagenes.php');
include('../includes/header.php');

if (isset($_SESSION['idUsuario'])){
    $idUsuario = $_SESSION['idUsuario'];
} else {
    ?><script>location.href='index.php';</script><?php
}

$camposFormulario = array('titulo', 'descripcion', 'categoria', 'fechaTomada', 'camara', 'lente', 'distanciaFocal', 'velocidad', 'apertura', 'iso', 'etiquetas');
$control = "";
if (isset ($_POST['control'])){
    $control = $_POST['control'];
} elseif (isset($_GET['control'])) {
    $control = $_GET['control'];
}

switch ($control){
    case "grabar":
        $fechaSeparada = explode("/", $_POST['fechaTomada']);
        $fechaUnida = $fechaSeparada[2] . "-" . $fechaSeparada[1] . "-" . $fechaSeparada[0];
        
        $ok = true;
        $_SESSION['colorBorde'] = "none";
        $idUsuario = $_SESSION['idUsuario'];
        $nombreUsuario = $_SESSION['nombreUsuario'];
        $ficheroTemporal = $_POST['ficheroTemporal'];
        $titulo = trim(filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING));
        // Comprobamos si el único campo requerido en este formulario se ha rellenado.
        if (!isset($titulo) || $titulo == ""){
            $ok = false;
            $_SESSION['colorBorde'] = "red";
        }
        $descripcion = trim(filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING));
        $categoria = trim(filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING));
        $fechaTomada = $fechaUnida;
        $camara = trim(filter_input(INPUT_POST, 'camara', FILTER_SANITIZE_STRING));
        $lente = trim(filter_input(INPUT_POST, 'lente', FILTER_SANITIZE_STRING));
        $distanciaFocal = trim(filter_input(INPUT_POST, 'distanciaFocal', FILTER_SANITIZE_STRING));
        $velocidad = trim(filter_input(INPUT_POST, 'velocidad', FILTER_SANITIZE_STRING));
        $apertura = trim(filter_input(INPUT_POST, 'apertura', FILTER_SANITIZE_STRING));
        $iso = trim(filter_input(INPUT_POST, 'iso', FILTER_SANITIZE_STRING));
        $etiquetas = strtolower(trim(filter_input(INPUT_POST, 'etiquetas', FILTER_SANITIZE_STRING)));
        $latitud = $_SESSION['latitud'];
        $longitud = $_SESSION['longitud'];
        $descarga = false;
        if ($_POST['permitirDescarga'] == 'si'){
            $descarga = true;
        }

        unset($_SESSION['latitud']);
        unset($_SESSION['longitud']);
        
        if ($ok == false){
            // Si el campo requerido no se ha rellenado.
            // Se rellenan las variables de sesión con los valores obtenidos del formulario.
            foreach ($camposFormulario as $campo){
                $_SESSION[$campo] = $$campo;
            }
            ?><script>
                setTimeout(function() {location.href="procesos/crearFoto.php";}, 3000);
            </script> <?php
        } else {

            // Si el campo requerido sí se ha rellenado.
            // Abrimos y leemos el fichero de texto donde se guarda el nombre de la última foto subida. 
            $fichero = fopen('../imagenes/lastph.dat', 'a+');
            rewind($fichero);
            $ultimoNombre = fgets($fichero);
            if (!$ultimoNombre ){
                $ultimoNombre = 1;
            }
            fclose($fichero);

            $ficheroOriginalTemp = $_SESSION['ficheroOriginalTemp'];
            $ficheroMuestraTemp = $_SESSION['ficheroMuestraTemp'];
            $carpetaUsuario = "../imagenes/usuarios/" . $nombreUsuario;
            $extension = substr($ficheroOriginalTemp, -3);
            $nombreFichero = $ultimoNombre . "." . $extension;

            // Guardamos el nombre de esta foto en el fichero de texto como última foto subida.
            $ultimoNombre++;
            $fichero = fopen('../imagenes/lastph.dat', 'w');
            fwrite($fichero, $ultimoNombre);
            fclose($fichero);
            
            $ficherosGuardados = true;
            // Guardamos la imagen con el tamaño de muestra.
            $rutaFinalMuestra = $carpetaUsuario . "/muestra/" . $nombreFichero;
            // Como ya la hemos redimensionado al tamaño de muestra antes de guardarla
            // simplemente la copiamos a su ubicación definitiva.
            if (!copy($ficheroMuestraTemp, $rutaFinalMuestra)){
                $ficherosGuardados = false;
            }
            
            // Guardamos la miniatura de la imagen.
            $rutaFinalMiniatura = $carpetaUsuario . "/miniaturas/" . $nombreFichero;
            if (!redimensionar($ficheroOriginalTemp, $rutaFinalMiniatura, 50, 50, 'mini')){
                $ficherosGuardados = false;
            }

            // Guardamos la imagen original en su carpeta definitiva.
            $rutaFinalOriginal = $carpetaUsuario . "/originales/" . $nombreFichero;
            if (!copy($ficheroOriginalTemp, $rutaFinalOriginal)){
                $ficherosGuardados = false;
            }
            // y borramos los ficheros temporales.
            unlink($ficheroOriginalTemp);
            unlink($ficheroMuestraTemp);

            if ($ficherosGuardados){
                // Se crea una nueva fotografía.
                $foto = new Fotografia(0, $idUsuario, $nombreUsuario, $nombreFichero, $titulo, $descripcion, $categoria, ' ', 
                 $fechaTomada, $latitud, $longitud, $camara, $lente, $distanciaFocal, $velocidad, $apertura, $iso, $etiquetas,
                 0, 0, 0, 0, $descarga);
                
                // y se inserta en la base de datos.
                if ($foto->insertarFotografia()){
                    unset($_SESSION['ficheroOriginalTemp']);
                    unset($_SESSION['ficheroMuestraTemp']);
                } else {
                    unlink($rutaFinalMuestra);
                    unlink($rutaFinalMiniatura);
                    unlink($rutaFinalOriginal);
                }

            }
            include_once('../clases/colFotografia.php');
            $camposCriterio = array('idUsuario' => $idUsuario);
            $criterioOrdenacion = 'fechasubida';
            $comienzo = 0;
            $limite = 1;
            $colFotosUsuario = new ColFotografia($camposCriterio, $criterioOrdenacion, $comienzo, $limite);
            $coleccion = $colFotosUsuario->getColfotos();
            $idFotoNueva = $coleccion[0]->getIdFoto();
            ?><script>location.href='procesos/mostrarFotografia.php?idFoto=<?php echo $idFotoNueva; ?>';</script><?php
        }
        break;
    
    default:    
    // cargamos la lista de categorías desde la bdd para rellenar la lista de selección del formulario.
    $categorias = new Categoria();
    $listaCategorias = $categorias->leerListaCategorias();

    if (isset($_SESSION['ficheroMuestraTemp'])){
        $ficheroMuestraTemp = substr($_SESSION['ficheroMuestraTemp'], 3);
        list($ancho, $alto) = getimagesize($_SESSION['ficheroMuestraTemp']);
        $idMarco = 'marcofotoNuevaActivo';
        $idBotonGuardar = 'guardarFotoActivo';
    } else {
        $idMarco = 'marcoFotoNueva';
        $idBotonGuardar = 'guardarFoto';
    }
            
        
    // Creamos el formulario para solicitar la fotografía y sus datos.
?>
<div id="contenedor">
    <div class="contenedorDatosTotal">
        <h1>Subiendo una fotograf&iacute;a nueva</h1>
        <div class="lugarFotoComentarios">
            <div class="marcoFoto" id="marcoFotoNueva" style="width: <?php echo $ancho; ?>px; height: <?php echo $alto; ?>px;">
                <div id="mensajeErrorFotoNueva">&nbsp;</div>
                <?php
                if (isset($ficheroMuestraTemp)){
                ?>
                    <img src="<?php echo $ficheroMuestraTemp; ?>" id="fotoNueva" style="opacity: 1;" width="<?php echo $ancho; ?>px" height="<?php echo $alto; ?>px"/>
                <?php
                    unset($ficheroMuestraTemp);
                    unset($_SESSION['ficheroMuestraTemp']);
                } else {
                ?>
                <div class="centradoMarcoFotoNueva" id="multiusosFotoNueva">
                    <form action="" Method="POST" id="fotoFormulario" enctype="multipart/form-data">
                        <div class="customInputFile mosaicoBtn" id="crearFotoFotoBtn">
                            <input type="file" class="inputFile" id="nuevaFotoFoto" onchange="enviarFotoFoto('#nuevaFotoFoto')"/>
                            Examinar
                        </div>
                    </form>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <form action="procesos/crearFoto.php" method="POST">
            <aside>
                <div id="externoDatosFoto">
                    <div id="datosFoto">
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="titulo" size="60" value="<?php echo $_SESSION['titulo'] ?>" style="border-color: <?php echo $_SESSION['colorBorde'] ?>;" placeholder="T&iacute;tulo*">
                        </div>
                        <div class="datoDatosFoto">
                            <textarea name="descripcion" class="inputDatosFoto inputDatosFotoTextarea" placeholder="Descipci&oacute;n"><?php echo $_SESSION['descripcion'] ?></textarea>
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" id="fechaTomada" name="fechaTomada" size="12" value="<?php echo $_SESSION['fechaTomada']; ?>" placeholder="Fecha tomada" readonly="readonly">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="camara" size="60" value="<?php echo $_SESSION['camara'] ?>" placeholder="C&aacute;mara">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="lente" size="60" value="<?php echo $_SESSION['lente'] ?>" placeholder="Lente">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="distanciaFocal" size="60" value="<?php echo $_SESSION['distanciaFocal'] ?>" placeholder="Distancia focal">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="velocidad" size="60" value="<?php echo $_SESSION['velocidad'] ?>" placeholder="Velocidad">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="apertura" size="60" value="<?php echo $_SESSION['apertura'] ?>" placeholder="Apertura">
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="iso" size="60" value="<?php echo $_SESSION['iso'] ?>" placeholder="ISO">
                        </div>
                        <div class="datoDatosFoto">&nbsp;</div>
                        <div class="datoDatosFoto">
                            <select name="categoria"  class="inputDatosFoto inputDatosFotoSelect">
                                <option value="sinCategoria">Sin categor&iacute;a</option>
                                <?php
                                // Rellenamos la lista de categorías disponibles.
                                foreach ($listaCategorias as $value => $texto){
                                    
                                    $selected = "";
                                    if ($value == $_SESSION['categoria']){
                                        $selected = " selected=\"selected\" ";
                                    }
                                    echo "<option value=\"" . $texto . "\"" . $selected .  ">" . $texto . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="datoDatosFoto">
                            <input type="text" class="inputDatosFoto" name="etiquetas" size="60" value="<?php echo $_SESSION['etiquetas'] ?>" placeholder="Etiquetas">
                        </div>
                        <div class="datoDatosFoto" id="permitirDescargaCB">
                            <input type="checkbox" name="permitirDescarga" value="si" size="60"/>Permitir descargas de la fotograf&iacute;a
                        </div>
                        <div class="datoDatosFoto">&nbsp;</div>
                        <div class="datoDatosFoto">
                            <div id="map" class="mapaDatosFoto"><script>crearMapa()</script></div>
                            <input type="button" id="mapaBorrarMarcador" class="mosaicoBtn" value="Borrar marcador" onclick="mapaQuitarMarcador()">
                            <div class="blanco"></div>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="blanco"></div>
            <div><input type="submit" id="<?php echo $idBotonGuardar; ?>" class="mosaicoBtn" value="Guardar fotograf&iacute;a"></div>
            <input type="hidden" name="control" value="grabar">
            <input type="hidden" name="ficheroTemporal" value="<?php echo $ficheroTemporal ?>">
        </form>
        <?php
        // Vaciamos las variables de sesión.
            foreach ($camposFormulario as $campo){
                $_SESSION[$campo] = "";
            }
        // y devolvemos el color del borde del campo requerido a su color original.
        $_SESSION['colorBorde'] = "";
        ?>
        <div class="blanco"></div>
    </div><!-- Cierre del div 'contenedor'-->

</div><!-- Cierre del div 'contenedorDtosTotal'-->
<?php
} // Llave de cierre del switch.
include('../includes/footer.php');
?>