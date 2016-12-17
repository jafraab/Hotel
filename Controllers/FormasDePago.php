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
    $q_MyCmd =
    "           
    SELECT 
    hotel.hot_regpas.id_registro
    , ifnull(hotel.hot_formas_de_pago.tipo_pago, 1) tipo_pago
    , ifnull(hotel.hot_formas_de_pago.modalidad_pago, 0) modalidad_pago
    , ifnull(hotel.hot_formas_de_pago.voucher, 0) voucher
    , ifnull(hotel.hot_formas_de_pago.tipo_documento, 0) tipo_documento
    , ifnull(hotel.hot_formas_de_pago.nro_documento_referencia, 0) nro_documento_referencia
    , hotel.hot_regpas.dias_estadia * valor_pp monto_adeudado 
    , ifnull(pagos.monto_cancelado, 0) monto_cancelado
    FROM hotel.hot_regpas
    left join hot_formas_de_pago on hot_regpas.id_registro = hot_formas_de_pago.id_registro 
    left join 
    (
        select abonos.id_registro, ifnull(sum(abonos.monto_cancelado), 0) monto_cancelado from hotel.hot_formas_de_pago abonos group by abonos.id_registro
    ) pagos on pagos.id_registro = hotel.hot_formas_de_pago.id_registro
    where hotel.hot_regpas.id_registro = ".$p_idregistro."
    group by
    hotel.hot_formas_de_pago.id_registro
    , hotel.hot_formas_de_pago.tipo_pago
    , hotel.hot_formas_de_pago.modalidad_pago
    , hotel.hot_formas_de_pago.voucher
    , hotel.hot_formas_de_pago.tipo_documento
    , hotel.hot_formas_de_pago.nro_documento_referencia
    , pagos.monto_cancelado
    ";
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}