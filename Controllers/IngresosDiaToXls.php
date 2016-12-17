<?php

/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
session_start();
require_once ('../Services/AppApi.php');  
require_once ('../Services/Db.php'); 
require_once('../Classes/PHPExcel.php');

    if(($_SERVER["REQUEST_METHOD"] === "POST")){
        try {
            $fechaIngreso = $_POST['FECHA_INGRESO'];
            $q_MyCmd = 
            "
            SELECT PASAJEROS.*,
            DATE_FORMAT(DATE_ADD(STR_TO_DATE(PASAJEROS.hora_ingreso, '%d/%m/%Y'), INTERVAL PASAJEROS.dias_estadia DAY), '%d/%m/%Y') SALIDA,
            CLIENTES.NOMBRE_CLIENTE CLIENTE,
            (dias_estadia * valor_pp) TOTAL
            FROM hot_regpas PASAJEROS 
            INNER JOIN hot_clientes CLIENTES ON CLIENTES.ID_CLIENTE = PASAJEROS.ID_CLIENTE
            WHERE DATE_FORMAT(STR_TO_DATE(PASAJEROS.hora_ingreso, '%d/%m/%Y'), '%d/%m/%Y') = '".$fechaIngreso."'";
            $db = new Db();
            $resultset = $db->ExecQuery($q_MyCmd);
            if($resultset->num_rows > 0){
                $rows = array();
                $oXls = new PHPExcel();
                $oXls->getProperties()->setCreator("Jaime Altamirano B - Millahue")
                                      ->setTitle("Ingresos Diarios");
                $oXls->setActiveSheetIndex(0);
                //setActiveSheetIndex(0)->mergeCells('A1:C1');
                $oXls->getActiveSheet()->SetCellValue('A1', 'Ingresos Diarios');
                $oXls->setActiveSheetIndex(0)->mergeCells('A1:B1')->getStyle("A1")->getFont()->setBold(true)->setSize(20);
                $oXls->getActiveSheet()->SetCellValue('C1', date("d/m/Y H:i"))->getStyle("C1")->getFont()->setBold(true)->setSize(20);
                
                $oXls->getActiveSheet()->SetCellValue('A2', 'Cliente');
                $oXls->getActiveSheet()->SetCellValue('B2', 'Check In');
                $oXls->getActiveSheet()->SetCellValue('C2', 'Habitacion');
                $oXls->getActiveSheet()->SetCellValue('D2', 'Dias Estadia');
                $oXls->getActiveSheet()->SetCellValue('E2', 'Valor');
                $oXls->getActiveSheet()->SetCellValue('F2', 'Valor Total');
                $i=3;
                $firstrow = $i;
                while($r = mysqli_fetch_array($resultset)) {
                  $oXls->getActiveSheet()->SetCellValue('A'.$i, $r["CLIENTE"]);
                  $oXls->getActiveSheet()->SetCellValue('B'.$i, $r["hora_ingreso"]);
                  $oXls->getActiveSheet()->SetCellValue('C'.$i, $r["habitacion"]);
                  $oXls->getActiveSheet()->SetCellValue('D'.$i, $r["dias_estadia"]);
                  $oXls->getActiveSheet()->SetCellValue('E'.$i, $r["valor_pp"]);
                  $oXls->getActiveSheet()->SetCellValue('F'.$i, $r["TOTAL"]);
                  $i++;
                }
                $lastrow = $i + 1;
                $oXls->getActiveSheet()->SetCellValue('F'.$lastrow, '=SUM(F'.$firstrow.':F'.$i.')');
                foreach(range('A','F') as $columnID)
                {
                    $oXls->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                }
                $oXlsWriter = new PHPExcel_Writer_Excel2007($oXls);
                if(!file_exists('../Temp'))
                {
                	$oldmask = umask(0);
                	mkdir('../Temp', 0744);
                }
                $outPath = '../Temp/Ingresos'.date("dmYHis").'.xlsx';
                $oXlsWriter->save($outPath);
                echo $outPath;
            }
            else{echo "No se encontró registros para la fecha indicada.";}
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    else{echo "No se ha hecho post";}

