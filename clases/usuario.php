<?php
// usuario.php

class Usuario{
    
    private $idUsuario;
    private $nombre;
    private $apellidos;
    private $nombreUsuario;
    private $contrasena;
    private $email;
    private $url;
    private $acerca;
    private $fechaAlta;
    private $fechaUltimoAcceso;
    private $foto;
    private $usuarioActivo;
    
    // Constructor.
    function Usuario($idUsuario=0, $nombre="", $apellidos="", $nombreUsuario="", $contrasena="", $email="", $url="", $acerca="", $foto="", $usuarioActivo=true){
        
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->nombreUsuario = $nombreUsuario;
        $this->contrasena = $contrasena;
        $this->email = $email;
        $this->url = $url;
        $this->acerca = $acerca;
        $this->foto = $foto;
        $this->usuarioActivo = $usuarioActivo;
    }
    
    // Getters.
    function getIdUsuario(){return $this->idUsuario;}
    function getNombre(){return $this->nombre;}
    function getApellidos(){return $this->apellidos;}
    function getNombreUsuario(){return $this->nombreUsuario;}
    function getContrasena(){return $this->contrasena;}
    function getEmail(){return $this->email;}
    function getUrl(){return $this->url;}
    function getAcerca(){return $this->acerca;}
    function getFechaAlta(){return $this->fechaAlta;}
    function getFoto(){return $this->foto;}
    function getUsuarioActivo(){return $this->usuarioActivo;}

    function identificarUsuario($motivo){

        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
        // Crear un objeto db y conectar con la base de datos.
        $db = new AccesoDb();
        $db->conectar();

        $existe = false;
        switch ($motivo) {
            case 'insertar': // Estamos creando un nuevo usuario. Se comprueba que el nombre de usuario
                             // no se corresponda con el de ningún usuario que ya esté dado de alta.

                $camposCriterio = array('nombreUsuario' => utf8_decode($this->nombreUsuario));
                $this->leerUsuario($camposCriterio);

                $cantidadFilas = $db->cantidadFilas($resultado);
                if ($cantidadFilas > 0){
                    $existe = true;
                }
                break;
            
            case 'login': // Un usuario hace login. Se comprueban su nombre de usuario y su contraseña.

                $camposCriterio = array('nombreUsuario' => utf8_decode($this->nombreUsuario), 'contrasena' => hash("SHA256", $this->contrasena));
                $this->leerUsuario($camposCriterio);

                if ($this->idUsuario != 0){   
                    $existe = true;
                }
                break;
        }

        return $existe;
    }

    function insertarUsuario(){

        $ok = false;
        $motivo = "insertar";
    	
        // Crear un objeto db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
        $db = new AccesoDb();
        $db->conectar();
        
        // Sentencia INSERT para insertar un usuario.
        $sql = "INSERT INTO usuario (nombre, apellidos, nombreUsuario, contrasena, email, url, acerca, ";
        $sql .= "foto) VALUES ('";
        $sql .= utf8_decode($this->nombre) . "', '";
        $sql .= utf8_decode($this->apellidos) . "', '";
        $sql .= utf8_decode($this->nombreUsuario) . "', '";
        $sql .= hash("SHA256", $this->contrasena) . "', '";
        $sql .= utf8_decode($this->email) . "', '";
        $sql .= utf8_decode($this->url) . "', '";
        $sql .= utf8_decode($this->acerca) . "', '";
        $sql .= utf8_decode($this->foto) . "');";
         
        // Ejecución de la sentencia SQL.
        if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
        
        // Desconexión con el servidor de base de datos.
        $db->desconectar();

        return $ok;
    }
    
    function leerUsuario($camposCriterio){
        
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectar con la base de datos.
		$db = new AccesoDb();
		$db->conectar();
        
		// Sentencia SELECT para leer un usuario determinado.
        $i = 1;
		$sql = "SELECT idUsuario, nombre, apellidos, nombreUsuario, contrasena, email, url, acerca, foto, usuarioActivo FROM usuario";
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

		$resultado = $db->ejecutarSQL($sql);
        $ok = false;
    	// Obtener la fila de datos.
    	if ($fila = $db->siguienteFila($resultado)){
            $ok = true;
    		// Rellenar las propiedades del objeto con los datos obtenidos de la bdd.
    		$this->idUsuario = $fila['idUsuario'];
    		$this->nombre = utf8_encode($fila['nombre']);
            $this->apellidos = utf8_encode($fila['apellidos']);
            $this->nombreUsuario = utf8_encode($fila['nombreUsuario']);
            $this->contrasena = $fila['contrasena'];
            $this->email = utf8_encode($fila['email']);
            $this->url = utf8_encode($fila['url']);
            $this->acerca = utf8_encode($fila['acerca']);
            $this->foto = utf8_encode($fila['foto']);
            $this->usuarioActivo = $fila['usuarioActivo'];
        }

        // Desconexión del servidor de base de datos.
        $db->desconectar();

        return $ok;
    }
    
	function modificarUsuario($camposValores){

		// Crear un objeto Db y conectar con la base de datos.
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		$db = new AccesoDb();
		$db->conectar();
		
		$i = 1;
		// Sentencia UPDATE para modificar la bdd.
		$sql = "UPDATE usuario SET ";
		foreach ($camposValores as $campo => $valor){
            if ($campo == "contrasena"){
                $sql .= $campo . " = '" . hash("SHA256", $valor) . "'";
            } else {
			    $sql .= $campo . " = '" . utf8_decode($valor) . "'";
            }
			if ($i < count($camposValores)) {
				$sql .= ", ";
			}
			$i++;
		}
		$sql .= " WHERE idUsuario = '" . $this->idUsuario . "';";
		
        $ok = false;
		// Ejecución de la sentencia SQL.
		if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
		
		// Desconexión del servidor de base de datos.
		$db->desconectar();
    	
        return $ok;	
    }
    
    function eliminarUsuario(){

        //$camposCriterio = array('idUsuario' => $this->idUsuario);
        //$this->leerUsuario($camposCriterio);
        if (!class_exists('AccesoDb')){
            include_once ("../clases/accesoDb.php");
        }
		// Crear un objeto Db y conectar con la base de datos.
		$db = new AccesoDb();
		$db->conectar();
	  
		// Creamos una colección de todas sus fotografías y la eliminamos.
        include_once ("colFotografia.php");
        $camposCriterio = array('idUsuario' => $this->idUsuario);
		$colFotografias = new ColFotografia($camposCriterio, 'fechaSubida');
		$colFotografias->eliminarColeccionFotografias();

        // Creamos una colección de todos sus likes y la eliminamos.
        include_once('colLikes.php');
        $colLikes = new ColLikes('idUsuario', $this->idUsuario);
        $colLikes->eliminarColeccionLikes();

        // Creamos una colección de todos sus favoritos y la eliminamos.
        include_once('colFavoritos.php');
        $colFavoritos = new ColFavoritos('idUsuario', $this->idUsuario);
        $colFavoritos->eliminarColeccionFavoritos();

        // Creamos una colección de todas sus descargas y la eliminamos.
        include_once('colDescargas.php');
        $colDescargas = new ColDescargas('idUsuario', $this->idUsuario);
        $colDescargas->eliminarColeccionDescargas();

        // Creamos una colección de todos sus comentarios y la eliminamos.
        //include_once('colComentario.php');
        //$colFavoritos = new colComentario('idUsuario', $this->idUsuario);
        //$colFavoritos->eliminarColeccionComentarios();

        // Borrar foto de perfil en el caso de que la haya.
        if ($this->foto != 'defecto' || $this->foto != ''){
            unlink("../imagenes/usuarios/" . $this->nombreUsuario . "/perfil/" . $this->foto);
            unlink("../imagenes/usuarios/" . $this->nombreUsuario . "/perfil/" . $this->nombreUsuario . "Mini.jpg");
        }

        // Borrar carpetas de usuario.
        rmdir("../imagenes/usuarios/" . $this->nombreUsuario . "/muestra");
        rmdir("../imagenes/usuarios/" . $this->nombreUsuario . "/miniaturas");
        rmdir("../imagenes/usuarios/" . $this->nombreUsuario . "/originales");
        rmdir("../imagenes/usuarios/" . $this->nombreUsuario . "/perfil");
        rmdir("../imagenes/usuarios/" . $this->nombreUsuario);

		// Sentencia DELETE para eliminar al usuario.
		$sql = "DELETE FROM usuario WHERE idUsuario = " . $this->idUsuario . ";";
	  
        $ok = false;
		// Ejecución de la sentencia SQL que elimina la foto de la bdd.
		if ($db->ejecutarSQL($sql)){
            $ok = true;
        }
	  
		// Desconexión del servidor de base de datos.
		$db->desconectar();

        return $ok;
	}
}
?>