<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');
if(($_SERVER["REQUEST_METHOD"] === "GET")){
    $query = filter_input(INPUT_GET, 'QUERY');
    if($query === 'CLIENTESACTIVOS'){
      $dataresult = SelClientesActivos();
      if($dataresult->num_rows > 0){
        //echo json_encode(mysqli_fetch_array($dataresult));
        $rawdata = array();
        $i=0;
        while($row = mysqli_fetch_array($dataresult))
        {
            $rawdata[$i] = $row;
            $i++;
        }
        echo json_encode($rawdata);
      }
      else{
        echo json_encode('mensaje: "La consulta no dio resultados"');
      }
    }
}
else{echo Mensaje("No existen datos que enviar.");}

function SelClientesActivos(){
    $q_MyCmd = 
    "
    select mov_e.id_registro, mov_e.habitacion, hot_regpas.id_cliente, clientes.nombre_cliente from hot_habitaciones_mov mov_e
    inner join hot_regpas on hot_regpas.id_registro = mov_e.id_registro
    inner join hot_clientes clientes on clientes.id_cliente = hot_regpas.id_cliente
    where mov_e.fecha_mov in (select max(fecha_mov) from hot_habitaciones_mov where hot_habitaciones_mov.habitacion = mov_e.habitacion and mov_e.estado = 'OCUPADA')
    ";
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}
