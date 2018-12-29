<?php

namespace ADIF\BaseBundle\Entity;

use mPDF;

/**
 * AdifApi
 *
 * Funciones de utilidad general
 * 
 * @author Manuel Becerra
 * created 15/07/2014
 */
class AdifApi {

    /**
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *  
     * @param type $string
     * @return type
     */
    public static function stringCleaner($string) {

        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), //
                array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), //
                array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), //
                array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), //
                array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), //
                array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array("\\", "¨", "º", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":"), '', $string
        );

        $string = str_replace(
                array(" "), '-', $string
        );

        return $string;
    }

    /**
     * Genera un archivo pdf y lo descarga en el navegador
     *  
     * @param type $html   //Contenido del documento
     * @param type $titulo //Opcional, el titulo que se va a mostrar en el header
     * @param type $nombreArchivo //Opcional, el nombre con el que se quiere exportar el archivo
     * @return type
     */
    public static function printPDF($html, $titulo = null, $nombreArchivo = null) {

        $mpdfService = new mPDF();

        $mpdfService->SetHTMLHeader('<table width="100%" style="vertical-align: center; font-size: 8pt; color: #000000; font-weight: bold; border-bottom: 1px solid"><tr>
            <td width="20%"><img src="' . __DIR__ . '../../../../../web/images/adifse.png" style="width: 48px; height: 17px;" /></td>
            <td width="60%" align="center" style="font-weight: bold;">' . (($titulo == null) ? '' : $titulo) . '</td>
            <td width="20%" style="text-align: right; ">{DATE j-m-Y}</td>
            </tr></table>');
        $mpdfService->SetFooter('||Página {PAGENO}/{nbpg}');
        $mpdfService->WriteHTML($html);
        $mpdfService->Output((($nombreArchivo == null) ? 'Archivo' : AdifApi::stringCleaner($nombreArchivo)) . '.pdf', 'D');
    }

    
    // public static function print2PDF($options = array()) {

    //     $titulo = isset($options['titulo']) ? $options['titulo'] : '';
    //     $nombreArchivo = isset($options['nombreArchivo']) ? $options['nombreArchivo'] : 'Archivo';
    //     $html = isset($options['html']) ? $options['html'] : '';
    //     $headerRightHtml = isset($options['headerRightHtml']) ? $options['headerRightHtml'] : '';

    //     $mpdfService = new mPDF();

    //     $mpdfService->SetHTMLHeader('<table width="100%" style="vertical-align: center; font-size: 8pt; color: #000000; font-weight: bold; border-bottom: 1px solid"><tr>
    //         <td width="33%"><img src="' . __DIR__ . '../../../../../web/images/adifse.png" style="width: 48px; height: 17px;" /></td>
    //         <td width="33%" align="center" style="font-weight: bold;">' . (($titulo == null) ? '' : $titulo) . '</td>
    //         <td width="33%" style="text-align: right; ">{DATE j-m-Y}</td>
    //         </tr></table>');
    //     $mpdfService->SetFooter('||Página {PAGENO}/{nbpg}');
    //     $mpdfService->WriteHTML($html);
    //     $mpdfService->Output(AdifApi::stringCleaner($nombreArchivo)'.pdf', 'D');
    // }

}
