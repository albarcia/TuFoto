<?php
// foto.php

class Fotografia{
    
    private $idFoto;
    private $idUsuario;
    private $nombreUsuario;
    private $nombreFichero;
    private $titulo;
    private $descripcion;
    private $categoria;
    private $fechaSubida;
    private $fechaTomada;
    private $latitud;
    private $longitud;
    private $camara;
    private $lente;
    private $distanciaFocal;
    private $velocidad;
    private $apertura;
    private $iso;
    private $etiquetas;
    private $likes;
    private $favoritos;
    private $visitas;
    private $puntuacion;
    private $descarga;
    private $descargas;
    
    // Constructor.
    function Fotografia($idfoto=0, $idUsuario="", $nombreUsuario="", $nombreFichero="", $titulo="", $descripcion="", $categoria="", $fechaSubida="", $fechaTomada="", $latitud="", $longitud="", $camara="", $lente="", $distanciaFocal="", $velocidad="", $apertura="", $iso="", $etiquetas="", $likes=0, $favoritos=0, $visitas=0, $puntuacion=0, $descarga=0, $descargas=0){
        
    	$this->idFoto = $idfoto;
        $this->idUsuario = $idUsuario;
        $this->nombreUsuario = $nombreUsuario;
        $this->nombreFichero = $nombreFichero;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->categoria = $categoria;
        $this->fechaSubida = $fechaSubida;
        $this->fechaTomada = $fechaTomada;
        $this->latitud = $latitud;
        $this->longitud = $longitud;
        $this->camara = $camara;
        $this->lente = $lente;
        $this->distanciaFocal = $distanciaFocal;
        $this->velocidad = $velocidad;
        $this->apertura = $apertura;
        $this->iso = $iso;
        $this->etiquetas = $etiquetas;
        $this->likes = $likes;
        $this->favoritos = $favoritos;
        $this->visitas = $visitas;
        $this->puntuacion = $puntuacion;
        $this->descarga = $descarga;
        $this->descargas = $descargas;
    }
    
    // Getters.
    function getIdFoto(){return $this->idFoto;}
    function getIdUsuario(){return $this->idUsuario;}
    function getNombreUsuario(){return $this->nombreUsuario;}
    function getNombreFichero(){return $this->nombreFichero;}
    function getTitulo(){return $this->titulo;}
    function getDescripcion(){return $this->descripcion;}
    function getCategoria(){return $this->categoria;}
    function getFechaSubida(){return $this->fechaSubida;}
    function getFechaTomada(){return $this->fechaTomada;}
    function getLatitud(){return $this->latitud;}
    function getLongitud(){return $this->longitud;}
    function getCamara(){return $this->camara;}
    function getLente(){return $this->lente;}
    function getDistanciaFocal(){return $this->distanciaFocal;}
    function getVelocidad(){return $this->velocidad;}
    function getApertura(){return $this->apertura;}
    function getIso(){return $this->iso;}
    function getEtiquetas(){return $this->etiquetas;}
    function getLikes(){return $this->likes;}
    function getFavoritos(){return $this->favoritos;}
    function getVisitas(){return $this->visitas;}
    function getPuntuacion(){return $this->puntuacion;}
    function getDescarga(){return $this->descarga;}
    function getDescargas(){return $this->descargas;}

    function insertarFotografia(){
    	
        // Crear un objeto db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
        $db = new AccesoDb();
        $con = $db->conectar();
        
        // Sentencia INSERT para insertar una fotograf’a.
        $sql = "INSERT INTO fotografia (idUsuario, nombreUsuario, nombreFichero, titulo, descripcion, categoria, fechaSubida, fechaTomada, latitud, ";
        $sql .= "longitud, camara, lente, distanciaFocal, velocidad, apertura, iso, etiquetas, likes, favoritos, visitas, ";
        $sql .= "puntuacion, descarga, descargas) VALUES ('";
        $sql .= utf8_decode($this->idUsuario) . "', '";
        $sql .= utf8_decode($this->nombreUsuario) . "', '";
        $sql .= utf8_decode($this->nombreFichero) . "', '";
        $sql .= utf8_decode($this->titulo) . "', '";
        $sql .= utf8_decode($this->descripcion) . "', '";
        $sql .= utf8_decode($this->categoria) . "', ";
        $sql .= "NOW(), '";
        $sql .= utf8_decode($this->fechaTomada) . "', '";
        $sql .= utf8_decode($this->latitud) . "', '";
        $sql .= utf8_decode($this->longitud) . "', '";
        $sql .= utf8_decode($this->camara) . "', '";
        $sql .= utf8_decode($this->lente) . "', '";
        $sql .= utf8_decode($this->distanciaFocal) . "', '";
        $sql .= utf8_decode($this->velocidad) . "', '";
        $sql .= utf8_decode($this->apertura) . "', '";
        $sql .= utf8_decode($this->iso) . "', '";
        $sql .= utf8_decode($this->etiquetas) . "', '";
        $sql .= utf8_decode($this->likes) . "', '";
        $sql .= utf8_decode($this->favoritos) . "', '";
        $sql .= utf8_decode($this->visitas) . "', '";
        $sql .= utf8_decode($this->puntuacion) . "', '";
        $sql .= utf8_decode($this->descarga) . "', '";
        $sql .= utf8_decode($this->descargas) . "');";
        
        $ok = false;
        // Ejecución de la sentencia SQL.
        if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
        // Desconexión con el servidor de base de datos.
        $db->desconectar();

        return $ok;
    }
    
    function leerFotografia($camposCriterio){

        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectar con la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		// Sentencia SELECT para leer una noticia determinada.
        $i = 1;
		$sql = "SELECT idFoto, idUsuario, nombreUsuario, nombreFichero, titulo, descripcion, categoria, fechaSubida, fechaTomada, latitud, longitud, camara, lente, distanciaFocal, velocidad, apertura, iso, etiquetas, likes, favoritos, visitas, puntuacion, descarga, descargas FROM fotografia WHERE ";
		foreach ($camposCriterio as $campo => $valor) {
            $sql .= $campo . " = '" . $valor . "' ";
            if ($i < count($camposCriterio)){
                $sql .= " AND ";
            }
            $i++;
        }

		$resultado = $db->ejecutarSQL($sql);

		// Desconexión con el servidor de base de datos.
		$db->desconectar();

		// Obtener la fila de datos.
		$fila = $db->siguienteFila($resultado);
		
		// Rellenar las propiedades del objeto con los datos obtenidos de la bdd.
        $this->idFoto = $fila['idFoto'];
		$this->idUsuario = utf8_encode($fila['idUsuario']);
        $this->nombreUsuario = $fila['nombreUsuario'];
        $this->nombreFichero = $fila['nombreFichero'];
        $this->titulo = utf8_encode($fila['titulo']);
        $this->descripcion = utf8_encode($fila['descripcion']);
        $this->categoria = utf8_encode($fila['categoria']);
        $this->fechaSubida = $fila['fechaSubida'];
        $this->fechaTomada = $fila['fechaTomada'];
        $this->latitud = $fila['latitud'];
        $this->longitud = $fila['longitud'];
        $this->camara = utf8_encode($fila['camara']);
        $this->lente = utf8_encode($fila['lente']);
        $this->distanciaFocal = utf8_encode($fila['distanciaFocal']);
        $this->velocidad = utf8_encode($fila['velocidad']);
        $this->apertura = utf8_encode($fila['apertura']);
        $this->iso = utf8_encode($fila['iso']);
        $this->etiquetas = utf8_encode($fila['etiquetas']);
        $this->likes = $fila['likes'];
        $this->favoritos = $fila['favoritos'];
        $this->visitas = $fila['visitas'];
        $this->puntuacion = $fila['puntuacion'];
        $this->descarga = $fila['descarga'];
        $this->descargas = $fila['descargas'];
    }
    
    function modificarFotografia($camposValores){

    	// Crear un objeto Db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
    	$db = new AccesoDb();
    	$db->conectar();
    	
    	// Sentencia UPDATE para modificar la bdd.
    	$sql = "UPDATE fotografia SET ";
    	foreach ($camposValores as $campo => $valor){
    		$sql .= $campo . "=" . utf8_decode($valor) . ", ";
    	}
    	$sql = substr($sql, 0, -2);
    	$sql .= " WHERE idFoto = '" . $this->idFoto . "';";
    	
        $ok = false;
    	// Ejecución de la sentencia SQL.
    	if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
    	
    	// Desconexión del servidor de base de datos.
    	$db->desconectar();

        return $ok;
    }
    
    function eliminarFoto(){
		// Crear un objeto Db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();

        // Creamos una colección de todos sus likes y la eliminamos.
        include_once('colLikes.php');
        $colLikes = new ColLikes('idFoto', $this->idFoto);
        $colLikes->eliminarColeccionLikes();

        // Creamos una colección de todos sus favoritos y la eliminamos.
        include_once('colFavoritos.php');
        $colFavoritos = new ColFavoritos('idFoto', $this->idFoto);
        $colFavoritos->eliminarColeccionFavoritos();

        // Creamos una colección de todos sus descargas y la eliminamos.
        include_once('colDescargas.php');
        $colDescargas = new ColDescargas('idFoto', $this->idFoto);
        $colDescargas->eliminarColeccionDescargas();
    
    	// Creamos una colección de todos sus comentarios y la eliminamos.
        include_once('colComentario.php');
    	$colComentarios = new ColComentario('idFoto', $this->idFoto);
    	$colComentarios->eliminarColeccionComentarios();
    	
    	// Borrar del disco los ficheros (imágenes) asociados a esta foto.
        $ok = true;
        $fichero = '../imagenes/usuarios/' . $this->nombreUsuario . '/miniaturas/' . $this->nombreFichero;
    	if (!unlink($fichero)){
    		$ok = false;
    	}
        $fichero = '../imagenes/usuarios/' . $this->nombreUsuario . '/muestra/' . $this->nombreFichero;
        if (!unlink($fichero)){
            $ok = false;
        }
        $fichero = '../imagenes/usuarios/' . $this->nombreUsuario . '/originales/' . $this->nombreFichero;
        if (!unlink($fichero)){
            $ok = false;
        }

        if (!$ok){
            echo 'Se ha producido un error al acceder al disco para borrar los ficheros asociados a esta fotograf&iacute;a'; 
        }

        // Eliminamos los likes y favoritos de esta fotografía.

    	
    	// Sentencia DELETE para eliminar una noticia.
        $resultado = false;
    	if ($sql = "DELETE FROM fotografia WHERE idFoto = " . $this->idFoto . ";"){
            $resultado = true;
        }
    
    	// Ejecución de la sentencia SQL que elimina la foto de la bdd.
    	$db->ejecutarSQL($sql);
    
    	// Desconexión del servidor de base de datos.
    	$db->desconectar();

        return $resultado;
    }

    function sumaVisita(){
        // Se suma la visita y se modifica la puntuación en función del nuevo número de visitas.
        $visitas = ++ $this->visitas;
        $puntuacion = $this->calcularPuntuacion($visitas);
        
        $camposValores = array('visitas' => $visitas, 'puntuacion' => $puntuacion);
        $this->modificarFotografia($camposValores);
    }


    function sumaFavorito(){
        $favoritos = ++ $this->favoritos;
        $puntuacion = $this->calcularPuntuacion($this->visitas);
        
        $camposValores = array('favoritos' => $favoritos, 'puntuacion' => $puntuacion);
        $this->modificarFotografia($camposValores);

        $respuesta = array('favoritos' => $favoritos, 'puntuacion' => $puntuacion);
        return $respuesta;

    }

    function sumaMeGusta(){
        $likes = ++ $this->likes;
        $puntuacion = $this->calcularPuntuacion($this->visitas);
        
        $camposValores = array('likes' => $likes, 'puntuacion' => $puntuacion);
        $this->modificarFotografia($camposValores);

        $respuesta = array('likes' => $likes, 'puntuacion' => $puntuacion);
        return $respuesta;

    }

    function sumaDescarga(){
        $descargas = ++ $this->descargas;
        
        $camposValores = array('descargas' => $descargas);
        $this->modificarFotografia($camposValores);
    }

    private function calcularPuntuacion($visitas){
        return round((($this->likes * 25) + ($this->favoritos * 75)) / $visitas);
    }

}
?>