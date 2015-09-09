<!-- cajaModal.php -->
<div class="cajaModal" id="idCajaModal">
    <div class="tituloCajaModal" id="idTituloCajaModal">
        &iexcl;Atenci&oacute;n!
    </div>
    <div class="cuerpoCajaModal" id="idCuerpoCajaModal">
        <div id="textoCajaModal">Esta imagen está protegida por copyright</div>
            <div id="botonesCajaModal">
                <input type="button" class="botonCajaModal" id="aceptarBtnCajaModal" value="Aceptar"/>
                <input type="button" class="botonCajaModal" style="display: none;" id="cancelarBtnCajaModal" value="Cancelar"/>
                <input type="hidden" id="valorCajaModal" value=""/> <!-- Dato necesario para la operación. En el caso de que se quiera
                eliminar una fotografía será el id de la foto. Si se quiere eliminar un usuario, id de usuario. ... -->
                <div class="blanco"></div>
            </div>
    </div>
</div>