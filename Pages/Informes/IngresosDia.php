<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
$_SESSION['FECHA_INGRESO'] = "";
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link href="../Scripts/datetimepicker/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="../Scripts/jquery/jquery-ui.js" type="text/javascript"></script>
        <script src="../Scripts/jquery/jquery.js" type="text/javascript"></script>
        <script src="../Scripts/datetimepicker/js/jquery.datetimepicker.js" type="text/javascript"></script>
<!--        <script src="../Scripts/AppApi.js" type="text/javascript"></script>-->
        <script>
            $(document).ready(function(){
                // Funciones
                
                //Settings
                $('#FECHA_INGRESO').datetimepicker({ timepicker:false,mask:true,format: 'd/m/Y' });
                // Métodos
                $('#btnConsultar').click(function(e){
                    e.preventDefault();
                    var form = $('#frmQryIngDia');
                    var formData = new FormData(form[0]);

                    $.ajax({
                        url: '../Controllers/IngresosDia.php',
                        data: formData,
                        dataType: 'json',
                        type: 'POST',
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            var tbdataview ="";
                            var total=0;
                            $('#DataResult').html(tbdataview);
                            tbdataview +="<table class='table table-bordered' data-type='dataview' id='dataresult'>"
                            tbdataview +="<colgroup>";
                            tbdataview +="<col span='5'>";
                            tbdataview +="<col style='background-color:#DAE1E5;text-align: right;'>";
                            tbdataview +="</colgroup>";
                            tbdataview +="<thead style='background-color:#DAE1E5;'>";
                            tbdataview +="<tr>";
                            tbdataview +="<th>Cliente</th>";
                            tbdataview +="<th>Check In</th>";
                            tbdataview +="<th>Habitaci&oacute;n</th>";
                            tbdataview +="<th>D&iacute;as Estad&iacute;a</th>";
                            tbdataview +="<th>Valor</th>";
                            tbdataview +="<th>Valor Total</th>";
                            tbdataview +="</tr>";
                            tbdataview +="</thead>";
                            tbdataview +="<tbody>";
                            $.each(data, function(i, item){
                                tbdataview +="<tr>";
                                tbdataview +="<td>"+item.CLIENTE+"</td>";
                                tbdataview +="<td>"+item.hora_ingreso+"</td>";
                                tbdataview +="<td>"+item.habitacion+"</td>";
                                tbdataview +="<td>"+item.dias_estadia+"</td>";
                                tbdataview +="<td>"+item.valor_pp+"</td>";
                                tbdataview +="<td>"+item.TOTAL+"</td>";
                                tbdataview +="</tr>";
                                total += parseInt(item.TOTAL);
                            }) ;
                            tbdataview +="</tbody>";
                            tbdataview +="</table>";
                            $('#DataResult').html(tbdataview);
                            $('#dataresult').append('<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th>'+total+'</th></tr></tfoot>');
                        }
                    });
                });
                $('#btnQryToXls').click(function(e){
                    e.preventDefault();
                    var form = $('#frmQryIngDia');
                    var formData = new FormData(form[0]);

                    $.ajax({
                        url: '../Controllers/IngresosDiaToXls.php',
                        data: formData,
                        dataType: 'text',
                        type: 'POST',
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            //alert(data);
                            window.location.href = data;
                        }
                    });
                });
            })
        </script>
    </head>
    <body>
        <div class="container">
        <form method="post" role="form" id='frmQryIngDia' class='form-horizontal' action="../Controllers/IngresosDia.php">
            <div class="form-group " >
                <label for="HORA_INGRESO" class='control-label col-sm-3'>Check In</label>
                <div class='col-lg-2'>
                    <input type="text" id="FECHA_INGRESO" name="FECHA_INGRESO" data-type='datetime' class="form-control text-center"/>            
                </div>
            </div>  
            <div class="form-group" >
                <div class='col-lg-4'>
                    <div class="btn-group">
                        <input type="button" id="btnConsultar" value="Ver en pantalla" class="btn btn-default btn-primary"> 
                        <input type="button" id="btnQryToXls" value="Enviar a Excel" class="btn btn-default btn-success"> 
                    </div>    
                </div>
            </div>
            <div id='DataResult'></div>
        </form>
        </div>
    </body>
</html>