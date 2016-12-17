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
            from hot_habitaciones habitaciones
            left join
            (
            select mov.habitacion, mov.estado from hot_habitaciones_mov mov
            where mov.fecha_mov in (select max(fecha_mov) from hot_habitaciones_mov where habitacion = mov.habitacion)
            ) t1 on t1.habitacion = habitaciones.habitacion
            UNION
            SELECT max(piso) + 1 piso, 99 habitacion, '' tipo, 99 camas, ' ' estado FROM hotel.hot_habitaciones    
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
                 "<div data-value='".$row['habitacion']."' data-type='".$estado."' title='".$row['estado']."'>".
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
        $('div[data-type="LIBRE"]').on('click', function(){
            window.sharedVariable = $(this).data('value');
            var targetlink = 'RegistroPasajeros.php';
            $('.content').load(targetlink);
            return false;
        });  
    });
    $(function(){
        $.contextMenu({
            selector: '.flex > div:nth-child(n)', 
            trigger: 'right',
            delay: 500,
            callback: function(key) {
                $.ajax({
                   type:'GET',
                   url:'../Controllers/SetEstadoHabitaciones.php?habitacion='+$(this).data('value')+'&proceso='+key,
                   datatype:'text',
                   success: function(data){
                       $('.content').load('Habitaciones.php');
                       alert(data);
                   }
                });
            },
            items: {
            "LIBRE": {name: "Liberar", icon: "greenflag"},
            "LIMPIEZA": {name: "En proceso de aseo", icon: "clean"},
            "MANTENIMIENTO": {name: "Mantenimiento", icon: "cog"},
            "CHECKOUT": {name: "CheckOut", icon: "checkout"}
        }
        });
});
    
</script>