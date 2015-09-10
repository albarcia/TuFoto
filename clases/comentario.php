<?php
// comentario.php

class Comentario{
	
	private $idComentario;
	private $idFoto;
	private $idUsuario;
	private $fecha;
	private $texto;
	
	// Constructor.
    function Comentario($idFoto, $idUsuario, $fecha, $texto){
        
        $this->idFoto = $idFoto;
        $this->idUsuario = $idUsuario;
        $this->fecha = $fecha;
        $this->texto = $texto;
    }
    
    // Getters.
    function getIdComentario(){return $this->idComentario;}
    function getIdFoto(){return $this->idFoto;}
    function getIdUsuario(){return $this->idUsuario;}
    function getFecha(){return $this->fecha;}
    function getTexto(){return $this->texto;}
    
    function insertarComentario(){
    	// Crear un objeto Db y conectar con la base de datos.
    	if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
    	$db = new AccesoDb();
    	$con = $db->conectar();
    
    	// Sentencia INSERT para insertar un comentario.
    	$sql = "INSERT INTO comentario (idFoto, idUsuario, fecha, texto) VALUES ('";
    	$sql .= $this->idFoto . "', '";
    	$sql .= $this->idUsuario . "', ";
    	$sql .= "NOW(), '";
    	$sql .= utf8_decode($this->texto) . "');";
    
        $ok = false;
    	// Ejecución de la sentencia SQL.
    	if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
    
    	// Desconexión del servidor de base de datos.
    	$db->desconectar();

        return $ok;
    }
    
    function leerComentario(){

		// Crear un objeto Db y conectar con la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();

		// Sentencia SELECT para leer un comentario determinado.
		$sql = "SELECT idFoto, idUsuario, fecha, texto FROM comentario ";
		$sql .= "WHERE idComentario ='" . $this->idComentario . "';";

		$resultado = $db->ejecutarSQL($sql);

		// Desconexión del servidor de base de datos.
		$db->desconectar();

		// Obtener la fila de datos.
		$fila = $db->siguienteFila($resultado);

		// Rellenar las propiedades del objeto con los datos obtenidos de la bdd.
		$this->idFoto = $fila['idFoto'];
		$this->idUsuario = $fila['idUsuario'];
		$this->fecha = $fila['fecha'];
		$this->texto = utf8_encode($fila['texto']);
		echo $this->texto;
    }
    
    function eliminarComentario(){
    
		// Crear un objeto Db y conectar con la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		 
		// Ejecución de la sentencia SQL que comprueba si el comentario existe en la bdd.
		$db->ejecutarSQL($sql);


		// Sentencia DELETE para eliminar el comentario.
		$sql = "DELETE FROM comentario WHERE idComentario = " . $this->idComentario . ";";
		 
		// Ejecución de la sentencia SQL que elimina la foto de la bdd.
		$db->ejecutarSQL($sql);
		 
		// Desconexión del servidor de base de datos.
		$db->desconectar();
    }
    
}
?>