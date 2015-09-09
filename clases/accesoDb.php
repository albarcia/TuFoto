<?php
//accesoDb.php

	class AccesoDb{
		// Esta clase es espec�fica para trabajar con MySQL.
		// Variable privada para guardar la cadena de conexi�n.
		private $strcon;
		
		function conectar(){
			$host = 'db453227987.db.1and1.com';
			$usuario = 'dbo453227987';
			$contrasena = 'kortatu';
			$baseDatos = 'db453227987';
			
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
			// Esto no se deber�a dejar en una aplicaci�n en producci�n.
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