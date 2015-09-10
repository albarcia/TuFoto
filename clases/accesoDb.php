<?php
//accesoDb.php

	class AccesoDb{
		// Esta clase es específica para trabajar con MySQL.
		// Variable privada para guardar la cadena de conexión.
		private $strcon;
		
		function conectar(){
			$host = '********';
			$usuario = '**********';
			$contrasena = '*********';
			$baseDatos = '*********';
			
			$this->strcon = new mysqli($host, $usuario, $contrasena, $baseDatos) or
				die('Error de aplicaci&oacute;n: La conexi&oacute;n con la base de datos no se ha podido realizar.');
			return $this->strcon;
		}
		
		function desconectar(){
			$this->strcon->close();	
		}
		
		function ejecutarSQL($strSQL){
			$resultado = $this->strcon->query($strSQL);
			//$resultado = mysqli_query($this->strcon, $strSQL);
			// Muestra el detalle del mensaje de error de MySQL.
			// Esto no se debería dejar en una aplicación en producción.
			if (!$resultado){
				$msg = '<br>Consulta inv&aacute;lida: ' . mysql_error() . '<br>';
				$msg .= 'SQL: ' . $strSQL;
				die($msg);	
			}
			
			return $resultado;
		}
		
		function siguienteFila($rst){
			return $rst->fetch_assoc();	
		}
		
		function cantidadFilas($rst){
			return $rst->num_rows;	
		}
		
		function liberarRecursos($rst){
			$rst->free;	
		}
		
	}
?>
