<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAfip;


/**
 * Description of CobranzaService
 *
 * @author Augusto Villa Monte
 * created 26/05/2015
 */
class CobranzaService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroRecibo() 
	{
        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:Cobranza\ReciboCobranza', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('r')
                ->select('r.numero')
                ->orderBy('r.numero', 'DESC')
                ->setMaxResults(1)
                ->getQuery();
        //var_dump($query->getSQL()); die();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }        

        return $siguienteNumero + 1;
    }
	
	/**
	* Genera los codigos de barra de acuerdo al tipo de comprobante de venta
	*/
	public function generarCodigoBarras(
		$comprobanteVenta, $codigoBarras = null, $puntoVenta = null, $numeroComprobante = null, 
		$idTipoComprobante = null, $letraComprobante = null, $numeroContrato = null, 
		$fechaVencimiento = null, $fechaFinContrato = null, $numeroCupon = null,
		$fechaComprobante = null, $fechaAperturaLicitacion = null
	)
	{
		if ($codigoBarras != null) {
			return $codigoBarras;
		}
		
		$fechaVencimiento = $fechaVencimiento != null 
			? \DateTime::createFromFormat('d/m/Y', $fechaVencimiento)
			: null;
		
		$fechaFinContrato = $fechaFinContrato != null 
			? \DateTime::createFromFormat('d/m/Y', $fechaFinContrato)
			: null;
			
		$fechaComprobante = $fechaComprobante != null 
			? \DateTime::createFromFormat('d/m/Y', $fechaComprobante)
			: null;
		
		$fechaAperturaLicitacion = $fechaAperturaLicitacion != null 
			? \DateTime::createFromFormat('d/m/Y', $fechaAperturaLicitacion)
			: null;
		
		if ($comprobanteVenta == 'comprobante_venta') {
			return $this->generarCodigosBarrasComprobanteVenta($puntoVenta, $numeroComprobante, $idTipoComprobante, $letraComprobante);
		}
		
		if ($comprobanteVenta == 'cupon_venta') {
			return $this->generarCodigoBarrasCuponVenta($numeroContrato, $fechaVencimiento, $fechaFinContrato); 
		}
		
		if ($comprobanteVenta == 'cupon_venta_general') {
			return $this->generarCodigoBarrasCuponVentaGeneral($numeroCupon, $fechaVencimiento); 
		}
		
		if ($comprobanteVenta == 'cupon_venta_plazo') {
			return $this->generarCodigoBarrasCuponVentaPlazo($numeroContrato, $fechaVencimiento, $fechaComprobante);
		}
		
		if ($comprobanteVenta == 'cupon_pliego') {
			return $this->generarCodigoBarrasCuponPliego($numeroCupon, $fechaVencimiento, $fechaAperturaLicitacion); 
		}
		
		// por default
		return $this->generarCodigosBarrasComprobanteVenta($puntoVenta, $numeroComprobante, $idTipoComprobante, $letraComprobante);
        
    }

	public function generarCodigosBarrasComprobanteVenta($puntoVenta, $numeroComprobante, $idTipoComprobante, $letraComprobante)
	{
		if ($puntoVenta == null) {
            return null;
        }
		
        $idClienteAdif = '4687';
        $punto_venta = substr($puntoVenta, -2, 2);
        $tipo_cbte = ConstanteAfip::getTipoComprobante($letraComprobante, $idTipoComprobante);
        return $idClienteAdif . $punto_venta . $numeroComprobante . (strlen($tipo_cbte) == 1 ? '0' . $tipo_cbte : $tipo_cbte) . '9999000000';
	}
	
	public function generarCodigoBarrasCuponVenta($numeroContrato, \DateTime $fechaVencimiento = null, \DateTime $fechaFinContrato = null) 
	{    
        // 4 digitos cliente ADIF
        $idClienteAdif = '4687';
        
        $primera_letra = strtoupper(substr($numeroContrato, 0, 1));
        
        $segunda_letra = strtoupper(substr($numeroContrato, 1, 1));
        
        $numero = substr($numeroContrato, 2, 10);
        
        //                2 digitos 1º letra    2 digitos 2º letra    10 digitos numero contrato
        $codigoContrato = ord($primera_letra) . ord($segunda_letra) . str_pad($numero, 10, "0", STR_PAD_LEFT);
        
        // 6 digitos fecha vencimiento
        if ($fechaVencimiento != null) {
			$vencimientoCupon = $fechaVencimiento->format('dmY');
			return $idClienteAdif . $codigoContrato . $vencimientoCupon;    
		} else {
			$vencimiento_contrato = $fechaFinContrato->format('mY');
			return $idClienteAdif . $codigoContrato . '00' . $vencimiento_contrato;   	
		}	
    }
	
	public function generarCodigoBarrasCuponVentaGeneral($numeroCupon, \DateTime $fechaVencimiento) 
	{
        $idClienteAdif = '4687';

        $codigoContrato = str_pad($numeroCupon, 12, "0", STR_PAD_LEFT);

        $primera_letra = substr($codigoContrato, 0, 1);

        $segunda_letra = substr($codigoContrato, 1, 1);

        $numero = substr($codigoContrato, 2, 10);

        $vencimiento_contrato = $fechaVencimiento()->format('mY');

        return $idClienteAdif
                . ord($primera_letra)
                . ord($segunda_letra)
                . $numero
                . '00'
                . $vencimiento_contrato;        
    }
	
	public function generarCodigoBarrasCuponVentaPlazo($numeroContrato, \DateTime $fechaVencimiento = null, \DateTime $fechaComprobante = null) 
	{    
        $idClienteAdif = '4687';
//        $primera_letra = substr($numeroContrato, 0, 1);
//        $segunda_letra = substr($numeroContrato, 1, 1);
        $numero = substr($numeroContrato, 2, 10);
        
//        $codigoContrato = str_pad(ord($primera_letra) . ord($segunda_letra) . $numero, 12, "0", STR_PAD_LEFT);
        $codigoContrato = str_pad($numero, 10, "0", STR_PAD_LEFT);        
        $fecha = ($fechaVencimiento != null ? $fechaVencimiento : $fechaComprobante); 
        return $idClienteAdif . $codigoContrato. '01'.str_pad($fecha->format('my'), 4, '0', STR_PAD_LEFT) . '000000';    
    }
	
	public function generarCodigoBarrasCuponPliego($numeroCupon, \DateTime $fechaAperturaLicitacion) 
	{
        $idClienteAdif = '4687';

        $codigoContrato = str_pad($numeroCupon, 12, "0", STR_PAD_LEFT);

        $primera_letra = substr($codigoContrato, 0, 1);

        $segunda_letra = substr($codigoContrato, 1, 1);

        $numero = substr($codigoContrato, 2, 10);

        //$vencimiento_contrato = $this->getLicitacion()->getFechaApertura()->format('mY');
		$vencimiento_contrato = $fechaAperturaLicitacion->format('mY');

        return $idClienteAdif
                . ord($primera_letra)
                . ord($segunda_letra)
                . $numero
                . '00'
                . $vencimiento_contrato;
    }    


}
