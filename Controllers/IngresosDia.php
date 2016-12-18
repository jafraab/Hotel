<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
session_start();
require_once ('../Services/AppApi.php');  
require_once ('../Services/Db.php');  

    if(($_SERVER["REQUEST_METHOD"] === "POST")){
        $fechaIngreso = $_POST['FECHA_INGRESO'];
        $q_MyCmd = 
        "
        SELECT PASAJEROS.*, 
        CLIENTES.NOMBRE_CLIENTE CLIENTE,
        (dias_estadia * valor_pp) TOTAL
        FROM hot_regpas PASAJEROS 
        INNER JOIN hot_clientes CLIENTES ON CLIENTES.ID_CLIENTE = PASAJEROS.ID_CLIENTE
        WHERE DATE_FORMAT(STR_TO_DATE(PASAJEROS.hora_ingreso, '%d/%m/%Y'), '%d/%m/%Y') = '".$fechaIngreso."'";
        $db = new Db();
        $resultset = $db->ExecQuery($q_MyCmd);
        if($resultset->num_rows > 0){
            $rows = array();
            while($r = mysqli_fetch_array($resultset)) {
              $rows[] = $r;
            }
            echo json_encode($rows);
        }
        else{echo Mensaje("No se encontró registros para la fecha indicada.");}
    }
    else{echo Mensaje("No se ha hecho post");}

