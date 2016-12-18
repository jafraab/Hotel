<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */

require_once ('AppApi.php');
require_once ('Clientes.php');
$idcliente = $_GET['IDCLIENTE'];
$clientes = new Clientes();
$dataresult = $clientes->SelById($idcliente);

if($dataresult){
    echo json_encode(mysqli_fetch_array($dataresult));
}
else{
    echo Mensaje("La consulta no dio resultados");
}