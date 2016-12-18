<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('../Services/Db.php');
session_start();

$habitacion = filter_input(INPUT_GET, 'habitacion');
$proceso    = filter_input(INPUT_GET, 'proceso'); 
try{
$My_cmd = "INSERT INTO hotel.hot_habitaciones_mov(habitacion, estado) VALUES(".$habitacion.",'".$proceso."')";
$db = new Db();
$cmd_result = $db->ExecProc($My_cmd);
if($cmd_result){
    echo "Regitro agregado con exito";
}
else{
    echo "No se pudo agregar el registro";
}
} catch (Exception $ex){
    echo $ex->getMessage();
}