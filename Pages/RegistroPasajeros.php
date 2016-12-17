<!DOCTYPE html>
<?php 
    session_start();
?>
<!--
Desarrollado por : Jaime Francisco Altamirano Bustamante.
Noviembre 2016
-->
<!--<link href="../Scripts/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css"/>-->
<link href="../Scripts/datetimepicker/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="../Scripts/contextmenu/css/jquery.contextMenu.css" rel="stylesheet" type="text/css"/>
<script src="../Scripts/jquery/jquery.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="../Scripts/jquery/jquery-ui.js" type="text/javascript"></script>
<!--<script src="../Scripts/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>-->
<script src="../Scripts/jquery/jquery.validate.js" type="text/javascript"></script>
<script src="../Scripts/datetimepicker/js/jquery.datetimepicker.js" type="text/javascript"></script>
<script src="../Scripts/AppApi.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        //Functions
        function RegPasEx(){
            var valid = true;
            if ( valid ) {
              $( "#PASEX tbody" ).append( 
                "<tr>" +
                  "<td>" + $('#NOM_PASEX').val() + "</td>" +
                  "<td>" + $('#TELEFONO_PASEX').val() + "</td>" +
                "</tr>" );
            }
            return valid;
        }
        function tableToJson(table){
            var jsObj = [],
                tbDataRows = $(table).find('tbody tr');
            if (tbDataRows.length > 0) {
                for (rows = 0; rows < tbDataRows.length; rows++) {
                    rowscols = $(tbDataRows).eq(rows).find('td');
                    if (rowscols.length > 0) {
                        debugger;
                        var obj = {};
                        for (cols = 0; cols < rowscols.length; cols++) {
                            key = $(table.find('thead tr')[0]).find('th').eq(cols).attr('id');
                            //if (key != null) {
                                value = $(rowscols).eq(cols).text();
                                obj[key] = value;
                            //}
                        }
                        jsObj.push(obj);
                    }
                }
            }
            return JSON.stringify(jsObj);
        }
        //Settings  
        $('#btnFormaDePago').hide();
        if(window.sharedVariable !== null || window.sharedVariable !== ' '){
            $('#HABITACION').val(window.sharedVariable);
        }        
//        var dialog = $('#scrAddPas').dialog({
//            autoOpen: false,
//            height: 280,
//            width: 600,
//            modal: true,
//            buttons: {
//                "Registrar": RegPasEx,
//                Cerrar: function() {
//                    dialog.dialog( "close" );
//                }
//            }
//        });
        var diagfp = $('#formasdepago').dialog({
            autoOpen: false,
            height: screen.availHeight * 75 /100,
            width: screen.availWidth * 50 /100,
            modal: true
        });
        //Eventos Locales
        $('#btnFormaDePago').click(function(e){
            e.preventDefault();
            $('#formasdepago').load('FormasDePago.php');
            diagfp.dialog('open');
            return false;
        });
        $('#HORA_INGRESO').CurrentDateTime();
        $('#ID_CLIENTE').blur(function(){
            $('input[class^="pac"]').val('');
            $('input[class^="pac"]').attr('readonly', false);
            var model ={
                IDCLIENTE : $(this).val()
            };
            $.getJSON('../Services/ClientesHandler.php',model, function(data){
                $.each(data, function(){
                    $('#NOMBRE').val(data.NOMBRE_CLIENTE);
                    $('#NACIONALIDAD').val(data.NACIONALIDAD);
                    $('#CIUDAD').val(data.PROCEDENCIA);
                    $('#DIRECCION').val(data.DOMICILIO);
                    $('#TELEFONO').val(data.FONO);
                    $('input[class^="pac"]').attr('readonly', $('input[class^="pac"]').val() !== ' ');                });
            });
        });
        $('#btnGuardar').click(function(e){
            e.preventDefault();
            if (!ValidateRequiredFields())
            {
                return false;
            }

//            var _jsondata = tableToJson('#PASEX');
//            alert(_jsondata);
            var form = $('#frmRegPas');
            var formData = new FormData(form[0]);

            $.ajax({
                url: '../Controllers/RegistroPasajeros.php',
                data: formData,
                dataType: 'text',
                type: 'POST',
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    var jdata = data.split(',');
                    $('#ID_REGISTRO').val(jdata[1]);
                    alert(jdata[0].trim()+ ". Id referencia: "+jdata[1]);
                    $('#btnFormaDePago').show();
                }
            });
        });
        $('#btnPrint').click(function(){
            //var form = $('#frmRegPas');
            //var formData = new FormData(form[0]);

            $.ajax({
                url: '../Controllers/RegistroPasajerosToPdf.php',
                data: '123',
                dataType: 'text',
                type: 'POST',
                success: function (data) {
                   window.location.href = data;
                }
            });
        });
        $('#AddPas').click(function(){
            $('#ID_CLIENTE_PASEX').val('');
            $('#NOM_PASEX').val('');
            $('#TELEFONO_PASEX').val('');
            dialog.dialog( "open" );
            return false;
        });
        $('#btnLimpiar').click(function(){
            $( "#PASEX tbody tr" ).remove();
        });
        $('#DIAS_ESTADIA').focusout(function(){
            var finaldate = $('#HORA_INGRESO').val().split('/');
            var fechasalida = parseInt(finaldate[0]) + parseInt($(this).val()=== null ? '0' : $(this).val()) +'/'+finaldate[1]+'/'+finaldate[2].split(' ')[0] +' '+ finaldate[2].split(' ')[1];
            $('#FECHA_SALIDA').val(fechasalida);
        });
        $('#VALOR').focusout(function(){
        	$('#TOTAL').val($('#VALOR').val() * $('#DIAS_ESTADIA').val());
        });              
        $('#HORA_INGRESO').focusout(function(){
            var finaldate = $('#HORA_INGRESO').val().split('/');
            var fechasalida = parseInt(finaldate[0]) + parseInt($('#DIAS_ESTADIA').val()=== null ? '0' : $('#DIAS_ESTADIA').val()) +'/'+finaldate[1]+'/'+finaldate[2].split(' ')[0] +' '+ finaldate[2].split(' ')[1];
            $('#FECHA_SALIDA').val(fechasalida);
        }).attr('readonly', true);
    });
</script>

<form method="post" role="form" id='frmRegPas' class='form-horizontal' action="../Controllers/RegistroPasajeros.php">
    <input type="hidden" id="ACCION" name="ACCION" value="ADD"/>    
    <input type="hidden" id="ID_REGISTRO" name="ID_REGISTRO" value="0"/>    
    <div class="caption"><b>Registro de Pasajeros</b></div>
    <hr>
    <div class="caption">Datos del Pasajero</div>
    <div class="form-group" >
        <label for="ID_CLIENTE" class='control-label col-sm-3'>Id Cliente</label>
        <div class='col-lg-2'>
            <input type="text" id="ID_CLIENTE" name="ID_CLIENTE" data-required='Debe indicar cliente' class="form-control"/>
        </div>
    </div>
    <div class="form-group " >
        <label for="NOMBRE" class='control-label col-sm-3'>Nombre</label>
        <div class='col-lg-4'>
            <input type="text" id="NOMBRE" name="NOMBRE" data-required='El nombre del cliente no puede estar vac&iacute;o' class="pac form-control text-capitalize"/>
        </div>
    </div>
    <div class="form-group " >
        <label for="NACIONALIDAD" class='control-label col-sm-3'>Nacionalidad</label>
        <div class='col-lg-4'>
            <input type="text" id="NACIONALIDAD" name="NACIONALIDAD"  class="pac form-control  text-capitalize"/>
        </div>
    </div>
    <div class="form-group " >
        <label for="CIUDAD" class='control-label col-sm-3'>Ciudad</label>
        <div class='col-lg-4'>
            <input type="text" id="CIUDAD" name="CIUDAD"  class="pac form-control  text-capitalize"/>
        </div>
    </div>
    <div class="form-group " >
        <label for="DIRECCION" class='control-label col-sm-3'>Domicilio</label>
        <div class='col-lg-4'>
            <input type="text" id="DIRECCION" name="DIRECCION"  class="pac form-control  text-capitalize"/>
        </div>
    </div>
    <div class="form-group " >
        <label for="TELEFONO" class='control-label col-sm-3'>Telefono</label>
        <div class='col-lg-4'>
            <input type="text" id="TELEFONO" name="TELEFONO"  class="pac form-control"/>
        </div>
    </div>    
    <hr>
    <div class="caption">Datos de la estadia</div>
    <div class="form-group " >
        <label for="DIAS_ESTADIA" class='control-label col-sm-3'>Dias de Estadia</label>
        <div class='col-lg-1'>
            <input type="text" id="DIAS_ESTADIA" name="DIAS_ESTADIA" data-required='Debe indicar dias de estada' data-type='number' class="form-control text-center" value="1"/>
        </div>
    </div>  
    <div class="form-group " >
        <label for="HORA_INGRESO" class='control-label col-sm-3'>Check In</label>
        <div class='col-lg-3'>
            <input type="text" id="HORA_INGRESO" name="HORA_INGRESO" data-required='Debe indicar fecha y hora de ingreso' data-type='datetime' class="form-control text-center"/>            
        </div>
    </div>  
    <div class="form-group " >
        <label for="FECHA_SALIDA" class='control-label col-sm-3'>Check Out</label>
        <div class='col-lg-3'>
            <input type="text" id="FECHA_SALIDA" name="FECHA_SALIDA" class="form-control text-center" placeholder="Salida" readonly/>
        </div>        
    </div>   
    <div class="form-group " >
        <label for="HABITACION" class='control-label col-sm-3'>Pieza</label>
        <div class='col-lg-1'>
            <input type="text" id="HABITACION" name="HABITACION" data-required='Debe indicar pieza' data-type='number' class="form-control text-center" style="min-width: 75px;"/>
        </div>
    </div> 
    <div class="form-group " >
        <label for="VALOR" class='control-label col-sm-3'>Valor Hab.</label>
        <div class='col-lg-1'>
            <input type="text" id="VALOR" name="VALOR" data-type='number' class="form-control text-center" style="min-width: 75px;"/>
        </div>
        <div class='col-lg-1'>
            <input type="text" id="TOTAL" name="TOTAL"  class="form-control text-center disabled" disabled="" placeholder="A cobrar" style="min-width: 75px;"/><span style='font-size: 9px; color:red;'>Con Iva</span>            
        </div>
        <div class='col-lg-1'>
            <button  id="btnFormaDePago" style="font-size: 18px; border:none; background-color: transparent;" title="Ingresar forma de pago"><span class="glyphicon pay32x32"></span></button>
        </div>
    </div> 
    <div class="form-group " >
        <label for="PATENTE" class='control-label col-sm-3'>Patente</label>
        <div class='col-lg-1'>
            <input type="text" id="PATENTE" name="PATENTE" class="form-control text-center" style="min-width: 75px;"/>
        </div>
    </div> 
<!--    <div class="form-group">
        <table id="PASEX" class="table table-bordered table-responsive" style="width: 70%;">
            <caption><b>Pasajeros extras en la habitaci&#243;n</b>
                <button id="AddPas" style="font-size: 18px; border:none; background-color: transparent;" title="Agregar Pasajero extra a la habitaci&#243;n">        
                    <span class="glyphicon glyphicon-plus-sign"></span>
                </button>
            </caption>
            <thead>
                <tr>
                    <td>Nombre</td>
                    <td>Fono Contacto</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>-->
    <div class="col-lg-6" >
        <div class='btn-group'>
            <input type="submit" id="btnGuardar" class="btn btn-default btn-primary" value="Guardar"/>
            <input type="reset" id="btnLimpiar" class="btn btn-default btn-warning" value="Nuevo Registro"/>
            <input type="button" id="btnPrint" class="btn btn-default btn-success" value="Imprimir"/>
        </div>
    </div>  
</form>
<!--<div id='scrAddPas' title="Pasajeros extras en la habitaci&#243;n" class='container'>
    <form>
    <div class="form-horizontal" style="margin:10px;">
        <div class="form-group" >
            <label for="ID_CLIENTE_PASEX" class='control-label col-sm-3'>Id Pasajero</label>
            <div class='col-lg-3'>
                <input type="text" id="ID_CLIENTE_PASEX" name="ID_CLIENTE_PASEX" class="form-control"/>
            </div>
        </div>
        <div class="form-group " >
            <label for="NOM_PASEX" class='control-label col-sm-3'>Nombre</label>
            <div class='col-lg-6'>
                <input type="text" id="NOM_PASEX" name="NOM_PASEX"  class="form-control text-capitalize"/>
            </div>
        </div>
        <div class="form-group " >
            <label for="TELEFONO_PASEX" class='control-label col-sm-3'>Fono Contacto</label>
            <div class='col-lg-6'>
                <input type="text" id="TELEFONO_PASEX" name="TELEFONO_PASEX"  class="form-control text-capitalize"/>
            </div>
        </div>
    </div>
    </form>    
    <div id="mensaje"></div>
</div>-->
<div id="formasdepago"></div>

