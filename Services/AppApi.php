<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */

function Mensaje($mensaje){
    $jsondata = array();
    $jsondata['mensaje'] = $mensaje;
    header('Content-type: application/json; charset=utf-8');
    return json_encode($jsondata);
}