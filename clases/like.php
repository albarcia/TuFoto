<?php
// likes.php

class Like{
	private $idLike;
	private $idFoto;
	private $idUsuario;
	private $fecha;

	// Constructor.
	function Like($idLike=0, $idFoto=0, $idUsuario=0, $fecha=0){
		$this->idFoto = $idFoto;
		$this->idUsuario = $idUsuario;
		$this->fecha = $fecha;
	}

	//Getters
	function getIdLike(){return $this->idLike;}
	function getIdFoto(){return $this->idFoto;}
	function getIdUsuario(){return $this->idUsuario;}
	function getFecha(){return $this->fecha;}

	function insertarLike(){
    	
    	if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
        $ok = false;
        // Crear un objeto db y conectar con la base de datos.
        $db = new AccesoDb();
        $con = $db->conectar();
        
        // Sentencia INSERT para insertar un like.
        $sql = "INSERT INTO likes (idFoto, idUsuario, fecha) VALUES ('";
        $sql .= $this->idFoto . "', '";
        $sql .= $this->idUsuario . "', ";
        $sql .= "NOW());";
        
        // Ejecución de la sentencia SQL.
        if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
        // Desconexión con el servidor de base de datos.
        $db->desconectar();

        return $ok;
    }

	function leerLike($camposCriterio){

		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectar con la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		// Sentencia SELECT para leer uno o varios likes determinados.
        $i = 1;
		$sql = "SELECT idLike, idFoto, idUsuario, fecha FROM likes WHERE ";
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
		if ($fila = $db->siguienteFila($resultado)){

			// Rellenar las propiedades del objeto con los datos obtenidos de la bdd.
			$this->idLike = $fila['idLike'];
	        $this->idFoto = $fila['idFoto'];
			$this->idUsuario = $fila['idUsuario'];
			$this->fecha = $fila['fecha'];

			return true;
		} else {
			return false;
		}
	}

	function eliminarLike($camposCriterio){

		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectar con la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		$ok = false;
		// Sentencia DELETE para eliminar uno o varios likes de la BDD.
		$i = 1;
		$sql = 'DELETE FROM likes ';
		foreach ($camposCriterio as $campo => $valor) {
            if ($i == 1){
                $sql .= " WHERE ";
            }
            $sql .= $campo . " = '" . $valor . "' ";
            if ($i < count($camposCriterio)){
                $sql .= " AND ";
            }
            $i++;
        }

        if ($db->ejecutarSQL($sql)){
        	$ok = true;
        }

		// Desconexión del servidor de base de datos.
		$db->desconectar();

		return $ok;
	}

}




?>