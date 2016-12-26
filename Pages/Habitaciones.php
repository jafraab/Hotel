<div class="Hotel">
    <div class="toolbar">
        <div class="toolbuttoncontainer btn-group">
            <input type="button" class="btn btn-default toolbutton green-flag32x32"/>
            <input type="button" class="btn btn-default toolbutton clean32x32"/>
            <input type="button" class="btn btn-default toolbutton cog32x32"/>
            <input type="button" class="btn btn-default toolbutton pay32x32"/>
            <input type="button" class="btn btn-default toolbutton checkout32x32"/>
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
        window.global={};
        global.Habitacion = 0;
        global.idtransaction=0;
        $('.flex > div:nth-child(n)').click(function(){
            $('.flex > div:nth-child(n)').removeClass('selected');
            global.Habitacion = $(this).data('value');
            global.idtransaction=$(this).data('transaction_id');
            $(this).addClass('selected');
        });
        /*
        $('div[data-type="LIBRE"]').on('click', function(){
            global.Habitacion = $(this).data('value');
            var targetlink = 'RegistroPasajeros.php';
            $('.content').load(targetlink);
            return false;
        });   
        $('div[data-type="OCUPADA"]').on('click', function(){
                global.idtransaction = $(this).data('transaction_id');
                var targetlink = 'RegistroPasajeros.php';
                $('.content').load(targetlink);
                return false;
        });*/
    });
        
    $(function(){
        var diagfp = $('#formasdepago').dialog({
            autoOpen: false,
            height: screen.availHeight * 75 /100,
            width: screen.availWidth * 50 /100,
            modal: true
        });        
        $.contextMenu({
            selector: '.flex > div:nth-child(n)', 
            trigger: 'right',
            delay: 500,
            itemClickEvent:'click',
            autoHide: true,
            callback:function(key, options) {
                if(key==='AGREGARPAGO'){
                    $("#ID_REGISTRO").val($(this).data('transaction_id'));
                    $('#formasdepago').load('FormasDePago.php');
                    diagfp.dialog('open');
                    //return false;
                }else{
                    $.ajax({
                       type:'GET',
                       url:'../Controllers/SetEstadoHabitaciones.php?habitacion='+$(this).data('value')+'&proceso='+key+'&idregistro='+$(this).data('transaction_id'),
                       datatype:'text',
                       success: function(data){
                           $('.content').load('Habitaciones.php');
                           alert(data);
                       }
                    });   
                }
            },
            items: {
            "LIBRE": {name: "Liberar", icon: "greenflag"},
            "LIMPIEZA": {name: "En proceso de aseo", icon: "clean"},
            "MANTENIMIENTO": {name: "Mantenimiento", icon: "cog"},
            "AGREGARPAGO": {name: "Agregar Pago", icon: "pay"},
            "CHECKOUT": {name: "CheckOut", icon: "checkout"}
        }
        });
});
    
</script>
<input type="hidden" id="ID_REGISTRO" name="ID_REGISTRO" value="0"/>
<div id="formasdepago"></div>
