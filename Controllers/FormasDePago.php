<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */

//call hotel.sp_hot_formas_de_pago(2016326001, 1, 1, 0, 1, 1234, 35000);
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');

if(($_SERVER["REQUEST_METHOD"] === "GET")){
    $accion = filter_input(INPUT_GET, 'ACCION');
    if($accion === 'ADD'){
      AddNew();
    }
    if($accion === 'QRY'){
      $dataresult = SelById(filter_input(INPUT_GET, 'ID_REGISTRO'));
      if($dataresult){
        echo json_encode(mysqli_fetch_array($dataresult));
      }
      else{
        echo Mensaje("La consulta no dio resultados");
      }
    }
}
else{echo Mensaje("No existen datos que enviar.");}
function AddNew(){
    try {
        $i_MyCmd = 
                "call hotel.sp_hot_formas_de_pago("
                .filter_input(INPUT_GET, 'ID_REGISTRO').
                ",".filter_input(INPUT_GET, 'TIPO_PAGO').
                ",".filter_input(INPUT_GET, 'MODALIDAD_PAGO').
                ",".filter_input(INPUT_GET, 'VOUCHER').
                ",".filter_input(INPUT_GET, 'TIPO_DOCUMENTO').
                ",".filter_input(INPUT_GET, 'NRO_DOCUMENTO_REFERENCIA').
                ",".filter_input(INPUT_GET, 'MONTO_CANCELADO').
                ")";
        //echo $i_MyCmd;
        $db = new Db();
        $mycmdresult = $db->ExecProc($i_MyCmd);
        if(!$mycmdresult){ 
            $msg =  "Error: El registro no pudo ser guardado"; 
        }
    $msg =  'Registro guardado correctamente, ';
    } catch (Exception $ex) { $msg =  'Error: '.$ex->getMessage(); }
    echo $msg;
}
function SelById($p_idregistro){
    $q_MyCmd = "SELECT hotel.hot_formas_de_pago.*, hot_regpas.dias_estadia * valor_pp adeudado FROM hotel.hot_formas_de_pago inner join hot_regpas on hot_regpas.id_registro = hot_formas_de_pago.id_registro where hotel.hot_formas_de_pago.id_registro = " .$p_idregistro;
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}