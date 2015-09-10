<?php
//colLikes.php

class ColLikes{

	private $colLikes = Array();
	private $id; // Puede ser el id de usuario o el id de fotografía.
	private $campoCriterio; // indica si se trata de una colección de likes de un usuario o de una fotografía.

	// Constructor. $campoCriterio puede se 'idUsuario' o 'idFoto', lo que indicará si se crea la colección de likes
	// de una fotografía o de un usuario.
	function ColLikes($campoCriterio, $id){
		
		$this->id = $id;
		$this->campoCriterio = $campoCriterio;

		$this->crearColeccionLikes();

	}

	// Getter.
	function getColLikes(){
		return $this->colLikes;
	}

	function crearColeccionLikes(){
		
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		
		// Crear un objeto Db y conectarse a la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		include_once ('like.php');
		// Sentencia SELECT para obtener la colección de likes de un usuario o de una fotografía.
		$sql = "SELECT * FROM likes WHERE " . $this->campoCriterio . ' = ' . $this->id;

		$resultado = $db->ejecutarSQL($sql);
		
		while ($fila = $db->siguienteFila($resultado)){

			$this->colLikes[] = new Like($fila['idLike'], $fila['idFoto'], $fila['idUsuario'], $fila['fecha']);

		}

		// Liberamos recursos.
		$db->liberarRecursos($resultado);
		
		// Desconectamos de la bdd.
		$db->desconectar();

	}

	// Eliminar una colección de likes.
	function eliminarColeccionLikes(){

		// Crear un objeto Db y conectarse a la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		
		$sql = "DELETE FROM likes WHERE " . $this->campoCriterio . " = '" . $this->id . "';";
		
		$db->ejecutarSQL($sql);
		// TODO: Comprobar si la sentencia se ha ejecutado correctamente.
		
		// Desconectamos de la bdd.
		$db->desconectar();
	}

}

?>