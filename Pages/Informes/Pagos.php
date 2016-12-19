<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
?>
<!--            <input type="button" id="btnConsultar" value="Ver en pantalla" class="btn btn-default btn-primary"> 
            <input type="button" id="btnQryToXls" value="Enviar a Excel" class="btn btn-default btn-success"> -->

<div class="form-group" >
    <div class='col-lg-4'>
        <div class="btn-group">
            <input type="button" id="btnConsultar" value="Ver en pantalla" class="btn btn-default btn-primary"> 
            <input type="button" id="btnQryToXls" value="Enviar a Excel" class="btn btn-default btn-success"> 
        </div>    
    </div>
</div>
<br>
<div id='DataResult'></div>
<script>
    $(document).ready(function(){
        $('#btnConsultar').click(function(e){
            e.preventDefault();
            var model = {
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
    
