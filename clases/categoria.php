<?php
// categoria.php

class Categoria{
    
    private $idCategoria;
    private $value;
    private $texto;
    
    // Constructor.
    function Usuario($value="", $texto=""){
        
        $this->value = $value;
        $this->texto = $texto;
    }
    
    // Getters.
    function getIdCategoria(){return $this->idCategoria;}
    function getValue(){return $this->value;}
    function getTexto(){return $this->texto;}

    function insertarCategoria(){
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
        // Crear un objeto db y conectar con la base de datos.
        $db = new AccesoDb();
        $db->conectar();
        
        // Sentencia INSERT para insertar una categora.
        $sql = "INSERT INTO categorias (value, texto) VALUES ('";
        $sql .= mysql_escape_string($this->value) . "', '";
        $sql .= mysql_escape_string($this->texto) . "')";
        
        // Ejecucin de la sentencia SQL.
        $db->ejecutarSQL($sql);
        // TODO: Comprobar que la sentencia se ha ejecutado correctamente.
        
        // Desconexin con el servidor de base de datos.
        $db->desconectar();
    }
    
    function leerListaCategorias(){
    	
    	// Crear un objeto db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
    	$db = new AccesoDb();
    	$db->conectar();
    	
    	// Sentencia SELECT para leer la coleccin de categoras.
    	$sql = "SELECT value, texto FROM categorias ORDER BY texto ASC;";
    	
    	$resultado = $db->ejecutarSQL($sql);
    	
    	$listaCategorias = array();
    	while ($categoria = $db->siguienteFila($resultado)){
    		$listaCategorias[$categoria['value']] = utf8_encode($categoria['texto']);
    	}

    	// Liberamos recursos.
    	$db->liberarRecursos($resultado);
    	
    	// Desconectamos de la bdd.
    	$db->desconectar();
    	
    	return $listaCategorias;
    }
    
}
?>