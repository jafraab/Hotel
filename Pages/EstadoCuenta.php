<?php

/*
Proyecto: Hotel 
Archivo: EstadoCuenta.php
Autor: Jaime Altamirano Bustamante
Fecha: 18-ene-2017 20:24:13
*/
require_once ('../Services/Db.php');
session_start();
if(($_SERVER["REQUEST_METHOD"] === "GET")){
    $idregistro = filter_input(INPUT_GET, 'ID_REGISTRO');
    $dataresult = EstadoCtaHabitacion($idregistro);
    if($dataresult->num_rows > 0){
        $fila = $dataresult->fetch_row();

        $tbdataview ="";
        $tbdataview .=       
        "<table class='table table-bordered' data-type='dataview' id='dataresult'>".
        "<thead style='background-color:#DAE1E5;'>".
        "<tr style='background-color: whitesmoke;'><th>Cliente</th><th colspan='5'>".$fila[4]."</th></tr>".
        "<tr style='background-color: whitesmoke;'><th>Habitaci&oacute;n</th><th colspan='5'>".$fila[5]."</th></tr>".                
        "<tr>".
        "<th>Ingreso</th>".
        "<th>Fecha</th>".
        "<th>D&iacute;as Estad&iacute;a</th>".
        "<th>Valor</th>".
        "<th>Valor Total</th>".
        "<th>Abonos</th>".
        "</tr>".
        "</thead>".
        "<tbody>";                     
        mysqli_data_seek($dataresult, 0);
        while ($rd = $dataresult->fetch_assoc()) {
            $tbdataview .=
                  "<tr>".
                  "<td>".$rd["id_registro"]."</td>".
                  "<td>".$rd["fecha"]."</td>".
                  "<td>".$rd["dias_estadia"]."</td>".
                  "<td>".$rd["valor_pp"]."</td>".
                  "<td>".$rd["total_adeudado"]."</td>".
                  "<td>".$rd["abonos"]."</td>".
                  "</tr>";                    
        }
        $tbdataview .="</tbody>";
        $tbdataview .="</table>";
        $dataresult->free();
        echo $tbdataview;
    }
}
function EstadoCtaHabitacion($idregistro){
    $q_MyCmd =
    "        
    select * from
    (
        SELECT 
        registro.id_registro
        , registro.fecha_registro
        , DATE_FORMAT(STR_TO_DATE(registro.hora_ingreso, '%d/%m/%Y %H:%i:%S'), '%d/%m/%Y %H:%i:%S') fecha
        , registro.id_cliente
        , clientes.nombre_cliente cliente
        , registro.habitacion
        , registro.valor_pp
        , registro.dias_estadia
        , (registro.valor_pp * registro.dias_estadia) total_adeudado
        , 0 abonos
        from hotel.hot_regpas registro
        inner join hotel.hot_clientes clientes on clientes.id_cliente = registro.id_cliente
        where registro.id_registro = ".$idregistro."
        union
        SELECT 
        pagos.id_registro
        ,pagos.fecha_registro
        , DATE_FORMAT(pagos.fecha_registro, '%d/%m/%Y %H:%i:%S') fecha
        , hot_regpas.id_cliente 
        , ' ' cliente
        , hot_regpas.habitacion
        , 0 valor_pp
        , 0 dias_estadia
        , 0 total_adeudado
        , monto_cancelado abonos
        from hotel.hot_formas_de_pago pagos
        inner join hot_regpas on hot_regpas.id_registro = pagos.id_registro
        where pagos.id_registro = ".$idregistro."
    ) t1
    order by t1.id_registro asc, t1.fecha_registro asc";
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}


