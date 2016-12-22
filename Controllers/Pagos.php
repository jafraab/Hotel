<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('../Services/Db.php');
session_start();
if(($_SERVER["REQUEST_METHOD"] === "GET")){
    $accion = filter_input(INPUT_GET, 'ACCION');
    if($accion === 'QRY_Pagos'){
      $dataresult = ConsultaPagos(filter_input(INPUT_GET, 'idcliente'));
      if($dataresult->num_rows > 0){
        //echo json_encode(mysqli_fetch_array($dataresult));
          $tbdataview ="";
          $tbdataview .=
          "<table class='table table-bordered' data-type='dataview' id='dataresult'>".
          "<thead style='background-color:#DAE1E5;'>".
          "<th>Ingreso</th>".
          "<th>Fecha</th>".
          "<th>Id Cliente</th>".
          "<th>Cliente</th>".
          "<th>Habitaci&oacute;n</th>".
          "<th>D&iacute;as Estad&iacute;a</th>".
          "<th>Valor</th>".
          "<th>Valor Total</th>".
          "<th>Abonos</th>".
          "</tr>".
          "</thead>".
          "<tbody>";     

          while ($row = $dataresult->fetch_assoc()) {
              $tbdataview .=
                    "<tr>".
                    "<td>".$row["id_registro"]."</td>".
                    "<td>".$row["fecha"]."</td>".
                    "<td>".$row["id_cliente"]."</td>".
                    "<td>".$row["cliente"]."</td>".  
                    "<td>".$row["habitacion"]."</td>".
                    "<td>".$row["dias_estadia"]."</td>".
                    "<td>".$row["valor_pp"]."</td>".
                    "<td>".$row["total_adeudado"]."</td>".
                    "<td>".$row["abonos"]."</td>".
                    "</tr>";                    
          }
          $tbdataview .="</tbody>";
          $tbdataview .="</table>";
          echo $tbdataview;
      }
      else{
        echo Mensaje("La consulta no dio resultados");
      }
    }
}
else{echo Mensaje("No existen datos que enviar.");}
function ConsultaPagos($idcliente){
    $q_MyCmd =
    "        
    select * from
    (
        SELECT 
        registro.id_registro
        , DATE_FORMAT(STR_TO_DATE(registro.hora_ingreso, '%d/%m/%Y %H:%i:%S'), '%d/%m/%Y %H:%i:%S') fecha
        , 0 rownum
        , registro.id_cliente
        , clientes.nombre_cliente cliente
        , registro.habitacion
        , registro.valor_pp
        , registro.dias_estadia
        , (registro.valor_pp * registro.dias_estadia) total_adeudado
        , 0 abonos
        from hotel.hot_regpas registro
        inner join hotel.hot_clientes clientes on clientes.id_cliente = registro.id_cliente
        union
        SELECT 
        pagos.id_registro
        , DATE_FORMAT(pagos.fecha_registro, '%d/%m/%Y %H:%i:%S') fecha
        , @rownum:=@rownum + 1 AS rownum
        , hot_regpas.id_cliente 
        , ' ' cliente
        , 0 habitacion
        , 0 valor_pp
        , 0 dias_estadia
        , 0 total_adeudado
        , monto_cancelado abonos
        from hotel.hot_formas_de_pago pagos
        inner join hot_regpas on hot_regpas.id_registro = pagos.id_registro
    ) t1, (SELECT @rownum := 0) r
    order by t1.id_registro asc, t1.fecha asc, t1.rownum asc";
//    $q_MyCmd += " where id_cliente = ".$idcliente;
//    $q_MyCmd += " order by t1.id_registro asc, t1.fecha asc, t1.rownum asc";
    
    $db = new Db();
    $resultset = $db->ExecQuery($q_MyCmd);
    return $resultset;
}