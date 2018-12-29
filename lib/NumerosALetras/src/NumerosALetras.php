<?php

/**
 * Description of NumerosALetras
 *
 * @author eprimost
 */
class NumerosALetras {
    /**
    * Arreglo de palabras representando a las letras
    * [0] Cero y Uno
    * [1] Unidades(1-9) donde 1=>Un, números (10-29) y 100
    * [2] Decenas del (30-90)
    * [3] Centenas(100-900) donde 100 => Ciento
    * [4] Notación larga Cifras cerradas
    * [5] Notación larga para los sufijos..
    * El arreglo 4 y 5 se puede incrementar para soportar numeros superiores
    * @var array
    */
    private static $nStr = array(array('cero', 'uno'),
            array('', 'un', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete',
                    'ocho', 'nueve', 'diez', 'once', 'doce', 'trece',
                    'catorce', 'quince', 'dieciséis', 'diecisite', 'dieciocho',
                    'diecinueve', 'veinte', 'veintiuno', 'veintidós',
                    'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis',
                    'veintisiete', 'veintiocho', 'veintinueve', 100 => 'cien'),
            array('', '', '', 'treinta', 'cuarenta', 'cincuenta', 'sesenta',
                    'setenta', 'ochenta', 'noventa'),
            array('', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos',
                    'quinientos', 'seicientos', 'setecientos', 'ochocientos',
                    'novecientos'),
            array('', '', 'mil', 'millón', 'mil', 'billón', 'mil', 'trillón',
                    'mil', 'cuatrillón', 'mil', 'quintillón', 'mil',
                    'sextillón', 'mil', 'septillón', 'mil', 'octillón'),
            array('', '', 'mil', 'millones', 'mil', 'billones', 'mil',
                    'trillones', 'mil', 'cuatrillones', 'mil', 'quintillones',
                    'mil', 'sextillones', 'mil', 'septillones', 'mil',
                    'octillones', 'mil'));
    /**
    * Obtiene un nombre que respresenta al número en letras
    * @param integer $n Número del 0 al 29, 100 y 1000
    * @param integer $c 1=Unidad, 2=Decena y 3=Centena 4,5 Millar y *illones
    * @param integer $l Numero de nivel de agrupaciones de  3 ceros
    * @return string
    */
    private static function _num($n, $c = 1, $l = 1) {
        return ($n == 1 && !($l % 2)) || !$l ? '' : self::$nStr[$c][$n] . ' ';
    }
    /**
    * Convierte recursivamente un numero a letras
    * @param integer $lev Numero de cifras agrupadas en 3: Ej. 100101 = 2
    * @param srting $number Número a convetir a letras
    * @return string
    */
    private static function _i2str($lev, $number) {
        $int = intval($num = substr($number, 0, 3));
        $next = substr($number, 3);
        $str = ''; //echo "($lev|$num|$int|$number)<hr>\n"; //Debug
        if ($int) {
            if ($int == 100)
                $str = self::_num($int, 1);
            else {
                list($c, $d, $u) = $num;//centenas, decenas y unidad
                $str = $c ? self::_num($c, 3) : '';
                if (($du = (($d * 10) + $u)) < 30)
                    $str .= self::_num($du, $du == 1 && $lev < 2 ? 0 : 1, $lev);
                else {
                    $str .= $d ? self::_num($d, 2) . ($u ? 'y ' : '') : '';
                    $str .= $u ? self::_num($u, $u + $lev < 3 ? 0 : 1) : '';
                }
            }
            $str .= self::_num($lev, $int == 1 && ($lev % 2) ? 4 : 5)
                    . (preg_match('/^000+/', $next) ? self::_num($lev - 1, 5,
                                    !($lev % 2)) : '');
        }
        return $lev ? ($str . self::_i2str($lev - 1, $next)) : '';
    }
    /**
    * Convierte una cadena numerica o numero a letras
    * @param string $number
    * @return boolean|string
    */
    public static function toWord($number) {
        $less = preg_match('/^\-/', $number);
        $number = preg_replace('/[^0-9\.]/', '', $number);
        if (preg_match('/^(\d{1,54})$/', $number)) {
            $lev = (floor(strlen($number) / 3) + 1);
            $number = str_pad($number, ($lev * 3), '0', STR_PAD_LEFT);
            $result = self::_i2str($lev, $number);
            $result || ($result = self::_num(0, 0));
        } elseif (preg_match('/^\d{1,54}\.\d{1,54}$/', $number)) {
            list($number, $decimal) = explode('.', $number);
            $result = self::toWord($number) . ' con ';
            for ($i = 0; $i < (strlen($decimal) - 1); $i++) {
                if ($decimal[$i])
                    break;
                $result .= self::_num(0, 0);
            }
            $result .= self::toWord($decimal);
        }
        return isset($result) ? ($less ? 'menos ' : '') . $result : FALSE;
    }
    /**
    * Convierte una cifra tipo moneda a letras
    * @param string $number
    * @return boolean|string
    */
    public static function toCurrency($number) {
        $number = preg_replace('/[^0-9\.\-]/', '', $number);
        if (preg_match('/^[\-]{0,1}(\d{1,54})$/', $number))
            $number .= '.00';
        elseif (!preg_match('/^[\-]{0,1}\d{1,54}\.\d{1,54}$/', $number))
            return FALSE;
        list($number, $decimal) = explode('.', $number);
        $number = self::toWord($number);
        if (!$number)
            return FALSE;
        if (preg_match('/(llones|llón)$/', $number))
            $number .= ' de pesos ';
        else
            $number = preg_match('/uno$/', $number) ? (preg_replace('/uno$/',
                            '', $number) . ' un peso ') : ($number . ' pesos ');
        $decimal = round($decimal[0] . $decimal[1] . '.' . substr($decimal, 2));
        return $number . $decimal . '/100 M.N.';
    }
    
//    /*
//      Script Name: *numerosAletras
//      Script URI: http://www.mis-algoritmos.com/2007/09/07/numbers_to_words/
//      Description: Permite convertir numeros a letras mediante el uso de una sencilla clase.
//      Script Version: 0.1
//      Author: Victor De la Rocha
//      Author URI: http://www.mis-algoritmos.com
//     */
//
//    public $resultado;
//    private $antes_con_despues = 'con';
////    private $despues = 'decimales';
//    private $despues = '';
//    private $antes_sin_despues = '';
//
//    /*
//      Retorna el valor de la centena que se le envie como parametro.
//     */
//
//    public function __construct($monto) {
//        return $this->convertir($monto);
//    }
//    
//    private function centenas($centenas) {
//        $valores = array('cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis',
//            'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce',
//            'quince', 20 => 'veinte', 30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta',
//            60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa', 100 => 'ciento',
//            101 => 'quinientos', 102 => 'setecientos', 103 => 'novecientos');
//
//        switch ($centenas) {
//            case '1': return $valores[100];
//                break;
//            case '5': return $valores[101];
//                break;
//            case '7': return $valores[102];
//                break;
//            case '9': return $valores[103];
//                break;
//            default: return $valores[$centenas];
//        }
//    }
//
//    /*
//      Retorna el valor de la unidad que se le envie como parametro.
//     */
//
//    private function unidades($unidad) {
//        $valores = array('cero', 'un', 'dos', 'tres', 'cuatro', 'cinco', 'seis',
//            'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce',
//            'quince', 20 => 'veinte', 30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta',
//            60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa', 100 => 'ciento',
//            101 => 'quinientos', 102 => 'setecientos', 103 => 'novecientos'
//        );
//
//        return $valores[$unidad];
//    }
//
//    /*
//      Retorna el valor de la decena que se le envie como parametro
//     */
//
//    private function decenas($decena) {
//        $valores = array('cero', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete',
//            'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 20 => 'veinte', 30 => 'treinta',
//            40 => 'cuarenta', 50 => 'cincuenta', 60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa',
//            100 => 'ciento', 101 => 'quinientos', 102 => 'setecientos', 103 => 'novecientos');
//
//        return $valores[$decena];
//    }
//
//    private function evalua($valor) {
//        if ($valor == 0)
//            return 'cero';
//
//        $decimales = 0;
//        $letras = '';
//        while ($valor != 0) {
//            // Validamos si supera los 100 millones
//            if ($valor >= 1000000000)
//                return 'L&iacute;mite de aplicaci&oacute;n exedido.';
//
//            //Centenas de Millón
//            if (($valor < 1000000000) and ($valor >= 100000000)) {
//                if ((intval($valor / 100000000) == 1) and (($valor - (intval($valor / 100000000) * 100000000)) < 1000000))
//                    $letras.=(string) 'cien millones ';
//                else {
//                    $letras.=$this->centenas(intval($valor / 100000000));
//                    If ((intval($valor / 100000000) <> 1) and (intval($valor / 100000000) <> 5) and (intval($valor / 100000000) <> 7) and (intval($valor / 100000000) <> 9))
//                        $letras.=(string) 'ciento ';
//                    else
//                        $letras.=(string) ' ';
//                }
//                $valor = $valor - (Intval($valor / 100000000) * 100000000);
//            }
//
//            //Decenas de Millón
//            if (($valor < 100000000) and ($valor >= 10000000)) {
//                if (intval($valor / 1000000) < 16) {
//                    $tempo = $this->decenas(intval($valor / 1000000));
//                    $letras.=(string) $tempo;
//                    $letras.=(string) ' millones ';
//                    $valor = $valor - (intval($valor / 1000000) * 1000000);
//                } else {
//                    $letras.=$this->decenas(intval($valor / 10000000) * 10);
//                    $valor = $valor - (intval($valor / 10000000) * 10000000);
//                    if ($valor > 1000000)
//                        $letras.=$letras . ' y ';
//                }
//            }
//
//            //Unidades de Millon
//            if (($valor < 10000000) and ($valor >= 1000000)) {
//                $tempo = (intval($valor / 1000000));
//                if ($tempo == 1)
//                    $letras.=(string) ' un mill&oacute;n ';
//                else {
//                    $tempo = unidades(intval($valor / 1000000));
//                    $letras.=(string) $tempo;
//                    $letras.=(string) " millones ";
//                }
//                $valor = $valor - (intval($valor / 1000000) * 1000000);
//            }
//
//            //Centenas de Millar
//            if (($valor < 1000000) and ($valor >= 100000)) {
//                $tempo = (intval($valor / 100000));
//                $tempo2 = ($valor - ($tempo * 100000));
//                if (($tempo == 1) and ($tempo2 < 1000))
//                    $letras.=(string) 'cien mil ';
//                else {
//                    $tempo = $this->centenas(intval($valor / 100000));
//                    $letras.=(string) $tempo;
//                    $tempo = (intval($valor / 100000));
//                    if (($tempo <> 1) and ($tempo <> 5) and ($tempo <> 7) and ($tempo <> 9))
//                        $letras.=(string) 'ciento ';
//                    else
//                        $letras.=(string) ' ';
//                }
//                $valor = $valor - (intval($valor / 100000) * 100000);
//            }
//
//            //Decenas de Millar
//            if (($valor < 100000) and ($valor >= 10000)) {
//                $tempo = (intval($valor / 1000));
//                if ($tempo < 16) {
//                    $tempo = $this->decenas(intval($valor / 1000));
//                    $letras.=(string) $tempo;
//                    $letras.=(string) ' mil ';
//                    $valor = $valor - (intval($valor / 1000) * 1000);
//                } else {
//                    $tempo = $this->decenas(intval($valor / 10000) * 10);
//                    $letras.=(string) $tempo;
//                    $valor = $valor - (intval(($valor / 10000)) * 10000);
//                    if ($valor > 1000)
//                        $letras.=(string) ' y ';
//                    else
//                        $letras.=(string) ' mil ';
//                }
//            }
//
//
//            //Unidades de Millar
//            if (($valor < 10000) and ($valor >= 1000)) {
//                $tempo = intval($valor / 1000);
//                if ($tempo == 1)
//                    $letras.=(string) ''; //'un';
//                else {
//                    $tempo = $this->unidades(intval($valor / 1000));
//                    $letras.=(string) $tempo;
//                }
//                $letras.=(string) ' mil ';
//                $valor = $valor - (intval($valor / 1000) * 1000);
//            }
//
//            //Centenas
//            if (($valor < 1000) and ($valor > 99)) {
//                if ((intval($valor / 100) == 1) and (($valor - (intval($valor / 100) * 100)) < 1))
//                    $letras.='cien ';
//                else {
//                    $temp = (intval($valor / 100));
//                    $l2 = $this->centenas($temp);
//                    $letras.=(string) $l2;
//                    if ((intval($valor / 100) <> 1) and (intval($valor / 100) <> 5) and (intval($valor / 100) <> 7) and (intval($valor / 100) <> 9))
//                        $letras.='cientos ';
//                    else
//                        $letras.=(string) ' ';
//                }
//                $valor = $valor - (intval($valor / 100) * 100);
//            }
//
//            //Decenas
//            if (($valor < 100) and ($valor > 9)) {
//                if ($valor < 16) {
//                    $tempo = $this->decenas(intval($valor));
//                    $letras.=$tempo;
//                    $Numer = $valor - Intval($valor);
//                } else {
//                    $tempo = $this->decenas(Intval(($valor / 10)) * 10);
//                    $letras.=(string) $tempo;
//                    $valor = $valor - (Intval(($valor / 10)) * 10);
//                    if ($valor > 0.99)
//                        $letras.=(string) ' y ';
//                }
//            }
//
//            //Unidades
//            if (($valor < 10) And ($valor > 0.99)) {
//                $tempo = $this->unidades(intval($valor));
//                $letras.=(string) $tempo;
//                $valor = $valor - intval($valor);
//            }
//
//            //Decimales
//            if ($decimales <= 0)
//                if (($letras <> "Error en Conversi&oacute;n a Letras") and (strlen(trim($letras)) > 0))
//                    $letras .= (string) ' ';
//            return $letras;
//        }
//    }
//
//    /*
//      Retorna el texto de el numero enviado como parametros
//     */
//
//    public function convertir($valor) {
//        ob_start();
//        $tt = $valor;
//        $valor = intval($tt);
////        $decimales = $tt - intval($tt);
//        if (strpos($tt,'.') === false){
//            $decimales = 0;
//        } else {
//            $decimales_array = list($whole, $decimal) = explode('.', $tt);
//            $decimales = $decimales_array[1];
//        }
//
//        //Parte entera
//        print $this->evalua($valor);
//
//        //Parte Decimal
//        if ($decimales) {
//            print " $this->antes_con_despues ";
//            print $this->evalua($decimales);
//            print " $this->despues";
//        } else {
//            print " $this->antes_sin_despues ";
//        }
//        return $this->resultado = $texto = ob_get_clean();
//    }

}
