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
<div id='DataResult'></div>
<script>
    $(document).ready(function(){
        $('#btnConsultar').click(function(e){
            e.preventDefault();
            $.ajax({
                url: '../Controllers/Pagos.php',
                datatye: 'json',
                type:'GET',
                success: function(data){
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
                    tbdataview +="</tr>";
                    tbdataview +="</thead>";
                    tbdataview +="<tbody>";
                    $.each(data, function(i, item){
                        tbdataview +="<tr>";
                        tbdataview +="<td>"+item.id_cliente+"</td>";
                        tbdataview +="<td>"+item.fecha+"</td>";
                        tbdataview +="<td>"+item.habitacion+"</td>";
                        tbdataview +="<td>"+item.dias_estadia+"</td>";
                        tbdataview +="<td>"+item.valor_pp+"</td>";
                        tbdataview +="<td>"+item.total_adeudado+"</td>";
                        tbdataview +="</tr>";
                        //total += parseInt(item.TOTAL);
                    });
                    tbdataview +="</tbody>";
                    tbdataview +="</table>";
                    $('#DataResult').html("</br>" + tbdataview);                    
                }
            });
        });        
    });
</script>
    
