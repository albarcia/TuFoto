<?php
// mosaico.php

function cargaMosaico($criterioOrdenacion, $claveBusqueda='', $valorBusqueda=''){
  include_once($_SESSION['rutaRaiz'] . 'clases/colFotografia.php');
  // Contamos la cantidad de fotografías que tenemos en la base de datos para hacer la paginación.
  if (!empty($claveBusqueda) && !empty($valorBusqueda)){
      $camposCriterio = array($claveBusqueda => $valorBusqueda);
      $opacidadMensajeCabecera = '1';
      switch($claveBusqueda){
        case 'idUsuario':
          $mensajeCabecera = 'Colecci&oacute;n de fotograf&iacute;as de <span>' . $_SESSION['nombreUsuario'] . '</span>';
          break;
        case 'nombreUsuario':
          $mensajeCabecera = 'Colecci&oacute;n de fotograf&iacute;as de <span>' . $valorBusqueda . '</span>';
          break;
        case'etiquetas':
          $mensajeCabecera = 'Colecci&oacute;n de fotograf&iacute;as con la etiqueta <span>' . $valorBusqueda . '</span>';
          break;
      }
  } else {
    $camposCriterio = array();
    $mensajeCabecera = '';
  }
    $colFotografias = new ColFotografia($camposCriterio, $criterioOrdenacion);
    $totalFotos = count($colFotografias->getColfotos());
    $totalPaginas = ceil($totalFotos / 16);

    /*if ($totalfotos < 1){
      $respuesta = 'no Fotos';
    } else {*/
  
      $respuesta = '<div id="mensajeCabecera" style="display: inline; opacity: ' . $opacidadMensajeCabecera . ';">' . $mensajeCabecera . '</div>
      <div id="menuSuperiorMosaico">
        <div id="botonesMosaico">
          <div id="recientes">
            <input type="button" id="fechaSubida" class="mosaicoBtn" value="M&aacute;s recientes"/>
          </div>
          <div id="populares">
            <input type="button" id="puntuacion" class="mosaicoBtn" value="Mejor valoradas"/>
          </div>
          <div id="visitadas">
            <input type="button" id="visitas" class="mosaicoBtn" value="M&aacute;s visitadas"/>
          </div>
          <div style="clear: both;"></div>
        </div>
        
      </div>
      <div id="contenedor">
        <!-- Aquí se muestra el mosaico de fotografías -->
      </div>
      <div id="contenedorPaginacion">
        <div id="paginas">
          <!--<div style="float: left;">P&aacute;ginas: </div>-->';
          $respuesta .= '<div id="paginasInterior" style="width:' . ($totalPaginas + 1) * 20 . 'px;">';
            for ($i = 0; $i < $totalPaginas; $i ++){
              $pagina = $i+1;
              $respuesta .= '<div class="numeroPagina" id="'. $pagina . '">' . $pagina . '</div>';
            }
            $respuesta .= '<div class="blanco"></div>
          </div>
        </div>
        <div id="tituloPaginacion">P&aacute;ginas: </div>
      </div>
      <div class="blanco"></div>
        <script type="text/javascript">
          $(document).ready(function(){
            // Cargamos el mosaico con las primeras 16 fotos ordenadas por fecha de subida.
              cargaPrincipal("fechaSubida", 16, 1, "' . $claveBusqueda . '", "' . $valorBusqueda . '");
        }); 
        </script>';
 // }
    return $respuesta;
} // Llave de cierre de la función cargaMosaico().
 ?>