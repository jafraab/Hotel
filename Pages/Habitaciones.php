<div class="Hotel">
    <div class="toolbar">
        <div class="toolbuttoncontainer btn-group" style="margin-right: 10px;">
            <input id="btnCheckIn" data-action="CHECKIN" type="button" class="btn btn-default toolbutton checkin32x32" title="Registar pasajero en esta habitaci&oacute;n"/>
        </div>
        <div class="toolbuttoncontainer btn-group" style="margin-right: 10px;">
            <input id="btnRoomChange" data-action="CAMBIAR" type="button" class="btn btn-default toolbutton roomchange32x32" title="Trasladar pasajero a otra habitaci&oacute;n"/>
        </div>
        <div class="toolbuttoncontainer btn-group" style="margin-right: 10px;">
            <input id="btnAgregarPago" data-action="AGREGARPAGO"  type="button" class="btn btn-default toolbutton pay32x32" title="Registrar pago o abono"/>
        </div>
        <div class="toolbuttoncontainer btn-group" style="margin-right: 10px;">
            <input id="btnCheckout" data-action="CHECKOUT" type="button" class="btn btn-default toolbutton checkout32x32" title="CheckOut"/>
            <input id="btnLimpieza" data-action="LIMPIEZA" type="button" class="btn btn-default toolbutton clean32x32" title="Pasar a proceso de limpieza"/>
            <input id="btnMantenimiento" data-action="MANTENIMIENTO"  type="button" class="btn btn-default toolbutton cog32x32" title="Marcar en mantenimiento"/>
            <input id="btnLeberar" data-action="LIBRE" type="button" class="btn btn-default toolbutton green-flag32x32" title="Liberar"/>            
        </div>        
        <div class="toolbuttoncontainer btn-group" style="margin-right: 10px;">
            <input id="btnRefresh" data-action="REFRESHPAGE"  type="button" class="btn btn-default toolbutton refresh32x32" title="Refrescar p&aacute;gina"/>
        </div>

    </div>
<?php
session_start();
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');
if(!$_SESSION){
    $header = header('location: ../');
}

try {
        $q_MyCmd = 
            "
            select * from 
            (
            select 
              habitaciones.piso 
            , habitaciones.habitacion
            , habitaciones.tipo
            , habitaciones.camas
            , t1.estado
            , ifnull(t2.id_registro, 0) id_registro
            from hot_habitaciones habitaciones
            left join
            (
            select mov.habitacion, mov.estado from hot_habitaciones_mov mov
            where mov.fecha_mov in (select max(fecha_mov) from hot_habitaciones_mov where habitacion = mov.habitacion)
            ) t1 on t1.habitacion = habitaciones.habitacion
            left join 
            (
            select registro.habitacion, ifnull(max(registro.id_registro), 0) id_registro from hot_regpas registro 
            where registro.habitacion 
            in 
            (
                select mov_e.habitacion from hot_habitaciones_mov mov_e
                where mov_e.fecha_mov in (select max(fecha_mov) from hot_habitaciones_mov where habitacion = mov_e.habitacion and mov_e.estado = 'OCUPADA') 
            ) group by habitacion            
            ) t2 on t2.habitacion = habitaciones.habitacion
            UNION
            SELECT max(piso) + 1 piso, 99 habitacion, '' tipo, 99 camas, ' ' estado, 0 id_registro FROM hotel.hot_habitaciones    
            ) habitaciones order by habitaciones.piso, habitaciones.habitacion
            ";
        $db = new Db();
        $resultset = $db->ExecQuery($q_MyCmd);

        if($resultset->num_rows > 0) {
            $htmlcode = "";
            $piso = 0;
            while ($row = $resultset->fetch_assoc()) {
                if($piso <> $row['piso'])
                {
                    if ($piso > 0){
                        printf("%s".$htmlcode."%s", "<div class='flex' data-piso='".$piso."'>", "</div>");
                    }                    
                    $piso = $row['piso'];
                    $htmlcode = "";
                }
                $estado = (($row['estado'] == "LIBRE") || (empty($row['estado']) || is_null($row['estado']))) ? 'LIBRE' : $row['estado'];
                $htmlcode .=
                 
                 "<div data-value='".$row['habitacion']."' data-type='".$estado."' title='".$row['estado']."' data-transaction_id='".$row['id_registro']."'>".
                 "<div>".
                 "Habitaci&oacute;n".
                 "<div class='title'>".$row['habitacion']."</div>".
                 "<div class='subtitle'>".$row['tipo']."</div>".
                 "<div class='subtitle'>Camas: ".$row['camas']."</div>".
                 "</div>".
                 "</div>"; 
            }
            $resultset->free();
        }
}catch (Exception $ex) { echo  $ex->getMessage(); }
?>
</div>
<div id="dialog"></div>
<link href="../Css/AdmHabita.css" rel="stylesheet" type="text/css"/>
<link href="../Scripts/contextmenu/css/jquery.contextMenu.css" rel="stylesheet" type="text/css"/>
<link href="../Css/jquery-ui-1.12/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="../Scripts/jquery/jquery.js" type="text/javascript"></script>
<script src="../Scripts/contextmenu/js/jquery.contextMenu.js" type="text/javascript"></script>
<script src="../Scripts/contextmenu/js/jquery.ui.position.js" type="text/javascript"></script>
<script src="../Scripts/jquery/jquery-ui.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        function ctrlkeyHandler(ev){return ev.ctrlkey};
        var diagfp = $('#formasdepago').dialog({
            autoOpen: false,
            height: screen.availHeight * 75 /100,
            width: screen.availWidth * 50 /100,
            modal: true
        });        
        window.global={};
        global.Habitacion = 0;
        global.idtransaction=0;
        
        $('.flex > div:nth-child(n)').click(function(event){
            alert(event.button);
            $('.flex > div:nth-child(n).selected').removeClass('selected');
            global.Habitacion = $(this).data('value');
            global.idtransaction=$(this).data('transaction_id');
            $(this).addClass('selected');
        });
        $('input[class~="toolbutton"]').click(function(){
            if($(this).data('action')==='REFRESHPAGE'){$('.flex > div:nth-child(n).selected').removeClass('selected'); return;}
             var _div = $('div .selected');
             if( _div.length > 0){
                if(confirm("\u00BFConfirma que desea realizar acci\u00F3n sobre habitaci\u00F3n seleccionada?")){
                    switch($(this).data('action')){
                        case 'CHECKIN':
                            var targetlink = 'RegistroPasajeros.php';
                            $('.content').load(targetlink);
                            return false;
                            break;
                        case 'AGREGARPAGO':
                            if(_div.data('type')!=='OCUPADA'){
                                alert('No puede realizar esta acci\u00F3n sobre una habitaci\u00F3n desocupada');
                                break;
                            }
                            $("#ID_REGISTRO").val(_div.data('transaction_id'));
                            $('#formasdepago').load('FormasDePago.php');
                            diagfp.dialog('open');                        
                            break;
                        case 'CAMBIAR': 
                            if(_div.data('type')!=='OCUPADA'){
                                alert('No puede realizar esta acci\u00F3n sobre una habitaci\u00F3n desocupada');
                                break;
                            }
                            break;
                        default:
                            if($(this).data('action')==='CHECKOUT' && _div.data('type')!=='OCUPADA'){
                                alert('No puede realizar esta acci\u00F3n sobre una habitaci\u00F3n desocupada');
                                break;
                            }                            
                            $.ajax({
                               type:'GET',
                               url:'../Controllers/SetEstadoHabitaciones.php?habitacion='+_div.data('value')+'&proceso='+$(this).data('action')+'&idregistro='+_div.data('transaction_id'),
                               datatype:'text',
                               success: function(data){
                                   $('.content').load('Habitaciones.php');
                                   alert(data);
                               }
                            });                               
                            break;
                    }
                }
             }else{
                 alert('Debe seleccionar habitaci\u00F3n');
             }
        });
    });
        
//    $(function(){
//        var diagfp = $('#formasdepago').dialog({
//            autoOpen: false,
//            height: screen.availHeight * 75 /100,
//            width: screen.availWidth * 50 /100,
//            modal: true
//        });        
//        $.contextMenu({
//            selector: '.flex > div:nth-child(n)', 
//            trigger: 'right',
//            delay: 500,
//            itemClickEvent:'click',
//            autoHide: true,
//            callback:function(key, options) {
//                if(key==='AGREGARPAGO'){
//                    $("#ID_REGISTRO").val($(this).data('transaction_id'));
//                    $('#formasdepago').load('FormasDePago.php');
//                    diagfp.dialog('open');
//                    //return false;
//                }else{
//                    $.ajax({
//                       type:'GET',
//                       url:'../Controllers/SetEstadoHabitaciones.php?habitacion='+$(this).data('value')+'&proceso='+key+'&idregistro='+$(this).data('transaction_id'),
//                       datatype:'text',
//                       success: function(data){
//                           $('.content').load('Habitaciones.php');
//                           alert(data);
//                       }
//                    });   
//                }
//            },
//            items: {
//            "LIBRE": {name: "Liberar", icon: "greenflag"},
//            "LIMPIEZA": {name: "En proceso de aseo", icon: "clean"},
//            "MANTENIMIENTO": {name: "Mantenimiento", icon: "cog"},
//            "AGREGARPAGO": {name: "Agregar Pago", icon: "pay"},
//            "CHECKOUT": {name: "CheckOut", icon: "checkout"}
//        }
//        });
//});
    
</script>
<input type="hidden" id="ID_REGISTRO" name="ID_REGISTRO" value="0"/>
<div id="formasdepago"></div>
