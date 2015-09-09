<?php
// colComentario.php

// Clase que almacenará la colección de comentarios referentes a una fotografía o
// escritos por un usuario.
class ColComentario{
	
	private $colComentarios = Array(); // Array en el que se alamacenará la colección de comentarios.
	private $id; // Puede ser el id de usuario o el id de fotografía.
	private $campoCriterio; // Indica si se trata de la colección de comentarios de una fotografía o de un usuario.
	
	// Constructor.
	function ColComentario($campoCriterio, $id){

		$this->campoCriterio = $campoCriterio;
		$this->idFoto = $id;
		// Desde el constructor se crea automáticamante la colección de comentarios.
		$this->crearColeccionComentarios();
		
	}

	//Getter.
	function getColComentarios(){
		return $this->colComentarios;
	}
	
	function crearColeccionComentarios(){
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectarse a la base de datos.
		$db = new AccesoDb();
		$db->conectar();

		// Sentencia SELECT para obtener la colección de comentarios referentes a la fotografía.
		$sql = "SELECT * FROM comentario WHERE " . $this->campoCriterio . " = '" . $this->idFoto . "' ORDER BY fecha DESC;";

		$resultado = $db->ejecutarSQL($sql);

		require_once('comentario.php');
		while ($fila = $db->siguienteFila($resultado)){
			$this->colComentarios[] = new Comentario($fila['idFoto'], $fila['idUsuario'], $fila['fecha'], utf8_encode($fila['texto']));
		}

		// Liberamos recursos.
		$db->liberarRecursos($resultado);
		
		// Desconectamos de la bdd.
		$db->desconectar();

	}
	
	function eliminarColeccionComentarios(){
		
		// Crear un objeto Db y conectarse a la base de datos.
		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		
		$sql = "DELETE FROM comentario WHERE " . $this->campoCriterio . " = '" . $this->idFoto . "';";
		
		$db->ejecutarSQL($sql);
		// TODO: Comprobar si la sentencia se ha ejecutado correctamente.
		
		// Desconectamos de la bdd.
		$db->desconectar();
	}

}

?>