<?php

/*
Proyecto: Hotel 
Archivo: CambioHabitacion.php
Autor: Jaime Altamirano Bustamante
Fecha: 26-dic-2016 20:30:52
*/
session_start();
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');
if(!$_SESSION){
    $header = header('location: ../');
}
function GetLibres(){
    try{
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
        ) habitaciones where habitaciones.estado = 'LIBRE' 
        or habitaciones.habitacion not in (select habitacion from hot_habitaciones_mov)
        order by habitaciones.piso, habitaciones.habitacion              
    ";
        $db = new Db();
        $resultset = $db->ExecQuery($q_MyCmd);
        if($resultset->num_rows > 0) {
            $html = "<select name='HABITACION_DESTINO' class='form-control'>";
            $html .= "<option value='0'>..:: Seleccione habitaci&oacute;n</option>";
            while ($row = $resultset->fetch_assoc()) {
                $html .= "<option value='".$row["habitacion"]."'>".$row["habitacion"]."</option>";
            }
            $html .= "</select>";
            echo $html;
        }
} catch (Exception $ex) { echo  $ex->getMessage(); }
}

?>
<div class="container">
    <form rol="form" id="frmCambioHabitacion" method="POST">
        <div class="form-group">
            <div class="row">
                <label class='control-label  col-sm-2' id="HABITACIONORIGEN">Habitaci&oacute;n actual</label>
                <div class='col-lg-3'>
                    <input type="text" id="HABITACION_ORIGEN" name="HABITACION_ORIGEN" readonly="" class="form-control"/>
                </div>
            </div>
            <br>
            <div class="row">
                <label class='control-label  col-sm-2' id="HABITACIONDESTINO">Habitacion de destino</label>
                <div class='col-lg-3'>
<!--                    <input type="text" id="HABITACION_DESTINO" name="HABITACION_DESTINO" class="form-control"/>-->
                    <?php
                    GetLibres();
                    ?>
                </div>
            </div>  
            <br>
            <div class="row">
                <label class='control-label  col-sm-2'>Observaciones</label>
                <div class='col-lg-3'>
                    <input type="text" id="OBSERVACIONES" name="OBSERVACIONES" class="form-control"/>
                </div>
            </div>   
            <br>
            <div class="row">
                <div class='col-lg-8' id="divSaveCP">
                    <input type="button" id="btnSaveCP" class="btn btn-success" value="Guardar"/>
                </div>
            </div>                        
        </div>
    </form>
</div>
<script>
//    $(document).ready(function(){
//        
//    })
    
    $(function(){
        $('#HABITACION_ORIGEN').val(global.Habitacion);
    }); 
</script>


