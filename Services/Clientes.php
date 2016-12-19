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
    public function SelClientesActivos(){
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
            
}