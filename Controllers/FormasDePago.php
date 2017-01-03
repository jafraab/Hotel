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
      mysqli_free_result($dataresult);
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
	select 
    t1.*
    ,ifnull(t2.deuda, 0) monto_adeudado
    ,ifnull(t2.abonos, 0) monto_cancelado
    ,ifnull(t2.saldo, 0) saldo
    from
    (
    SELECT 
    regpas.id_registro
    , ifnull(forma_pago.tipo_pago, 1) tipo_pago
    , ifnull(forma_pago.modalidad_pago, 0) modalidad_pago
    , ifnull(forma_pago.voucher, 0) voucher
    , ifnull(forma_pago.tipo_documento, 0) tipo_documento
    , ifnull(forma_pago.nro_documento_referencia, 0) nro_documento_referencia
    FROM hotel.hot_regpas regpas
    left join hot_formas_de_pago forma_pago on regpas.id_registro = forma_pago.id_registro
    where regpas.id_registro = ".$p_idregistro."
	) t1 left join
    (
    select regpas.id_registro, (@deuda := regpas.valor_pp*regpas.dias_estadia) deuda, (@abonos := ifnull(sum(pagos.monto_cancelado), 0)) abonos , 
    cast((@deuda-@abonos) as signed) saldo
    from hot_formas_de_pago pagos
    inner join hot_regpas regpas on regpas.id_registro = pagos.id_registro
    where pagos.id_registro = regpas.id_registro and regpas.id_registro = ".$p_idregistro."
    ) t2 on t2.id_registro = t1.id_registro
    ";
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}