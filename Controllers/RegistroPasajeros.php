<?php
/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');
require_once ('../Services/Clientes.php');

if(($_SERVER["REQUEST_METHOD"] === "POST")){
    $accion = filter_input(INPUT_POST, 'ACCION');
    if($accion === 'ADD'){
        AddNew();
    }
}
else{echo Mensaje("No existen datos que enviar.");}
function AddNew(){
    try {
        $i_MyCmd = "call sp_hot_regpas_ins('".filter_input(INPUT_POST, 'ID_CLIENTE').
                "',".filter_input(INPUT_POST, 'DIAS_ESTADIA').
                ",'".filter_input(INPUT_POST, 'HORA_INGRESO').
                "','"."".
                "',".filter_input(INPUT_POST, 'HABITACION').
                ",".filter_input(INPUT_POST, 'VALOR').                
                ",@p_id_registro".
                ",'".filter_input(INPUT_POST, 'PATENTE')."')";
        $db = new Db();
        $mycmdresult = $db->ExecProcParam($i_MyCmd, '@p_id_registro');
        if(!$mycmdresult){ $msg =  "Error: El registro no pudo ser guardado"; }
        else{
            $updestadosqry = "call sp_set_estado_habitaciones(".filter_input(INPUT_POST, 'HABITACION').", 'OCUPADA', ".$mycmdresult.")";
            $updestados = $db->ExecProc($updestadosqry);
            
            $cliente = new Clientes();
            $cliente->AddClient();
        }
        $msg =  'Registro guardado correctamente,'.$mycmdresult ;
    } catch (Exception $ex) { $msg =  'Error: '.$ex->getMessage(); }
    echo $msg;
}