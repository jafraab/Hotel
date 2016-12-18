<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('../Services/Db.php');
session_start();
if(($_SERVER["REQUEST_METHOD"] === "GET")){
    $accion = filter_input(INPUT_GET, 'ACCION');
    if($accion === 'QRY_Pagos'){
      $dataresult = ConsultaPagos();
      if($dataresult){
        echo json_encode(mysqli_fetch_array($dataresult));
      }
      else{
        echo Mensaje("La consulta no dio resultados");
      }
    }
}
else{echo Mensaje("No existen datos que enviar.");}
function ConsultaPagos(){
    $q_MyCmd =
    "           
    select * from
    (
    select * from
    (
    SELECT 
    registro.id_registro
    , DATE_FORMAT(STR_TO_DATE(registro.hora_ingreso, '%d/%m/%Y %H:%i:%S'), '%d/%m/%Y %H:%i:%S') fecha
    , registro.id_cliente 
    , registro.habitacion
    , registro.valor_pp
    , registro.dias_estadia
    , (registro.valor_pp * registro.dias_estadia) total_adeudado
    , 0 abonos
    from hotel.hot_regpas registro
    union
    SELECT 
    pagos.id_registro
    , DATE_FORMAT(DATE(pagos.fecha_registro), '%d/%m/%Y %H:%i:%S') fecha
    , ' ' id_cliente 
    , 0 habitacion
    , 0 valor_pp
    , 0 dias_estadia
    , 0 total_adeudado
    , monto_cancelado abonos
    from hotel.hot_formas_de_pago pagos
    ) contabilidad
     order by contabilidad.id_registro, contabilidad.fecha
    ";
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}