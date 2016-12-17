<?php
/* 
 * Desarrollado por : Jaime Francisco Altamirano Bustamante.
 * Noviembre 2016
 */
session_start();
require_once ('../Services/AppApi.php');
require_once ('../Services/Db.php');
require_once('../Classes/fpdf.php');

//if(($_SERVER["REQUEST_METHOD"] === "POST")){
    try {
        $q_MyCmd = 
        "            
        SELECT PASAJEROS.*, 
        DATE_FORMAT(DATE_ADD(STR_TO_DATE(PASAJEROS.hora_ingreso, '%d/%m/%Y'), INTERVAL PASAJEROS.dias_estadia DAY), '%d/%m/%Y') SALIDA,
        CLIENTES.NOMBRE_CLIENTE CLIENTE,
        CLIENTES.ID_CLIENTE,
        CLIENTES.DOMICILIO,
        CLIENTES.PROCEDENCIA,
        CLIENTES.NACIONALIDAD,
        CLIENTES.FONO,
        (dias_estadia * valor_pp) TOTAL
        FROM hot_regpas PASAJEROS 
        INNER JOIN hot_clientes CLIENTES ON CLIENTES.ID_CLIENTE = PASAJEROS.ID_CLIENTE
        WHERE PASAJEROS.id_registro  IN (select IFNULL(max(hot_regpas.id_registro), 0) id_registro from hot_regpas)";
        $db = new Db();
        $resultset = $db->ExecQuery($q_MyCmd);
        if($resultset->num_rows > 0) {
            $r = mysqli_fetch_array($resultset);
            $doc = new FPDF('P', 'mm', 'Letter');
            $doc->AddPage();
            $doc->SetFont('Times','B',20);
            
            $y_axis_initial = 30;
            $x_axis_initial = 10;
            $defaultcellwidth = 30;
           
            // Título del documento
            $doc->Image('../Images/Logo_Millahue.jpg',10,10,-300);
            $doc->SetX(70);
            //$this->Cell( $defaultcellwidth, 10, $doc->Image('../Images/Logo_Millahue.jpg', $doc->GetX(), $doc->GetY(), -300), 0, 0, 'L', false );
            $title = 'Comprobante de Registro';
            $doc->Cell(0, 10, $title, 0, 0, 'L');
            
            $doc->Line($x_axis_initial, $y_axis_initial, $doc->GetPageWidth()-10, $y_axis_initial);
            
            $doc->SetFont('Times', 'B', 13);
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Antecedentes del Pasajero', 0, 0, 'L');
            
//            $doc->Line($x_axis_initial, $y_axis_initial, $doc->GetPageWidth()-10, $y_axis_initial);
            
            $y_axis_initial += 7;
            $doc->SetY($y_axis_initial);
            $doc->SetFont('Times', '', 12);
            
            $doc->Cell($defaultcellwidth, 10, 'Pasajero', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["CLIENTE"]), 10, ucfirst(strtoupper($r["CLIENTE"])), 0, 0, 'L');
            
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Id Cliente', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["ID_CLIENTE"]), 10, ucfirst(strtolower($r["ID_CLIENTE"])), 0, 0, 'L');

            
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Nacionalidad', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["NACIONALIDAD"]), 10, ucfirst(strtolower($r["NACIONALIDAD"])), 0, 0, 'L');
            
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Procedencia', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["PROCEDENCIA"]), 10, ucfirst(strtolower($r["PROCEDENCIA"])), 0, 0, 'L');
            
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Domicilio', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["DOMICILIO"]), 10, ucfirst(strtolower($r["DOMICILIO"])), 0, 0, 'L');

            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Teléfono', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["FONO"]), 10, ucfirst(strtolower($r["FONO"])), 0, 0, 'L');
            
            $y_axis_initial += 5;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Datos del vehículo', 0, 0, 'L');
            $doc->SetX(70);
            $doc->Cell($doc->GetStringWidth($r["patente"]), 10, ucfirst(strtolower($r["patente"])), 0, 0, 'L');
            
            $doc->SetFont('Times', 'B', 13);
            $y_axis_initial += 7;
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Antecedentes de la Estadía', 0, 0, 'L');
            
            $y_axis_initial += 7;           
            $doc->SetFont('Times', '', 12);
            // Antecedentes de la estadía
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Check In', 0, 0, 'C');
            
            $doc->SetX(70);
            $doc->Cell($defaultcellwidth, 10, 'Días de Estadía', 0, 0, 'C');
            
            $doc->SetX(70 + $defaultcellwidth);
            $doc->Cell($defaultcellwidth, 10, 'Check Out', 0, 0, 'C');

            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["hora_ingreso"])), 0, 0, 'C');
            
            $doc->SetX(70 + $defaultcellwidth);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["SALIDA"])), 0, 0, 'C');
            
            $doc->SetX(70);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["dias_estadia"])), 0, 0, 'C');
            //
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, 'Habitación', 0, 0, 'C');
            
            $doc->SetX(70);
            $doc->Cell($defaultcellwidth, 10, 'Valor Diario', 0, 0, 'C');
                        
            $doc->SetX(70 + $defaultcellwidth);
            $doc->Cell($defaultcellwidth, 10, 'Total', 0, 0, 'C');
            
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["habitacion"])), 0, 0, 'C');
            
            $doc->SetX(70);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["valor_pp"])), 0, 0, 'C');
            
            $doc->SetX(70 + $defaultcellwidth);
            $doc->Cell($defaultcellwidth, 10, ucfirst(strtolower($r["TOTAL"])), 0, 0, 'C');
            
            $y_axis_initial += 25;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, 'Observaciones');
            
            $y_axis_initial += 10; 
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  Autorizo a realizar cargos adicionales por otros conceptos durante mi estadía.');

            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  Llenando el check in, el hotel no aplica políticas de devolución por concepto de estadía.');
            
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  El Check In se efectúa a las 14:00 horas.');

            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  El Check Out se efectúa a las 12:00 horas.');
            
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  El valor de la hora adicional es de $5000.');

            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  Las llaves de los vehículos deben quedar en recepción.');
            
            $y_axis_initial += 5;           
            $doc->SetY($y_axis_initial);
            $doc->Cell($doc->GetPageWidth()-10, 5, chr(129).'  El consumo adicional del frigobar y alimentos se cancela en Check Out.');
            // Firma
            $y_axis_initial += 35;           
            $doc->SetY($y_axis_initial);    
            $doc->Cell($defaultcellwidth, 5, 'Firma de conformidad');
            $doc->SetX(70 + $defaultcellwidth);
            $doc->Cell($doc->GetStringWidth($r["CLIENTE"]), 5, ucfirst(strtoupper($r["CLIENTE"])), 'T');
            
            // Salida Informe
            if(!file_exists('../Temp'))
            {
                $oldmask = umask(0);
                mkdir('../Temp', 0744);
            }
            $outPath = '../Temp/Ingreso'.date("dmYHis").'.pdf';
            $doc->Output('F',$outPath);
            echo $outPath;
        }
        //$msg =  "Registro guardado correctamente";
    } catch (Exception $ex) { echo  $ex->getMessage(); }
    //echo $msg;
//}