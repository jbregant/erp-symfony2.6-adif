<?php

namespace ADIF\BaseBundle\Twig;

use ADIF\BaseBundle\Entity\EntityManagers;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Description of TwigExtension
 *
 * @author Manuel Becerra
 * created 11/06/2014
 * 
 */
class TwigExtension extends \Twig_Extension {
    
    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     *
     * @var type 
     */
    protected $container;
    
    /**
     * 
     * @param Container $container
     */
   
    
    /**
     * 
     * @return string
     */
    public function getName() {
        return 'twig_extension';
    }

    /**
     * 
     * @return type 
     */
    public function getFilters() {
        return array(
            'repeat' => new \Twig_Filter_Method($this, 'repeatString'),
            'decode' => new \Twig_Filter_Method($this, 'stringDecode'),
            'escape_json' => new \Twig_Filter_Method($this, 'escapeJsonString'),
            'money_format' => new \Twig_Filter_Method($this, 'moneyFormat'),
            'currency_format' => new \Twig_Filter_Method($this, 'currencyFormat'),
            'str_pad' => new \Twig_Filter_Method($this, 'strPad'),
            'a_letras' => new \Twig_Filter_Method($this, 'aLetras'),
            'get_mes' => new \Twig_Filter_Method($this, 'getMes'),
            'truncate' => new \Twig_Filter_Method($this, 'truncate'),
            'preg_match' => new \Twig_Filter_Method($this, 'preg_match'),
            'roundandmatch' => new \Twig_Filter_Method($this, 'roundandmatch'),
            'getTotalDebe' => new \Twig_Filter_Method($this, 'getTotalDebe'),
            'getTotalHaber' => new \Twig_Filter_Method($this, 'getTotalHaber'),
        );
    }

    /**
     * 
     * @param type $string
     * @param type $count
     * @return type
     */
    public function repeatString($string, $count) {
        return str_repeat($string, $count);
    }

    /**
     * 
     * @param type $number
     * @param type $simbol
     * @param type $exchageRate
     * @param type $decimals
     * @param type $decPoint
     * @param type $thousandsSep
     * @return string
     */
    public function moneyFormat($number, $simbol = '$', $exchageRate = 1, $decimals = 2, $decPoint = ',', $thousandsSep = '.') {

        $valued = $number * $exchageRate;

        $price = number_format($valued, $decimals, $decPoint, $thousandsSep);

        $price = $simbol . ' ' . $price;

        return $price;
    }

    /**
     * 
     * @param type $number
     * @param type $decimals
     * @param type $decPoint
     * @param type $thousandsSep
     * @return type
     */
    public function currencyFormat($number, $decimals = 2, $decPoint = ',', $thousandsSep = '.') {

        $currency = number_format($number, $decimals, $decPoint, $thousandsSep);

        return $currency;
    }

    /**
     * 
     * @param type $input
     * @param type $pad_length
     * @param type $pad_string
     * @param type $pad_type
     * @return type
     */
    public function strPad($input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_RIGHT) {
        return str_pad($input, $pad_length, $pad_string, $pad_type);
    }

    /**
     * 
     * @param type $monto
     * @return string
     */
    public function aLetras($monto) {
//        $nal = new \NumerosALetras_NumerosALetras($monto);
//        return $nal->resultado;
//        $nal = \NumerosALetras_NumerosALetras::toWord($monto);
        return \NumerosALetras_NumerosALetras::toWord($monto);
    }

    /**
     * 
     * @param type $string
     * @return type
     */
    public function stringDecode($string) {

        return htmlentities(html_entity_decode($string, ENT_QUOTES), ENT_QUOTES);
    }

    /**
     * 
     * @param type $value
     * @return type
     */
    public function escapeJsonString($value) {

        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");

        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");

        $result = str_replace($escapers, $replacements, $value);

        return $result;
    }
    
    /**
     * 
     * @param string $fecha
     * @return stringMe devuelve el mes al formato como esta seteado el sistema operativo.
     * @author gluis
     * @param string $fecha
     * @return string
     */
    public function getMes($fecha)
    {
        setlocale(LC_ALL, "es_AR.UTF-8");
        $dt = new \DateTime($fecha);
        return ucfirst(strftime("%B", $dt->getTimestamp()));
    }
    
    /**
	* Trunca la longitud de un string
	* @param string $string
	* @param int $limit
	* @param string $leyenda
	* @return string 
	*/
	public function truncate($string, $limit = 100, $leyenda = '...')
	{
        $lenString = strlen($string);

        if($lenString > $limit) {
            // Fix: Tuve que cambiar la funcion "substr" a "mb_substr"
			// porque me permite elegir la codificacion interna del tratamiento del string
			// Gustavo - 03/07/2014
            $string = mb_substr($string, 0, $limit, "UTF-8");
            return $string . $leyenda;
        } else {
            return $string;
        }
    }
	
	/**
	* preg_match de php nativo
	*/
	public function preg_match($string, $pattern)
	{
		return preg_match($pattern, $string);
	}

    public function roundandmatch($value, $match)
    {
        $nval1 = round($value, 2, PHP_ROUND_HALF_DOWN);
        $nval2 = round($match, 2, PHP_ROUND_HALF_DOWN);
        
        if ( $nval1 != $nval2){
            $decs[] = (float)$nval1 - (int)$nval1;
            $decs[] = (float)$nval2 - (int)$nval2;

            //$diff = max($decs) - min($decs);

            if ( (int)$nval1 == (int)$nval2 )
                {
                $value = (int)$value + min($decs);
            } 
            else {
                return false;
            }
        }

        return ($nval1);
    }
	
	public function getTotalDebe($idAsientoContable)
	{
        if ( $idAsientoContable != NULL ) {
            $em = $this->doctrine->getManager(EntityManagers::getEmContable());
            return $em
                ->getRepository('ADIFContableBundle:AsientoContable')
                ->getTotalRenglon($idAsientoContable, 1);
        }
        else {
            return 0;
        }
        
	}
    
    public function getTotalHaber($idAsientoContable)
	{
        if ( $idAsientoContable != NULL ) {
            $em = $this->doctrine->getManager(EntityManagers::getEmContable());
            return $em
                ->getRepository('ADIFContableBundle:AsientoContable')
                ->getTotalRenglon($idAsientoContable, 2);
        }
        else {
            return 0;
        }
	}
}
