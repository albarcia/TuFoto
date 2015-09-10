<?php
//colDescargas.php

class ColDescargas{

	private $colDescargas = Array();
	private $id; // Puede ser el id de usuario o el id de fotografía.
	private $campoCriterio; // indica si se trata de una colección de descargas de un usuario o de una fotografía.

	// Constructor. $campoCriterio puede se 'idUsuario' o 'idFoto', lo que indicará si se crea la colección de descargas
	// de una fotografía o de un usuario.
	function ColDescargas($campoCriterio, $id){
		
		$this->id = $id;
		$this->campoCriterio = $campoCriterio;

		$this->crearColeccionDescargas();

	}

	// Getter.
	function getColDescargas(){
		return $this->colDescargas;
	}

	function crearColeccionDescargas(){
		
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
			
		// Crear un objeto Db y conectarse a la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		include_once ('descarga.php');
		// Sentencia SELECT para obtener la colección de descargas de un usuario o de una fotografía.
		$sql = "SELECT * FROM descargas WHERE " . $this->campoCriterio . ' = ' . $this->id;

		$resultado = $db->ejecutarSQL($sql);
		
		while ($fila = $db->siguienteFila($resultado)){

			$this->colDescargas[] = new Descarga($fila['idDescarga'], $fila['idFoto'], $fila['idUsuario'], $fila['fecha']);

		}

		// Liberamos recursos.
		$db->liberarRecursos($resultado);
		
		// Desconectamos de la bdd.
		$db->desconectar();

	}

	// Eliminar una colección de descargas.
	function eliminarColeccionDescargas(){

		// Crear un objeto Db y conectarse a la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		
		$sql = "DELETE FROM descargas WHERE " . $this->campoCriterio . " = '" . $this->id . "';";
		
		$db->ejecutarSQL($sql);
		// TODO: Comprobar si la sentencia se ha ejecutado correctamente.
		
		// Desconectamos de la bdd.
		$db->desconectar();
	}

}

?>