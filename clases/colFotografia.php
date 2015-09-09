<?php
// colFotografia.php

// Clase que almacenar� la colecci�n de fotograf�as perteneciente a un usuario en concreto.
class ColFotografia{
	
	// Array en el que se alamcanar� la colecci�n de fotograf�as.
	private $colFotos = Array();
	private $idUsuario;
	
	// Constructor. El par�metro $camposCriterio se utiliza del siguiente modo:
	//		- Si est� vac�o se recuperan todas las fotograf�as de la base de datos.
	//		- Si no est� vac�o contendr� un id de usuario y recuperar� s�lo las fotograf�as de ese usuario.
	function colFotografia($camposCriterio, $criterioOrdenacion = '', $comienzo = 0, $limite = ''){

		if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		include_once ('fotografia.php');
		
		// Crear un objeto Db y conectarse a la base de datos.
		$db = new AccesoDb();
		$db->conectar();
		
		$i = 1;
		// Sentencia SELECT para obtener la colecci�n de fotograf�as de un usuario.
		$sql = "SELECT * FROM fotografia";

		if (!empty($camposCriterio)){
			foreach ($camposCriterio as $campo => $valor) {
	            if ($i == 1){
	                $sql .= " WHERE ";
	            }
	            $sql .= $campo; 
	            // Si se quiere una colecci�n en base a una etiqueta el operador de comparaci�n ser� LIKE en vez de =.
	            if ($campo != 'etiquetas'){
	            	$sql .= " = '";
	            	$sql .= utf8_decode($valor) . "' ";
	            } else {
	            	$sql .= " LIKE '";
	            	$sql .= "%" . $valor . "%' ";
	            }

	            if ($i < count($camposCriterio)){
	                $sql .= " AND ";
	            }
	            $i++;
	        }
    	}
    	if (!empty($criterioOrdenacion)){
    		$sql .= ' ORDER BY ' . $criterioOrdenacion;
    	}

    	$sql .= ' DESC';

    	if (!empty($comienzo) || !empty($limite)){
    		$sql .= " LIMIT " . $comienzo . ", " . $limite;
    	}

		$resultado = $db->ejecutarSQL($sql);
		
		while ($fila = $db->siguienteFila($resultado)){

			$this->colFotos[] = new Fotografia($fila['idFoto'], $fila['idUsuario'], $fila['nombreUsuario'], $fila['nombreFichero'], utf8_encode($fila['titulo']), utf8_encode($fila['descripcion']), 
							utf8_encode($fila['categoria']), $fila['fechaSubida'], $fila['fechaTomada'], $fila['latitud'], $fila['longitud'], utf8_encode($fila['camara']), 
							$fila['lente'], $fila['distanciaFocal'], $fila['velocidad'], $fila['apertura'], $fila['iso'], utf8_encode($fila['etiquetas']), 
							$fila['likes'], $fila['favoritos'], $fila['visitas'], $fila['puntuacion'], $fila['descarga'], $fila['descargas']);
		}

		// Liberamos recursos.
		$db->liberarRecursos($resultado);
		
		// Desconectamos de la bdd.
		$db->desconectar();
		
	}

	// Getter
	function getColFotos(){
		return $this->colFotos;
	}
	
	function eliminarColeccionFotografias(){
		// Borramos cada una de las fotograf�as de la colecci�n.
		foreach($this->colFotos as $foto){
			$foto->eliminarFoto();
		}
	}

	function cantidadFotografias(){
		return count($this->colFotos);
	}
	
}


?>