<?php

namespace ADIF\ContableBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\GeneralController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Settings;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_PageSetup;

class ExporterCustomController extends GeneralController {

    private $_formats = array(
        'text' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
        'date' => PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
        'number' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,
        'currency' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    private $__INIT_COLUMN__ = null;
    private $__INIT_COLUMN_LETRA__ = null;
    private $__INIT_ROW__ = null;

    /**
     * @Route("/libro_iva_compras/export_pdf")
     */
    public function exportLibroIvaComprasPDFAction(Request $request) {

        $contenido = json_decode($request->get('content'), true);

        $rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;

        $rendererLibraryPath = (dirname(__FILE__) . '/../../../../vendor/mpdf/mpdf');

        \PHPExcel_Settings::setPdfRenderer(
                $rendererName, $rendererLibraryPath
        );

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getActiveSheet()->
                getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $phpExcelObject->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        foreach ($contenido["sheets"] as $nroHoja => $sheet) {
            $this->crearHoja($phpExcelObject, $sheet, $nroHoja, 0, 'A');
        }


        $sheetIndex = $phpExcelObject->getIndex(
                $phpExcelObject->getSheetByName('Worksheet 1')
        );
        $phpExcelObject->removeSheetByIndex($sheetIndex);


        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'PDF');

        $writer->setPreCalculateFormulas(true);

        $writer->writeAllSheets();

        // create the response
        $response = new StreamedResponse(
                function () use ($writer) {
            $writer->save('php://output');
        }, 200, array()
        );

        // adding headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $contenido['title'] . '.pdf');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
    
    /**
     * @Route("/libro_iva_ventas/export_pdf")
     */
    public function exportLibroIvaVentasPDFAction(Request $request) {

        $contenido = json_decode($request->get('content'), true);

        $rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;

        $rendererLibraryPath = (dirname(__FILE__) . '/../../../../vendor/mpdf/mpdf');

        \PHPExcel_Settings::setPdfRenderer(
                $rendererName, $rendererLibraryPath
        );

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getActiveSheet()->
                getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $phpExcelObject->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        foreach ($contenido["sheets"] as $nroHoja => $sheet) {
            $this->crearHoja($phpExcelObject, $sheet, $nroHoja, 0, 'A');
        }


        $sheetIndex = $phpExcelObject->getIndex(
                $phpExcelObject->getSheetByName('Worksheet 1')
        );
        $phpExcelObject->removeSheetByIndex($sheetIndex);


        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'PDF');

        $writer->setPreCalculateFormulas(true);

        $writer->writeAllSheets();

        // create the response
        $response = new StreamedResponse(
                function () use ($writer) {
            $writer->save('php://output');
        }, 200, array()
        );

        // adding headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $contenido['title'] . '.pdf');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
    
    
    public function exportReporteSeguimientoCertificadosAction($certificados) {
        $rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;
        $rendererLibraryPath = (dirname(__FILE__) . '/../../../../vendor/mpdf/mpdf');
        \PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath);

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $phpExcelObject->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $phpExcelObject->createSheet(NULL, 1);
        $phpExcelObject->setActiveSheetIndex(1);
        $phpExcelObject->getActiveSheet()->setTitle('Seguimiento certificados');

        $this->__INIT_COLUMN__ = 0;
        $this->__INIT_COLUMN_LETRA__ = 'A';
        $this->__INIT_ROW__ = 1;
        
        $headers = array(
            array('texto' => 'F. Creaci&oacute;n', 'formato' => 'text'),
            array('texto' => 'Tipo', 'formato' => 'text'),
            array('texto' => 'N&ordm;', 'formato' => 'number'),
            array('texto' => 'A&ntilde;o', 'formato' => 'number'),
            array('texto' => 'Rengl&oacute;n', 'formato' => 'text'),
            array('texto' => 'CUIT', 'formato' => 'text'),
            array('texto' => 'Raz&oacute;n social', 'formato' => 'text'),
            array('texto' => 'Documento financiero', 'formato' => 'text'),
            array('texto' => 'N&uacute;mero', 'formato' => 'text'),
            array('texto' => 'F. anulaci&oacute;n', 'formato' => 'text'),
            array('texto' => 'Corresponde pago', 'formato' => 'text'),
            array('texto' => 'Comprobante', 'formato' => 'text'),
            array('texto' => 'Total comprobante', 'formato' => 'currency'),
            array('texto' => 'Ing. Adif', 'formato' => 'text'),
            array('texto' => 'Ing. Adm', 'formato' => 'text'),
            array('texto' => 'Referencia', 'formato' => 'text'),
            array('texto' => 'Orden pago', 'formato' => 'text'),
            array('texto' => 'Cheque/transferencia', 'formato' => 'text'),
            array('texto' => 'F. Pago', 'formato' => 'date'),
            array('texto' => 'Estado Pago', 'formato' => 'text'),
            array('texto' => 'Monto Neto', 'formato' => 'currency'),
			array('texto' => '% de Certificaci&oacute;n', 'formato' => 'text'),
			array('texto' => 'Fecha de Inicio', 'formato' => 'date'),	
			array('texto' => 'Fecha de Fin', 'formato' => 'date') 
        );
        
        /************************************************/
        
        $styleHeader = array(
            'font' => array('bold' => true),
            'color' => array('rgb' => PHPExcel_Style_Color::COLOR_WHITE),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'CCCCCC')
            )
        );
        
        $styleTotal = array(
            'font' => array('bold' => true, 'size' => 13),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $styleDefault = array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        PHPExcel_Settings::setLocale('es');

        $phpExcelObject->getProperties()->setTitle('Seguimiento certificados')->setDescription('Seguimiento certificados');

        $currentRow = $this->__INIT_ROW__;
        $currencies = array();
        
        $proveedorActual = '';
        $cantidadRegistros = 0;
        $totalProveedor = 0;
        
        foreach($certificados as $certificado){
            if($proveedorActual == ''){
                $proveedorActual = $certificado['cuit'];
                
                //Escribo los primeros headers
                $currentColumn = $this->__INIT_COLUMN__;
                foreach ($headers as $header) {
                    $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($currentColumn);
                    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($currentColumn, $currentRow, html_entity_decode($header['texto']))->getStyle($columnLetter . $currentRow)->applyFromArray($styleHeader);
                    
                    if ($header['formato'] == 'currency') {
                        $currencies[] = $currentColumn - $this->__INIT_COLUMN__;
                    }
                    $currentColumn++;
                }
                $currentRow++;
            }
            if($proveedorActual != $certificado['cuit']){
                $proveedorActual = $certificado['cuit'];
                // Cambio de proveedor, escribo el total y los headers del nuevo proveedor
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(11, $currentRow, 'Total '.$certificado['razonSocial'])->getStyle('L'.$currentRow)->applyFromArray($styleTotal);
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(12, $currentRow, $totalProveedor)->getStyle('M'.$currentRow)->applyFromArray($styleTotal);
                
                $phpExcelObject->getActiveSheet()->getStyle('H'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $currentRow += 2;
                
                $currentColumn = $this->__INIT_COLUMN__;
                foreach ($headers as $header) {
                    $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($currentColumn);
                    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($currentColumn, $currentRow, html_entity_decode($header['texto']))->getStyle($columnLetter . $currentRow)->applyFromArray($styleHeader);
                    
                    //Seteo el formato de la tabla anterior
                    $phpExcelObject->getActiveSheet()->getStyle($columnLetter . ($currentRow - 3) . ':' . $columnLetter . ($currentRow - $cantidadRegistros))->getNumberFormat()->setFormatCode($this->_formats[$header['formato']]);                   
                    $currentColumn++;
                }
                $cantidadRegistros = 0;
                $totalProveedor = 0;
                $currentRow++;
            }
            
            //Escribo la linea            
            $data = array_map('html_entity_decode', $certificado);

            foreach ($data as $key => $value) {
                $data[$key] = trim(strip_tags($value));
                if (in_array($key, $currencies)) {
                    // Si es un valor currency lo formateo para que no lo tome como texto
                    $data[$key] = str_replace(',', '.', str_replace('.', '', $data[$key]));
                }
            }

            $phpExcelObject->getActiveSheet()->fromArray($data, null, $this->__INIT_COLUMN_LETRA__ . $currentRow);            

            $lastColumn = $this->__INIT_COLUMN_LETRA__;
            for ($i = 0; $i < $currentColumn; $i++) {
                $lastColumn++;
            }

            for ($col = $this->__INIT_COLUMN_LETRA__; $col < $lastColumn; $col++) {
                $phpExcelObject->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }            
            
            $cantidadRegistros++;
            $totalProveedor += $certificado['totalComprobante'];
            $currentRow++;
        }   

        //Elimino los ultimos headers
        //$phpExcelObject->getActiveSheet()->removeRow($currentRow - 1);
        
        /************************************************/        
        /*$sheetIndex = $phpExcelObject->getIndex($phpExcelObject->getSheetByName('Worksheet 1'));
        $phpExcelObject->removeSheetByIndex($sheetIndex);*/

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename="reporte_seguimiento_certificados_' . date("d_m_Y") . '.xls"');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
    
    /**
     * 
     * @param type $phpExcelObject
     * @param type $options
     */
    public function crearTabla($phpExcelObject, $options) {

        $styleHeader = array(
            'font' => array('bold' => true),
            'color' => array('rgb' => PHPExcel_Style_Color::COLOR_WHITE),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'CCCCCC')
            )
        );

        $styleTitulo = array(
            'font' => array('bold' => true, 'size' => 15),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $styleDefault = array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        PHPExcel_Settings::setLocale('es');

        $phpExcelObject->getProperties()->setTitle($options['title'])->setDescription($options['title']);

        $currentRow = $this->__INIT_ROW__;

        $titulo = $options['title'];

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($this->__INIT_COLUMN__, $currentRow, $titulo)->getStyle($this->__INIT_COLUMN_LETRA__ . $currentRow)->applyFromArray($styleTitulo);
        $phpExcelObject->getActiveSheet()->mergeCellsByColumnAndRow($this->__INIT_COLUMN__, $currentRow, $this->__INIT_COLUMN__ + count($options['headers']) - 1, $currentRow);
        $currentRow += 1;

        // Imprimir HEADERS        
        $lastColumnLetter = \PHPExcel_Cell::stringFromColumnIndex($this->__INIT_COLUMN__ + count($options['headers']) - 1);
        $rangoHeaders = $this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . $lastColumnLetter . $currentRow;

        // Columnas con datos 'currency'
        $currencies = array();

        // Escribo los headers y seteo el formato de los datos
        $currentColumn = $this->__INIT_COLUMN__;
        foreach ($options['headers'] as $header) {
            $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($currentColumn);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($currentColumn, $currentRow, $header->texto)->getStyle($columnLetter . $currentRow)->applyFromArray($styleHeader);
            //Seteo el formato
            $phpExcelObject->getActiveSheet()->getStyle($columnLetter . ($currentRow + 1) . ':' . $columnLetter . ($currentRow + count($options['data']) + 1))->getNumberFormat()->setFormatCode($this->_formats[$header->formato]);
            if ($header->formato == 'currency') {
                $currencies[] = $currentColumn - $this->__INIT_COLUMN__;
            }
            $currentColumn++;
        }
        $phpExcelObject->getActiveSheet()->setAutoFilter($rangoHeaders);
        $currentRow++;

        $phpExcelObject->getActiveSheet()->getStyle($this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . ($lastColumnLetter . ($currentRow + count($options['data']) - 1)))->applyFromArray($styleDefault);

        $initialRow = $currentRow;

        $cardinality = 1;

        $totales = array();

        foreach ($options['data'] as $rowData) {
            //agrego la cardinalidad si esta seteada
            if (isset($options['options']['add_cardinality'])) {
                array_unshift($rowData, $cardinality++);
            }

            $data = array_map('html_entity_decode', $rowData);

            foreach ($data as $key => $value) {
                $data[$key] = trim(strip_tags($value));
                if (in_array($key, $currencies)) {
                    // Si es un valor currency lo formateo para que no lo tome como texto
                    $data[$key] = str_replace(',', '.', str_replace('.', '', $data[$key]));
                } else {
                    
                }
            }

            $phpExcelObject->getActiveSheet()->fromArray($data, null, $this->__INIT_COLUMN_LETRA__ . $currentRow);
            $currentRow++;
        }

        $lastColumn = $this->__INIT_COLUMN_LETRA__;
        for ($i = 0; $i < $currentColumn; $i++) {
            $lastColumn++;
        }

        for ($letra = $this->__INIT_COLUMN_LETRA__; $letra <= $lastColumnLetter; $letra++) {
            if (in_array(\PHPExcel_Cell::columnIndexFromString($letra) - 1, $currencies)) {
                $phpExcelObject->getActiveSheet()->setCellValue($letra . $currentRow, '=SUM(' . $letra . $initialRow . ':' . $letra . ($currentRow - 1) . ')');
            }
        }

        $rangoHeaders = $this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . ($lastColumnLetter . $currentRow);
        $phpExcelObject->getActiveSheet()->getStyle($rangoHeaders)->applyFromArray($styleHeader);
        $phpExcelObject->getActiveSheet()->getStyle($this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . ($lastColumnLetter . $currentRow))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        
        for ($col = $this->__INIT_COLUMN_LETRA__; $col < $lastColumn; $col++) {
            $phpExcelObject->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
        }

        $this->__INIT_ROW__ = $currentRow + 2;
    }

    /**
     * 
     * @param type $phpExcelObject
     * @param type $sheet
     */
    public function crearHoja($phpExcelObject, $sheet, $nroHoja, $initColumn, $initColumnLetra) {

        $phpExcelObject->createSheet(NULL, $nroHoja);
        $phpExcelObject->setActiveSheetIndex($nroHoja);
        $phpExcelObject->getActiveSheet()->setTitle($sheet['title']);

        $this->__INIT_COLUMN__ = $initColumn;
        $this->__INIT_COLUMN_LETRA__ = $initColumnLetra;
        $this->__INIT_ROW__ = 1;

        foreach ($sheet['tables'] as $tabla) {
            $options = array(
                'title' => $tabla['title'],
                'titulo_alternativo' => $tabla['titulo_alternativo'],
                'headers' => json_decode($tabla['headers']),
                'data' => json_decode($tabla['data'])
            );
            $this->crearTabla($phpExcelObject, $options);
        }
    }

}
