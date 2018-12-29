<?php

namespace ADIF\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\GeneralController as BaseController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Settings;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_PageSetup;

class ExporterController extends BaseController {

    private $_formats = array(
        'text' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
        'date' => PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
        'number' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER,
        'currency' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    private $__INIT_COLUMN__ = null;
    private $__INIT_COLUMN_LETRA__ = null;
    private $__INIT_ROW__ = null;

    /**
     * @Route("/export_excel")
     */
    public function exportAction(Request $request) {

        $contenido = json_decode($request->get('content'), true);
        $response = null;
        if (!empty($contenido) && !is_null($contenido)) {
            // ask the service for a Excel5
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            foreach ($contenido["sheets"] as $nroHoja => $sheet) {
                $this->crearHoja($phpExcelObject, $sheet, $nroHoja, 1, 'B');
            }

            foreach (['Worksheet', 'Worksheet 1'] as $sheet_name) {
                $sheet = $phpExcelObject->getSheetByName($sheet_name);
                if ($sheet) {
                    $sheetIndex = $phpExcelObject->getIndex($sheet);
                    $phpExcelObject->removeSheetByIndex($sheetIndex);
                }    
            }

            // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // create the response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // adding headers
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename="exportacion_' . $contenido['title'] . '_' . date("d_m_Y") . '.xls"');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');

        }
        
        return $response;
    }

    /**
     * @Route("/export_pdf")
     */
    public function exportPDFAction(Request $request) {

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


        $writer->writeAllSheets();

        // create the response
        $response = new StreamedResponse(
                function () use ($writer) {
            $writer->save('php://output');
        }, 200, array()
        );

        // adding headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="exportacion_' . $contenido['title'] . '_' . date("d_m_Y") . '.pdf');
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
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        PHPExcel_Settings::setLocale('es');

        $phpExcelObject->getProperties()->setTitle($options['title'])->setDescription($options['title']);

        $currentRow = $this->__INIT_ROW__;

        // Imprimo el título
        if($options['titulo_alternativo'] != ''){
            $titulo = $options['titulo_alternativo'];
        } else {
            $titulo = 'Listado de ' . ucfirst(str_replace('_', ' ', $options['title'])) . ' (' . count($options['data']) . ')';
        }
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($this->__INIT_COLUMN__, $currentRow, $titulo)->getStyle($this->__INIT_COLUMN_LETRA__ . $currentRow)->applyFromArray($styleTitulo);
        $phpExcelObject->getActiveSheet()->mergeCellsByColumnAndRow($this->__INIT_COLUMN__, $currentRow, $this->__INIT_COLUMN__ + count($options['headers']) - 1, $currentRow);
        $currentRow += 2;

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
            $phpExcelObject->getActiveSheet()->getStyle($columnLetter . ($currentRow + 1) . ':' . $columnLetter . ($currentRow + count($options['data'])))->getNumberFormat()->setFormatCode($this->_formats[$header->formato]);
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

        if (isset($options['data']) && !is_null($options['data'])) {
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
                    }
                }

                $phpExcelObject->getActiveSheet()->fromArray($data, null, $this->__INIT_COLUMN_LETRA__ . $currentRow);
                $currentRow++;
            }
        }

        $lastColumn = $this->__INIT_COLUMN_LETRA__;
        for ($i = 0; $i < $currentColumn; $i++) {
            $lastColumn++;
        }

        for ($col = $this->__INIT_COLUMN_LETRA__; $col < $lastColumn; $col++) {
            $phpExcelObject->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
        }


        //escribo los totales
        if (isset($options['options']['total'])) {
            $rangoHeaders = $this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . ($lastColumnLetter . $currentRow);
            $phpExcelObject->getActiveSheet()->fromArray($options['options']['total'], null, $this->__INIT_COLUMN_LETRA__ . $currentRow)->getStyle($rangoHeaders)->applyFromArray($styleHeader);
            $phpExcelObject->getActiveSheet()->getStyle($this->__INIT_COLUMN_LETRA__ . $currentRow . ':' . ($lastColumnLetter . $currentRow))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
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
