<?php

$rutaRaiz = "/homepages/12/d416622347/htdocs/fotografia/";

include_once($rutaRaiz . "clases/accesoDb.php");
include_once($rutaRaiz . "clases/fotografia.php");
include_once($rutaRaiz . "clases/usuario.php");
include_once($rutaRaiz . "clases/like.php");
include_once($rutaRaiz . "clases/favorito.php");
include_once($rutaRaiz . "clases/comentario.php");
include_once($rutaRaiz . "clases/descarga.php");
include_once($rutaRaiz . "clases/colFotografia.php");
include_once($rutaRaiz . "clases/colLikes.php");
include_once($rutaRaiz . "clases/colFavoritos.php");
include_once($rutaRaiz . "clases/colComentario.php");

function __autoload($objeto){
    include($rutaRaiz . "clases/". $objeto . ".php");
}
session_start();
$_SESSION['rutaRaiz'] = $rutaRaiz;
unset($_SESSION['claveBusqueda']);
unset($_SESSION['valorBusqueda']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>tuFoto</title>
    <meta charset="UTF-8">
    <base href="/fotografia/" target="_self">
    <link rel="shortcut icon" href="recursos/favicon.ico">
    <link type="text/css" rel="stylesheet" href="estilos/estilo01.css" media="screen">

    <script type="text/javascript" src="scripts/jquery/jquery-1.9.1-min.js"></script>
    <script type="text/javascript" src="scripts/jquery/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
    <script type="text/javascript" src="scripts/jquery/jquery-ui/jquery.ui.datepicker-es.js"></script>
    <script type="text/javascript" src="scripts/jquery/jquery-ui/jquery.ui.progressbar.js"></script>
    <script type="text/javascript" src="scripts/jquery/jquery-ui/jquery.ui.widget.js"></script>
    <link type="text/css" rel="stylesheet" href="scripts/jquery/jquery-ui/css/jquery.ui.all.css" media="screen">

    <script type="text/javascript" src="scripts/jscrollpane/jscrollpane.js"></script>
    <script type="text/javascript" src="scripts/mousewheel.js"></script>
    <link type="text/css" rel="stylesheet" href="scripts/jscrollpane/jscrollpane.css" media="screen">
    <link type="text/css" rel="stylesheet" href="estilos/jscrollpane-custom.css" media="screen">

    <!--<script type="text/javascript" src="scripts/flexcroll/flexcroll.js"></script>-->
    
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
    <div id="cabecera">
        <div id="logo">
            <a href="index.php"><img src="recursos/LogoTuFoto.png" alt="Logo tuFoto"/></a>
        </div>
        <?php
            include($rutaRaiz . 'includes/menuFotos.php');
            include($rutaRaiz . 'includes/login.php');
            include('cajaLogin.php');
        ?>
        <div style="clear: both;"></div>
    </div>
    <div id="contenedor_externo">