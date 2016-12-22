<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
    session_start();
?>
<!--            <input type="button" id="btnConsultar" value="Ver en pantalla" class="btn btn-default btn-primary"> 
            <input type="button" id="btnQryToXls" value="Enviar a Excel" class="btn btn-default btn-success"> -->

<div class="form-group" >
    <div class="row">
        <label class="control-label col-md-10">Seleccione Cliente</label>
        <div class='col-lg-4' id="sel">
        </div>
    </div>
    <br>
    <div class="row">
        <div class='col-lg-4'>
            <div class="btn-group">
                <input type="button" id="btnConsultar" value="Ver en pantalla" class="btn btn-default btn-primary"> 
                <input type="button" id="btnQryToXls" value="Enviar a Excel" class="btn btn-default btn-success"> 
            </div>    
        </div>
    </div>
</div>
<br>
<div id='DataResult'></div>
<script>
    $(document).ready(function(){
        var model ={
            QUERY: 'CLIENTESACTIVOS'
        };
        $.getJSON('../Controllers/Clientes',model, function(data){
            var html_c = '<select class="form-control" id="clientesselect">';
            html_c += '<option> Selecciona Cliente [Opcional]</option>'
            $.each(data, function(i, item){  
                html_c += '<option value="'+item.id_cliente+'">';
                html_c += item.nombre_cliente;
                html_c += '</option>';      
            });
            html_c += '<select>';
            $('#sel').html(html_c);
        });
        $('#btnConsultar').click(function(e){
            e.preventDefault();
            var model = {
                idcliente: $( "#clientesselect option:selected" ).text(),
                ACCION: 'QRY_Pagos'
            };

            $.ajax({
                url: '../Controllers/Pagos.php',
                data: model,
                dataType: 'text',
                type: 'GET',
                success: function (data) {
                   $('#DataResult').html(data);  
                }
            });                                   
        });        
    });
</script>
    
