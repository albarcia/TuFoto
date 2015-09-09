// scripts.js
var numeroImagenesPorPagina = 16;
$(document).ready(inicio);

function inicio(){

  // Ocultamos y mostramos el botón de enviar comentario según haya, o no haya, contenido en el textfield.
  $('#nuevoComentario').keyup(function(){
    if ($(this).val() != ''){
      $('#enviarComentario').css('opacity', '1');
    } else {
      $('#enviarComentario').css('opacity', '0');
    }
  });

  // Gestión de la animación de login
  $('#login').click(function(){
    quitarTabindex('#cajaLogin', '#idCajaModal');
    if ($(window).height() < $('#contenedor_externo').height()){
      var top = ($(window).height() - 200) / 2;
    } else {
      var top = ($('#contenedor_externo').height() - 200) / 2;
    }
    var left = ($(window).width() - 400) / 2;
    $('#cajaLogin').css({'position': 'absolute',
                  'top': top + 'px',
                  'left': left + 'px',
                  'display': 'block',
                  'opacity': '1',
                  'z-index': '6',
                  '-webkit-box-shadow': '0px 0px 30px #FFF',
                  '-moz-box-shadow': '0px 0px 30px #FFF',
                  '-o-box-shadow': '0px 0px 30px #FFF',
                  'box-shadow': '0px 0px 30px #FFF'
                }).addClass('rotacion');
    $('#contenedor_externo').css({'-webkit-filter': 'blur(3px) grayscale(1)',
                                  'filter': 'blur(3px) grayscale(1)'
                                });
    $('#fondoModal').removeClass('fondoModalOff').addClass('fondoModalOn').css('height', $('body').height());
    $('#loginNombreUsuario').focus();
  });

  // Validación del formulario de login
  $('.botonCajaModal').click(function(){
    restaurarTabindex();
    if ($(this).attr('id') == 'loginEntrar'){
      validarDatosLogin();
    } else {
      cancelarLogin();
    }
  });

  // Si se cierra sesión desde el menú desplegable.
  $('#menuUsuarioLogout').click(logout);

  // Al hacer click sobre la caja que indica qué usuario está logeado, se muestran su colección de 
  // fotografías.
  $('#cajaLogeado').click(function(){
    var idUsuario = $('#idUsuarioLogeado').val();
    cargaMosaico('fechaSubida', numeroImagenesPorPagina, 1, 'idUsuario', idUsuario);
  });

  $('#menuFotos li').click(function(){
    var id = $(this).attr('id');
    cargaMosaico(id, numeroImagenesPorPagina, 1, '', '');
    setTimeout(function(){botones(id);}, 50);
    
  });

  // Nombre de usuario cuando se muestra la fotografía.
  $('.nombreUsuarioDatosFoto').click(function(){
    var nombreUsuario = $(this).attr('id');
    cargaMosaico('fechaSubida', numeroImagenesPorPagina, 1, 'nombreUsuario', nombreUsuario);
  });

  // Nombre de usuario que se muestra en los comentarios.
  $('.nombreUsuarioComentario').click(function(){
    var nombreUsuario = $(this).attr('id');
    cargaMosaico('fechaSubida', numeroImagenesPorPagina, 1, 'nombreUsuario', nombreUsuario);
  });

  // Al hacer click sobre una etiqueta se muestra la colección de fotografías etiquetadas con 
  // esa misma etiqueta.
  $('.etiqueta').click(function(){
    var etiqueta = $(this).attr('id');
    cargaMosaico('fechaSubida', numeroImagenesPorPagina, 1, 'etiquetas', etiqueta);
  });

  // Si se pulsa el botón de registro de usuario nuevo...
  $('#registro').click(function(){
    location.href="procesos/crearUsuario.php";
  });


  // Gestión de los botones 'Me gusta' y 'Favorito'.
  $('.likeFavoritoBtnEnabled').click(function(){
    var id = $(this).attr('id');
    var idUsuario = $('#idUsuario').attr('value');
    sumaMeGustaFavorito(id, idUsuario);
  });

  // Controlamos la pulsación con el botón secundario del ratón sobre la fotografía para que el usuario
  // no la pueda descargar.
  $('.contenedorDatosTotal img').bind("contextmenu",function(e){
    e.preventDefault();
    quitarTabindex('#idCajaModal', '#cajaLogin');
    mostrarCajaModal('copyright','¡Atención!', 'Esta imagen está protegida por copyright', false);
    });

  $('.eliminarImagen').click(function(){
    var id = $(this).attr('id');
    $('#valorCajaModal').val(id);
    mostrarCajaModal('eliminarFoto','¡Atención!', '¿Seguro que quieres borrar esta fotografía?', true);
  });

  $('#eliminarUsuarioBtn').click(function(){
    var id = $('#idUsuario').val();
    $('#valorCajaModal').val(id);
    mostrarCajaModal('eliminarUsuario','¡Atención!', '¿Seguro que quieres borrar este usuario?', true);
  });

  // El propietario de una foto autoriza o deniega su descarga.
  $('.permisoDescargaFotoBtn').click(function(){
    var idFoto = $('.marcoFoto > img').attr('id');
    var permiso = false;
    if ($(this).attr('id') == 'permitirDescargaFotoBtn'){
      permiso = true;
    }
    var datos = 'idFoto=' + idFoto + '&permiso=' + permiso + '&selector=permisoDescargaFoto';
        $.ajax({
          type: 'POST',
          url:'procesos/respuestaAjax.php',
          data: datos,
          success: function(respuesta){
            if (respuesta){
              if (permiso){
                $('.permisoDescargaFotoBtn').val('Denegar descarga fotografía').attr('id', 'denegarDescargaFotoBtn');
              } else {
                $('.permisoDescargaFotoBtn').val('Autorizar descarga fotografía').attr('id', 'permitirDescargaFotoBtn');
              }
            }
          }
        });
  });

  // Si la descarga de una foto está autorizada por su propietario, cualquier usuario registrado puede descargarla.
  $('#descargarFotoBtn').click(function(){
    var idFoto = $('.marcoFoto img').attr('id');
    var nombreUsuarioPropietarioFoto = $('.nombreUsuarioDatosFoto').attr('id');
    var idUsuario = $('#idUsuario').attr('value'); // Se toma del input hidden de los botones like y favorito.
    var datos = 'idFoto=' + idFoto + '&nombreUsuarioPropietarioFoto=' + nombreUsuarioPropietarioFoto + '&idUsuario=' + idUsuario + '&selector=descargarFoto';
        $.ajax({
          type: 'POST',
          url:'procesos/respuestaAjax.php',
          data: datos,
          success: function(respuesta){
            var r = JSON.parse(respuesta);
            location.href="procesos/descargarFoto.php?fichero=" + r.fichero + "&usuario=" + r.usuario;
          } 
        });
  });

  // Animación de entrada de los datos de la foto.
  $('#externoDatosFoto').css({'left': '0px',
                              'opacity': '1'
                            });

  // Animación de entrada del título de la página (en el caso de que lo tenga).
  $('.contenedorDatosTotal h1').css({'left': '0px',
                                    'opacity': '1'
                                  });

  // Al entrar en la pantalla de datos de usuario guardamos el email en una variable.
  // Al salir de editar el campo email comprobamos si se ha modificado. Si es así, mantenemos
  // a la vista el campo para repetir el email. Si no es así ocultamos este último.
  var email = $('#datoDatoEmail').val();
  $('#datoDatoEmail').blur(function(){
    if (email == $(this).val()){
      $('#datosUsuarioSegundoEmail').css({'height': '0px',
                                          'margin-bottom': '0px'
                                          });
    } else {
      $('#datoDatoEmail2').focus().select();
    }
  })

  // Solicitar que se repita el email al editarlo en los datos de usuario.
  $('#datoDatoEmail').keyup(function(){
    $('#datosUsuarioSegundoEmail').css({'height': '75px',
                                        'margin-bottom': '-15px'
                                        });
  });

  // Control barras scroll
  // jScrollPane
  controlBarrasScroll();
  
  //fleXenv.fleXcrollMain('contenedorComentarios');

  // Calendario para introducir la fecha en que la fotografía fue tomada.
  var fechaHoy = new Date();
  $('#fechaTomada').datepicker({maxDate: fechaHoy});
  $('#fechaTomada').datepicker('option', 'showAnim', 'slideDown');

  // Control de la pulsación de teclas en los campos del cuadro de login.
  $('.camposLogin').keyup(function(){
    if ($(this).val() != ''){
      $('#cajaLogin').css({'webkit-box-shadow': '0px 0px 30px #FFF',
                          '-moz-box-shadow': '0px 0px 30px #FFF',
                          '-o-box-shadow': '0px 0px 30px #FFF',
                          'box-shadow': '0px 0px 30px #FFF',
                          'border-color': '#BBB'
                      });
      return false;
    }
  });

  // Subiendo archivo de fotografía nueva. Si tenemos en pantalla un mensaje informándonos de 
  // un error anterior, al hacer click en el botón para seleccionar una foto nueva, ese mensaje
  // desaparece.
  $("#nuevaFotoUsuarioNuevo").click(function(){
        $('#mensajeErrorFotoNueva').css('opacity', '0');
    });

  // En la pantalla de condulta/modificación de datos de usuario, al modificar cualquiera de los campos
  // o al seleccionar una foto nueva, mostramos el botón para guardar los cambios.
  $('#nuevaFotoUsuario').change(function(){
    $('#modificarUsuarioBtn').css({'display': 'block',
                                    'opacity': '1'
                                  });
  });

  $('#fotoDatosUsuario').css('opacity', '1');
  setTimeout(function(){$('#datosDatosUsuario').css('opacity', '1');}, 200);
  setTimeout(function(){$('#eliminarUsuarioBtn').css('opacity', '1');},400);

  // Pulsamos el botón de cambiar contraseña en la pantalla de visualización/modificación
  // de datos de usuario.
  $('#cambiarContrasenaBtn').click(function(){
    $(this).css('opacity', '0');
    $('#inputsCambiarContrasena').css({'opacity': '1',
                                        'z-index': '3'
                                      });
    $('#datoDatoContrasena').focus();
  });

  // Click en el botón de modificar datos de usuario.
  $('#modificarUsuarioBtn').click(modificarUsuario);

  // En la pantalla de visualización/modificación de datos de usuario, al pulsar una tecla
  // se vuelven los bordes al estado 'natural'. Se hace siempre pero, realmente, sólo es
  // necesario cuando se ha señalado un error en los datos con un borde rojo. -> Optimización.
  $('.inputDatoDato').keyup(function(){
    $(this).css({'border-top': '1px solid #333',
                  'border-right': '1px solid #444',
                  'border-bottom': '1px solid #444',
                  'border-left': '1px solid #333'
                });
    $('#modificarUsuarioBtn').css({'display': 'block',
                                    'opacity': '1'
                                  });
  });

} // Cierre de la función inicio.

// Asignamos las barras de scroll personalizadaas a los contenedores que las necesitan y 
// controlamos cuando el puntero se encuentra sobre ellos para mostrarlas o no.
function controlBarrasScroll(){
  $('#contenedorComentarios').jScrollPane();
  $('#descripcionDatosFotoExterno').jScrollPane();
  $('#tiraImagenesExterior').jScrollPane({'showArrows': true});
  $('.jspScrollable').mouseenter(function(){
    switch ($(this).attr('id')){
      case 'contenedorComentarios':
        if (parseInt($(this).css('height')) > 599){
          $(this).find('.jspDrag').stop(true, true).css('opacity', '1');
        }
        break;
      case 'descripcionDatosFotoExterno':
        if (parseInt($(this).css('height')) > 149){
          $(this).find('.jspDrag').stop(true, true).css('opacity', '1');
        }
        break;
      case 'tiraImagenesExterior':
        if (parseInt($('#tiraImagenesInterior').css('width')) > 300){
          $(this).find('.jspDrag').stop(true, true).css('opacity', '1');
          $(this).find('.jspCap').stop(true, true).css('opacity', '1');
          $(this).find('.jspArrow').stop(true, true).css('opacity', '1');
        }
        break;
    }
  });
  $('.jspScrollable').mouseleave(function(){
      $(this).find('.jspDrag').stop(true, true).css('opacity', '0');
      $(this).find('.jspCap').stop(true, true).css('opacity', '0');
          $(this).find('.jspArrow').stop(true, true).css('opacity', '0');
  });
}

// Al pulsar un botón en el mosaico, ponemos el fondo de todos en negro y el fondo
// del botón pulsado en el color que lo identifica como activo.
function botones(id){
    $('.mosaicoBtn').css('backgroundColor', 'rgba(0,0,0,0)');
    $('#botonesMosaico #' + id).css('backgroundColor', 'rgba(100,0,0,0.6)');
  }

// Muestra y gestiona la caja modal con los parámetros especificados.
function mostrarCajaModal(motivo, titulo, texto, cancelarBtn){
  $('#idTituloCajaModal').text(titulo);
  $('#textoCajaModal').text(texto);
  if (cancelarBtn){
    $('#cancelarBtnCajaModal').css('display', 'inline');
  } else {
    $('#cancelarBtnCajaModal').css('display', 'none');
  }
  if ($(window).height() < $('#contenedor_externo').height()){
    var top = ($(window).height() - 200) / 2;
  } else {
    var top = ($('#contenedor_externo').height() - 200) / 2;
  }
  var left = ($(window).width() - 400) / 2;
  $('#idCajaModal').css({'position': 'absolute',
                  'display': 'block',
                  'top': top + 'px',
                  'left': left + 'px',
                  'opacity': '1',
                  'z-index': '6',
                  '-webkit-box-shadow': '0px 0px 30px #FFF'
                }).addClass('rotacion');
    $('#contenedor_externo').css({'-webkit-filter': 'blur(3px) grayscale(1)',
                                  'filter': 'blur(3px) grayscale(1)'
                                });
    $('#fondoModal').removeClass('fondoModalOff').addClass('fondoModalOn');

  // Botón 'Aceptar' de la caja modal. Al pulsarlo cierra la caja y devuelve true.
  $('#aceptarBtnCajaModal').click(function(){
    cerrarCajaModal();
    restaurarTabindex();
    var id = $('#valorCajaModal').val();
    switch(motivo){
      case 'eliminarFoto':
        var datos = 'idFoto=' + id + '&selector=eliminarFoto';
        $.ajax({
          type: 'POST',
          url:'procesos/respuestaAjax.php',
          data: datos,
          success: llegadaEliminarFoto
        });
        break;
      case 'eliminarUsuario':
        var datos = 'idUsuario=' + id + '&selector=eliminarUsuario';
        $.ajax({
          type: 'POST',
          url:'procesos/respuestaAjax.php',
          data: datos,
          success: llegadaEliminarUsuario
        });
        break;
      case 'subirMasFotografias':
        location.href='procesos/crearFoto.php';
        break;
    }
  });
  // Botón cancelar de la caja modal. Al pulsarlo cierra la caja y devuelve false.
  $('#cancelarBtnCajaModal').click(function(){
    switch(motivo){
      case 'subirMasFotografias':
        location.href='index.php';
      default:
        cerrarCajaModal();
        restaurarTabindex();
      }
  });
}

function llegadaEliminarFoto(respuesta){
  if (respuesta){
    location.href="index.php";
  }
}

function llegadaEliminarUsuario(respuesta){
  if (respuesta){
    logout();
  }
}

// Deshabilitar los tabindex de todos los input y enlaces excepto de la 
// caja modal activa (caja1). 
function quitarTabindex(caja1, caja2){
  $('#cabecera input').attr('tabindex', '-1');
  $('#cabecera a').attr('tabindex', '-1');
  $(caja1 + ' input').attr('tabindex', '');
  $('#contenedor_externo input').attr('tabindex', '-1');
  $('#contenedor_externo a').attr('tabindex', '-1');
  $(caja2 + ' input').attr('tabindex', '-1');
}

// Restauramos los tabindex quitados al cerrar la caja modal.
function restaurarTabindex(){
  $('#cajaLogin input').attr('tabindex', '');
  $('#cajaCopy input').attr('tabindex', '');
  $('#contenedor_externo input').attr('tabindex', '');
  $('#contenedor_externo a').attr('tabindex', '');
  $('#cabecera input').attr('tabindex', '');
  $('#cabecera a').attr('tabindex', '');
}
// ========================================= INICIO GESTIÓN DEL MAPA ==============================================

// Funciones que crean y gestionan el mapa para indicar la localización en la que se ha realizado una foto.
var map;
var marcador = null;

function crearMapa(){
  var latlng = new google.maps.LatLng(0, 0);
  var myOptions = {
    zoom: 0,
    center: latlng,
    mapTypeControl: false,
    streetViewControl: false,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById("map"),
      myOptions);
  google.maps.event.addListener(map, 'click', function(event) {
      placeMarker(event.latLng);
  });
}

function placeMarker(location) {
  if (marcador != null){
    marcador.setMap(null);
    marcador = null;
  }
  marcador = new google.maps.Marker({
    position: location, 
    map: map
  });
  map.setCenter(location);
  var datos = "latitud=" + location.lat() + "&longitud=" + location.lng() + "&selector=latLong";
  $.ajax({
          type: "POST",
          url:"procesos/respuestaAjax.php",
          data: datos,
          success: function(respuesta){
            $("#latLong").html(respuesta);
          }

        });
}

function mapaQuitarMarcador(){
  marcador.setMap(null);
  var latlng = new google.maps.LatLng(0, 0);
  var datos = "latitud=" + null + "&longitud=" + null + "&selector=latLong";
  $.ajax({
        type: "POST",
        url:"procesos/respuestaAjax.php",
        data: datos,
        success: function(respuesta){
          $("#latLong").html(respuesta);
        }
        });
}

// -------------------------------- Función que muestra el mapa a partir de las coordenadas de la fotografía -----------

function mostrarMapa(latitud, longitud){
  var latlng = new google.maps.LatLng(latitud, longitud);
  var myOptions = {
    zoom: 8,
    center: latlng,
    mapTypeControl: false,
    streetViewControl: false,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var mapa = new google.maps.Map(document.getElementById("divMapaDatosFoto"),
      myOptions);
  var marcador = new google.maps.Marker({
    position: latlng, 
    map: mapa
  });
}

// ==================================== FIN GESTIÓN DEL MAPA ==========================================================

// Captura la foto del usuario que se va a subir antes de que se pulse el botón submit en el formulario.
function enviarFotoUsuario(origen){    
    var datos = new FormData();
    jQuery.each($(origen)[0].files, function(i, file) {
      datos.append('file-'+i, file);
    });
    $.ajax({
          type: "POST",
          url:"procesos/subirFotoUsuario.php",
          contentType: false,
          data: datos,
          processData: false,
          mimeType: 'multipart/form-data',
          //beforeSend: inicioEnvioFotoUsuario,
          success: llegadaFotoUsuario,
          timeout: 4000,
          }); 
    return false;
}

function inicioEnvioFotoUsuario(){
    $('#centradoMarcoFotoNueva').html('<div id="barraProgreso"></div>');
    $('#barraProgreso').progressbar();
    $('#barraProgreso').progressbar( "option", "value", false );
}

function llegadaFotoUsuario(respuesta){
    var r = JSON.parse(respuesta);
    if (r.exito == 'ok'){
      var img = new Image();
      img.src = r.imagen;
      //alert(r.imagen);
      img.onload = function(){
        $('#marcoFotoNuevaActivo').css({'width': r.ancho,
                                  'height': r.alto,
                                  '-webkit-box-shadow': '0px 0px 15px #999',
                                  '-moz-box-shadow': '0px 0px 15px #999',
                                  '-o-box-shadow': '0px 0px 15px #999',
                                  'box-shadow': '0px 0px 15px #999'
                                  });
        $('#fotoUsuario').attr('src', r.imagen);
        $('#fotoUsuario').css({'opacity': '1',
                                'width': r.ancho,
                                'height': r.alto
                              });
        $('#crearUsuario').css({'display': 'block',
                                'opacity': '1'
                              });
      }
    } else if (r.exito == 'nok'){
      $('#mensajeErrorFotoNueva').html(r.error).css('opacity', '1');
    }
}

function problemasFotoUsuario(){
    $('#mensajeErrorFotoNueva').html('Se ha producido un error en el procesamiento de la foto').css('opacity', '1');
}

// ------------------------------------------------------------------------------------------------------------

// Captura la fotografía nueva que el usuario quiere subir.
function enviarFotoFoto(origen){    
    var datos = new FormData();
    jQuery.each($('#nuevaFotoFoto')[0].files, function(i, file) {
      datos.append('file-'+i, file);
    });
    $.ajax({
          type: 'POST',
          url:'procesos/subirFotoFoto.php',
          contentType: false,
          data: datos,
          processData: false,
          mimeType: 'multipart/form-data',
          beforeSend: inicioEnvioFotoFoto,
          success: llegadaFotoFoto,
          timeout: 4000,
          error: problemasFotoFoto
          });
    return false;
}

function inicioEnvioFotoFoto(){
  $('#multiusosFotoNueva').html('<div id="barraProgreso"></div>');
  $('#barraProgreso').progressbar();
  $('#barraProgreso').progressbar( "option", "value", false );
}

function llegadaFotoFoto(respuesta){
    var r = JSON.parse(respuesta);
    if (r.exito == 'ok'){
      var img = new Image();
      img.src = r.imagen;
      img.onload = function(){
        $('#marcoFotoNueva').css({'width': r.ancho,
                                  'height': r.alto,
                                  '-webkit-box-shadow': '0px 0px 15px #999',
                                  '-moz-box-shadow': '0px 0px 15px #999',
                                  '-o-box-shadow': '0px 0px 15px #999',
                                  'box-shadow': '0px 0px 15px #999'
                                  });
        $("#marcoFotoNueva").html('<img src=""id="fotoNueva" width="' + r.ancho + '" height="' + r.alto + '"/>');
        $('#fotoNueva').attr('src', r.imagen);
        $('#fotoNueva').css('opacity', '1');
        $('#guardarFoto').css({'display': 'block',
                                'opacity': '1'
                              });
      }
    } else if (r.exito == 'nok'){
      $('#mensajeErrorFotoNueva').html(r.error).css('opacity', '1');
    }
}

function problemasFotoFoto(){
    $('#mensajeErrorFotoNueva').html('Se ha producido un error en el procesamiento de la foto').css('opacity', '1');
}
// ------------------------------------------------------------------------------------------------------------
// Captura el comentario...
function crearComentario(){   
  var textoComentario = $("textarea#nuevoComentario").val();
  var fotoUsuario = $("#imgFotoUsuarioActivo").attr('src');
  var datos = "comentario=" + textoComentario + "&fotoUsuario=" + fotoUsuario + "&selector=crearComentario";
  $.ajax({
        type: "POST",
        url:"procesos/respuestaAjax.php",
        data: datos,
        success: llegadaComentario
        });
  return false;
}

function llegadaComentario(datos){
    $("#coleccionComentarios").prepend(datos).hide().fadeIn('fast');
    $("#nuevoComentario").val('');
    $("#enviarComentario").css('opacity', '0');
    var alturaNuevoComentario = $('.nuevoComentarioCreado').height();
    var alturaColeccionComentarios = $('#coleccionComentarios').height();
    var alturaContenedor_externo = $('#contenedor_externo').height();
    var sumaAlturas = alturaColeccionComentarios + 100 + 10;
    if (sumaAlturas < 600){
      var sumaContenedor_externo = alturaContenedor_externo + alturaNuevoComentario + 10;
        $('#contenedor_externo').css('height', sumaContenedor_externo + 'px');
        $('#contenedorComentarios').css('height', sumaAlturas + 'px');
        $('.jspContainer').css('height', sumaAlturas + 'px');
      } else {
        $('#contenedorComentarios').css('height', '600px');
        $('.jspContainer').css('height', '600px');
      }
    controlBarrasScroll();
}

function problemasComentario(){
    $("#nuevoComentarioCreado").html('Problemas en el servidor.');
}

// ---------------------------------------------------------------------------------------------------------------

// ============================================= MOSAICO =========================================================
// ------------------------------------------ Cargar mosaico -----------------------------------------------------
function cargaMosaico(criterioOrdenacion, limite, pagina, claveBusqueda, valorBusqueda){
    // Se establecen los valores por defecto de los parámetros.
    criterioOrdenacion = typeof(criterioOrdenacion) != 'undefined' ? criterioOrdenacion : 'fechaSubida';
    limite = typeof(limite) != 'undefined' ? limite : numeroImagenesPorPagina;
    pagina = typeof(pagina) != 'undefined' ? pagina : '1';
    claveBusqueda = typeof(claveBusqueda) != 'undefined' ? claveBusqueda : '';
    valorBusqueda = typeof(valorBusqueda) != 'undefined' ? valorBusqueda : '';

    $('#contenedor_externo').empty();
    var datos = "criterioOrdenacion=" + criterioOrdenacion + "&limite=" + limite + "&pagina=" + pagina 
                + "&claveBusqueda=" + claveBusqueda + "&valorBusqueda=" + valorBusqueda + '&selector=mosaicoUsuario';
    $.ajax({
          type: "POST",
          url:"procesos/respuestaAjax.php",
          data: datos,
          success: llegadaMosaico,
          timeout: 4000
          });
    return false;
}

function llegadaMosaico(datos){
  var r = JSON.parse(datos);
  $('#contenedor_externo').html(r.frameMosaico);
  cargaPrincipal(r.criterioOrdenacion, r.limite, r.pagina, r.claveBusqueda, r.valorBusqueda);
  botonesMosaico();
}

function cargaPrincipal(criterioOrdenacion, limite, pagina, claveBusqueda, valorBusqueda){
    // Se establecen los valores por defecto de los parámetros.
    criterioOrdenacion = typeof(criterioOrdenacion) != 'undefined' ? criterioOrdenacion : 'fechaSubida';
    limite = typeof(limite) != 'undefined' ? limite : numeroImagenesPorPagina;
    pagina = typeof(pagina) != 'undefined' ? pagina : '1';
    claveBusqueda = typeof(claveBusqueda) != 'undefined' ? claveBusqueda : '';
    valorBusqueda = typeof(valorBusqueda) != 'undefined' ? valorBusqueda : '';

    // Gestión del color de fondo del número de página que nos indica la página activa.
    $('.numeroPagina').css('backgroundColor', 'rgba(0,0,0,0)');
    $('#' + pagina).css('backgroundColor', 'rgba(100,0,0,0.6)');

    var datos = "criterioOrdenacion=" + criterioOrdenacion + "&limite=" + limite + "&pagina=" + pagina 
                + "&claveBusqueda=" + claveBusqueda + "&valorBusqueda=" + valorBusqueda;
    $.ajax({
          type: "POST",
          url:"includes/principal.php",
          data: datos,
          contentType: "application/x-www-form-urlencoded;charset=UTF-8",
          success: llegadaPrincipal
          });
    return false;
}

function llegadaPrincipal(datos){
  $('#contenedor').html(datos);  
}

// --------------- Tamaño aletorio de las fotos del mosaico ------------------------------------------------------

// Función que carga las fotos de los mosaicos.
function cargaFoto(imagen, id){
  var img = new Image();
  img.src = imagen;
  img.onload = function(){
    var imgHTML = $('#'+id);
    imgHTML.hide();
    imgHTML.attr('src', imagen)
    imgHTML.fadeIn('slow');
  };
}

// ==================================================================================================================

// ----------------------------------------------- Nuevo 'Favorito' y nuevo 'Me gusta' -----------------------------------------
function sumaMeGustaFavorito(id, idUsuario){

  // A partir del id del botón HTML obtenemos el id de la foto y el 'tipo' que nos indica si se ha pulsado el botón
  // de 'me gusta' o de 'favorito'. Para el id de la foto capturamos 6 dígitos en previsión del crecimiento del 
  // número de fotos alojadas en el portal. ¡Seis cifras, jajaja...! :P
  var idFoto = id.substr(1,6);
  var tipo = id.substr(0,1);
  var datos = "idFoto=" + idFoto + "&idUsuario=" + idUsuario + "&selector=";
  var clase = '';
  if (tipo == 'f'){
    datos += 'sumaFavorito';
    clase = 'favorito';
  } else {
    datos += 'sumaMeGusta';
    clase = 'like';
  }
  $('#' + id).attr('disabled', 'disabled').removeClass(clase + 'BtnEnabled likeFavoritoBtnEnabled').addClass(clase + 'BtnDisabled');
  $.ajax({
        type: "POST",
        url:"procesos/respuestaAjax.php",
        data: datos,
        success: clickSumado
        });
  return false;
}

function clickSumado(respuesta){
  var r = JSON.parse(respuesta);
  if (r.tipo == 'f'){
    $('#favoritos').text(r.favoritos);

  } else {
    $('#likes').text(r.likes);
  }
  $('#valoracionNumero').text(r.puntuacion);
}

// ------------------------------------ Validar datos de login -----------------------------------------------------------
function validarDatosLogin(){
  $('.error').fadeOut('slow').remove();
  var loginNombreUsuario = $('#loginNombreUsuario').val();
  var loginContrasena = $('#loginContrasena').val();
  if (loginNombreUsuario == ''){
    $('#cajaLogin').css({'webkit-box-shadow': '0px 0px 50px #F00',
                          '-moz--box-shadow': '0px 0px 50px #F00',
                          '-o-box-shadow': '0px 0px 50px #F00',
                          'box-shadow': '0px 0px 50px #F00',
                          'border-color': 'rgba(255,0,0,0.7)'
                      });
    $('#loginNombreUsuario').focus();
    return false;
  } else if (loginContrasena == ''){
    $('#cajaLogin').css({'webkit-box-shadow': '0px 0px 50px #F00',
                          '-moz--box-shadow': '0px 0px 50px #F00',
                          '-o-box-shadow': '0px 0px 50px #F00',
                          'box-shadow': '0px 0px 50px #F00',
                          'border-color': 'rgba(255,0,0,0.7)'
                      });
    return false;
  }
  var datos = "loginNombreUsuario=" + loginNombreUsuario + "&loginContrasena=" + loginContrasena + "&selector=login";
  $.ajax({
          type: "POST",
          url:"procesos/respuestaAjax.php",
          data: datos,
          success: llegadaLogin
        });
}

function llegadaLogin(respuesta){
  var urlActual = location.href;
  if (respuesta){
    if (urlActual == 'http://localhost/fotografia/procesos/crearUsuario.php'){
        location.href='index.php';
    } else {
        location.href=urlActual;
    }
  } else {
    $('#cajaLogin').css({'webkit-box-shadow': '0px 0px 50px #F00',
                          '-moz--box-shadow': '0px 0px 50px #F00',
                          '-o-box-shadow': '0px 0px 50px #F00',
                          'box-shadow': '0px 0px 50px #F00',
                          'border-color': 'rgba(255,0,0,0.7)'
                      });
    $('#loginContrasena').val('');
  }
}

function cancelarLogin(){
  $('#cajaLogin').css({'opacity': '0',
                        'display': 'none',
                        'top': '200px',
                        'height': '200px',
                        'z-index': '-5',
                        'webkit-box-shadow': '0px 0px 50px #FFF',
                        '-moz--box-shadow': '0px 0px 50px #FFF',
                        '-o-box-shadow': '0px 0px 50px #FFF',
                        'box-shadow': '0px 0px 50px #FFF',
                        'border-color': '#BBB' 
                        }).removeClass('rotacion');
  $('#datosLogin').css('backgroundColor', 'rgba(100,100,100,0)');
  $('#errorLogin').css('opacity', '0');
  $('.error').remove();
  $('#loginNombreUsuario').val('');
  $('#loginContrasena').val('');
  $('#fondoModal').removeClass('fondoModalOn').addClass('fondoModalOff');
  $('#contenedor_externo').css({'-webkit-filter': 'blur(0px) grayscale(0)',
                                'filter': 'blur(0px) grayscale(0)'
                                });
}
//----------------------------------------------------------------------------------------------------------------------------

// Cerrar caja modal.
function cerrarCajaModal(){
  $('#idCajaModal').css({'opacity': '0',
                      'display': 'none',
                      'top': '0px',
                      'height': '200px',
                      'z-index': '-5' 
                      }).removeClass('rotacion');
  $('#fondoModal').removeClass('fondoModalOn').addClass('fondoModalOff');
  $('#contenedor_externo').css({'-webkit-filter': 'blur(0px) grayscale(0)',
                                'filter': 'blur(0px) grayscale(0)'
                                });
}

// Se cierra sesión.
function logout(){
  var datos = "selector=logout";
  $.ajax({
          type: "POST",
          url:"procesos/respuestaAjax.php",
          data: datos,
          success: llegadaLogout
        });
}

function llegadaLogout(){
  location.href="index.php";
}

// Gestión de los botones de ordenación y paginación del mosaico principal.
function botonesMosaico(){
  $('.mosaicoBtn').click(function(){
    var id = $(this).attr('id');
    cargaPrincipal(id, numeroImagenesPorPagina, 1, '', '');
    $('.mosaicoBtn').css('backgroundColor', 'rgba(0,0,0,0)');
    $(this).css('backgroundColor', 'rgba(100,0,0,0.6)');

  });

  // Gestión de paginación del mosaico.
  $('.numeroPagina').click(function(){
    var pagina = $(this).attr('id');
    var criterioOrdenacion = $('#criterioOrdenacion').val();
    cargaPrincipal(criterioOrdenacion, numeroImagenesPorPagina, pagina, '', '');
    $('.numeroPagina').css('backgroundColor', 'rgba(0,0,0,0)');
    $(this).css('backgroundColor', 'rgba(100,0,0,0.6)');
  });

  // Gestionamos la barra de scroll del paginador.
  $('#paginas').jScrollPane();
  $('.jspScrollable').mouseenter(function(){
    if ($(this).attr('id') == 'paginas'){
      if (parseInt($('#paginasInterior').css('width')) > 99){
        $(this).find('.jspDrag').stop(true, true).css('opacity', '1');
      }
    }
  });
  $('.jspScrollable').mouseleave(function(){
      $(this).find('.jspDrag').stop(true, true).css('opacity', '0');
  });
}

// Función que se ejecuta al hacer click en el botón de modificar datos de usuario.
// Compruba que los datos sean correctos antes de enviarlos al servidor.
function modificarUsuario(){
  var idUsuario = $('#idUsuario').val();
  var nombreUsuario = $('#nombreUsuario').val();
  var nombre = $('#datoDatoNombre').val();
  var apellidos = $('#datoDatoApellidos').val();
  var email = $('#datoDatoEmail').val();
  var email2 = $('#datoDatoEmail2').val();
  var url = $('#datoDatoUrl').val();
  var acerca = $('#datoDatoAcerca').val();
  var contrasena = $('#datoDatoContrasena').val();
  var contrasena2 = $('#datoDatoContrasena2').val();
  var expRegEmail = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
  var datos = 'idUsuario=' + idUsuario + '&nombreUsuario=' + nombreUsuario +
             '&nombre=' + nombre + '&apellidos=' + apellidos;
  var okEmail = false;
  var okContrasena = false;
  if (!expRegEmail.test(email)){
    $('#datoDatoEmail').focus().css({'border-top': '1px solid #C00',
                              'border-right': '1px solid #F00',
                              'border-bottom': '1px solid #F00',
                              'border-left': '1px solid #C00'
                            });
  } else if (email2 != email){
    $('#datoDatoEmail2').focus().css({'border-top': '1px solid #C00',
                              'border-right': '1px solid #F00',
                              'border-bottom': '1px solid #F00',
                              'border-left': '1px solid #C00'
                            });
  } else {
    okEmail = true;
    datos += '&email=' + email;
    datos += '&url=' + url + '&acerca=' + acerca;
  }
  if (contrasena != '' && contrasena2 != contrasena){
    $('#datoDatoContrasena2').focus().css({'border-top': '1px solid #C00',
                              'border-right': '1px solid #F00',
                              'border-bottom': '1px solid #F00',
                              'border-left': '1px solid #C00'
                            });
  } else {
    if (contrasena != ''){
      datos += '&contrasena=' + contrasena;
    }
    okContrasena = true;
  }
  if (okEmail && okContrasena){
    datos += '&selector=modificarUsuario';
    $.ajax({
          type: "POST",
          url:"procesos/respuestaAjax.php",
          data: datos,
          success: llegadaModificarUsuario
        });
    
  }
}

function llegadaModificarUsuario(datos){
  if (datos){
    setTimeout(function(){location.href='procesos/mostrarUsuario.php';}, 0);
  } else {
    alert('wa, wa, waaaaaaa...');
  }
}

