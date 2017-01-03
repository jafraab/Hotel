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
        , estado_h.estado
        , estado_h.id_registro
        from hot_habitaciones habitaciones
        left join hot_estado_habitaciones estado_h on estado_h.habitacion = habitaciones.habitacion
        where estado_h.estado = 'LIBRE' or estado_h.estado is null
        ) habitaciones order by habitaciones.piso, habitaciones.habitacion  
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
        mysqli_free_result($resultset);
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


