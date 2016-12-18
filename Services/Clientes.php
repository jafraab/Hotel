<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
session_start();
require_once ('AppApi.php');
require_once ('Db.php');
class Clientes{
    public function AddClient(){
        try {
            $q_MyCmd = "call hotel.sp_hot_clientes_ins('".filter_input(INPUT_POST, 'ID_CLIENTE').
                    "', '".filter_input(INPUT_POST, 'NOMBRE').
                    "', '".filter_input(INPUT_POST, 'NACIONALIDAD').
                    "', '".filter_input(INPUT_POST, 'CIUDAD').
                    "', '".filter_input(INPUT_POST, 'DIRECCION').
                    "', '".filter_input(INPUT_POST, 'TELEFONO')."')";
            $db = new Db();
            $mycmdresult = $db->ExecQuery($q_MyCmd);
            if(!$mycmdresult)
            {
               $msge = "Cliente nuevo agregado"; 
            }
        } catch (Exception $ex) {
            $msge =  $ex->getMessage();
        }
        //return $msge;
    }
    public function SelById($p_idcliente){
        $q_MyCmd = "SELECT * FROM HOT_CLIENTES WHERE ID_CLIENTE = '".$p_idcliente."'";
        $db = new Db();
        $resultset = $db->ExecQuery($q_MyCmd);
        return $resultset;
    }
}