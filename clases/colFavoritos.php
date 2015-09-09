<?php
//colFavoritos.php

class ColFavoritos{

	private $colFavoritos = Array();
	private $id; // Puede ser el id de usuario o el id de fotografía.
	private $campoCriterio; // indica si se trata de una colección de likes de un usuario o de una fotografía.

	// Constructor. $campoCriterio puede se 'idUsuario' o 'idFoto', lo que indicará si se crea la colección de favoritos
	// de una fotografía o de un usuario.
	function ColFavoritos($campoCriterio, $id){
		
		$this->id = $id;
		$this->campoCriterio = $campoCriterio;

		$this->crearColeccionFavoritos();

	}

	// Getter.
	function getColFavoritos(){
		return $this->colFavoritos;
	}

	function crearColeccionFavoritos(){
		
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		
		// Crear un objeto Db y conectarse a la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		include_once ('favorito.php');
		// Sentencia SELECT para obtener la colección de favoritos de un usuario o de una fotografía.
		$sql = "SELECT * FROM favoritos WHERE " . $this->campoCriterio . ' = ' . $this->id;

		$resultado = $db->ejecutarSQL($sql);
		
		while ($fila = $db->siguienteFila($resultado)){

			$this->colFavoritos[] = new Favorito($fila['idFavorito'], $fila['idFoto'], $fila['idUsuario'], $fila['fecha']);

		}

		// Liberamos recursos.
		$db->liberarRecursos($resultado);
		
		// Desconectamos de la bdd.
		$db->desconectar();

	}

	// Eliminar una colección de favoritos.
	function eliminarColeccionFavoritos(){

		// Crear un objeto Db y conectarse a la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		
		$sql = "DELETE FROM favoritos WHERE " . $this->campoCriterio . " = '" . $this->id . "';";
		
		$db->ejecutarSQL($sql);
		// TODO: Comprobar si la sentencia se ha ejecutado correctamente.
		
		// Desconectamos de la bdd.
		$db->desconectar();
	}

}

?>