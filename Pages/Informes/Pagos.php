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
            $.getJSON('../Controllers/Pagos.php',model, function(data){
                var tbdataview ="";
                $('#DataResult').html(tbdataview);
                tbdataview +="<table class='table table-bordered' data-type='dataview' id='dataresult'>";
                tbdataview +="<thead style='background-color:#DAE1E5;'>";
                tbdataview +="<th>Fecha</th>";
                tbdataview +="<th>Cliente</th>";
                tbdataview +="<th>Habitaci&oacute;n</th>";
                tbdataview +="<th>D&iacute;as Estad&iacute;a</th>";
                tbdataview +="<th>Valor</th>";
                tbdataview +="<th>Valor Total</th>";
                tbdataview +="<th>Abonos</th>";
                tbdataview +="</tr>";
                tbdataview +="</thead>";
                tbdataview +="<tbody>";          
                $.each(data, function(){
                    tbdataview +="<tr>";
                    tbdataview +="<td>"+data.id_cliente+"</td>";
                    tbdataview +="<td>"+data.fecha+"</td>";
                    tbdataview +="<td>"+data.habitacion+"</td>";
                    tbdataview +="<td>"+data.dias_estadia+"</td>";
                    tbdataview +="<td>"+data.valor_pp+"</td>";
                    tbdataview +="<td>"+data.total_adeudado+"</td>";
                    tbdataview +="<td>"+data.abonos+"</td>";
                    tbdataview +="</tr>";                    
                });
                tbdataview +="</tbody>";
                tbdataview +="</table>";
                $('#DataResult').html(tbdataview);                   
            });
        });        
    });
</script>
    
