 <?php
session_start();
require_once ('../Services/Db.php');
/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
function TraeSaldo(){
    $idregistro = filter_input(INPUT_GET, 'ID_REGISTRO');
    $My_Qry =
        "
        select (@deuda := regpas.valor_pp*regpas.dias_estadia) deuda, (@abonos := ifnull(sum(pagos.monto_cancelado), 0)) abonos , 
        cast((@deuda-@abonos) as signed) saldo
        from hot_formas_de_pago pagos
        inner join hot_regpas regpas on regpas.id_registro = pagos.id_registro
        where pagos.id_registro =".$idregistro;

        $db = new Db();                
        $qryresult = $db->ExecQuery($My_Qry);
        if($qryresult->num_rows>0){
            $row = $qryresult->fetch_assoc();
            $saldo = $row['saldo'];
            echo $saldo;
        }
}
?>
<div class="container">
    <form rol="form" id="frmFormasDePago" method="POST">
        <div class="form-group" >
            <div class="row">
                <label class='control-label  col-sm-2'>Tipo de Pago</label>
                <div class='col-lg-2'>
                    <label class="radio-inline"><input type="radio" name="TIPO_PAGO" value="0">Abono</label>
                    <label class="radio-inline"><input type="radio" name="TIPO_PAGO" checked="true" value="1">Pago</label>            
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Modalidad De Pago</label>
                <div class='col-lg-3'>
                    <select class="form-control" id="MODALIDAD_PAGO" name="MODALIDAD_PAGO">
                        <option value="0"> Seleccione opci&oacute;n</option>  
                        <option value="1">Efectivo</option>
                        <option value="2">Tarjeta d&eacute;bito</option>
                        <option value="3">Tarjeta cr&eacute;dito</option>
                        <option value="4">Transferencia</option>
                    </select> 
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Voucher</label>
                <div class='col-lg-3'>
                    <input type="text" id="VOUCHER" name="VOUCHER" class="form-control" data-type="number"/>
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Tipo De Documento</label>
                <div class='col-lg-3'>
                    <select class="form-control" id="TIPO_DOCUMENTO" name="TIPO_DOCUMENTO">
                        <option value="0"> Seleccione opci&oacute;n</option>  
                        <option value="1">Boleta</option>
                        <option value="2">Factura</option>
                    </select> 
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Nro. Documento</label>
                <div class='col-lg-3'>
                    <input type="text" id="NRO_DOCUMENTO_REFERENCIA" name="NRO_DOCUMENTO_REFERENCIA" class="form-control" data-type="number"/>
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Monto adeudado</label>
                <div class='col-lg-3'>
                    <input type="text" id="MONTO_ADEUDADO" name="MONTO_ADEUDADO" readonly="" class="form-control"/>
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Monto cancelado</label>
                <div class='col-lg-3'>
                    <input type="text" id="MONTO_CANCELADO" name="MONTO_CANCELADO" class="form-control" data-type="number" data-required='Debe indicar monto cancelado'/>
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Saldo</label>
                <div class='col-lg-3'>
                    <input type="text" id="SALDO" name="SALDO" readonly="" class="form-control"/>
                </div>
            </div> 
            <br>
            <div class="row">
                <div class='col-lg-3' id="divSaveFP">
                    <input type="button" id="btnSaveFP" class="btn btn-success" value="Guardar"/>
                </div>
            </div>            
        </div>
        <input type="hidden" id='ID_REGISTRO'/>
    </form>
</div>
<script>
    $(document).ready(function(){
        $('#ID_REGISTRO').val(global.idtransaction);
    });
    var model ={
        ID_REGISTRO: global.idtransaction,
        ACCION:'QRY'
    };
    $.getJSON('../Controllers/FormasDePago.php',model, function(data){
        if(data !== null){
            $.each(data, function(){
                $('input[name=TIPO_PAGO]:checked').val(data.tipo_pago || 1);
                $('#MODALIDAD_PAGO').val(data.modalidad_pago || 1);
                $('#VOUCHER').val(data.voucher);
                $('#TIPO_DOCUMENTO').val(data.tipo_documento  || 1);
                $('#NRO_DOCUMENTO_REFERENCIA').val(data.nro_documento_referencia);
                $('#MONTO_CANCELADO').val(data.monto_cancelado);
                $('#MONTO_ADEUDADO').val(data.monto_adeudado);
                var cancela = $('#MONTO_CANCELADO').val() || 0;
                $('#SALDO').val($('#MONTO_ADEUDADO').val() - cancela);
                if(parseInt($('#SALDO').val())=== 0){
                    $('#MONTO_CANCELADO').attr('readonly', true);
                    $('#btnSaveFP').css('display', 'none'); 
                    $('#divSaveFP').html('<h4 style="color:red;">El cliente no tiene deuda</h4>'); 
                }
            });
        }
    });
    
    $('#MONTO_ADEUDADO').val($('#TOTAL').val());
    $('#MONTO_CANCELADO').focusout(function(){
        var cancela = $('#MONTO_CANCELADO').val() || 0;
        if(parseInt(cancela) > parseInt($('#SALDO').val())){
            alert('No se puede cancelar un moto mayor a lo que se adeuda');
            $('#MONTO_CANCELADO').val($('#SALDO').val());
        }
        $('#SALDO').val($('#MONTO_ADEUDADO').val() - cancela);
    });
    $('#btnSaveFP').click(function(e){
        e.preventDefault();
//        if (!ValidateRequiredFields())
//        {
//            return false;
//        }

        var model = {
            ID_REGISTRO: global.idtransaction,
            TIPO_PAGO: $('input[name=TIPO_PAGO]:checked').val(),
            MODALIDAD_PAGO: $('#MODALIDAD_PAGO').val(),
            VOUCHER: $('#VOUCHER').val()|| 0,
            TIPO_DOCUMENTO: $('#TIPO_DOCUMENTO').val(),
            NRO_DOCUMENTO_REFERENCIA: $('#NRO_DOCUMENTO_REFERENCIA').val() || 0,
            MONTO_CANCELADO: $('#MONTO_CANCELADO').val(),
            ACCION:'ADD'
        };
        $.ajax({
           type:'GET',
           url:'../Controllers/FormasDePago.php',
           data:model,
           datatype:'text',
           success: function(data){
               alert(data);
           }
        });
    });
</script>