<?php

namespace ADIF\BaseBundle\Service;

use ADIF\ContableBundle\Entity\Comprobante;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Servicio de comunicacion WS con el sistema de Trámites
 *
 * @author gluis
 */
class TramitesService extends \SoapClient
{
	const TRAMITES_DEV_WSDL = 'http://192.168.10.20/gluis/tramites_1.4/wsdl/server.php?wsdl';
	const TRAMITES_PROD_WSDL = 'http://tramites.adifse.com.ar/wsdl/server.php?wsdl';
    
    const USER = 'sigaweb';
	const PASS = 'a76fad5ea7496b87efeef0aff09712d3';
	
	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
	{
		$env = $container->get('kernel')->getEnvironment();
			
		$tramitesWsdl = '';
		if ($env == 'dev') {
			$tramitesWsdl = self::TRAMITES_DEV_WSDL;
		} else {
			$tramitesWsdl = self::TRAMITES_PROD_WSDL;
		}
		
		parent::SoapClient($tramitesWsdl);
	}
    
    /**
	 * Actualiza al sistema de tramites a traves del WS, la referencia del comprobante de SIGA
     * @param ADIF\ContableBundle\Entity\Comprobante $comprobante
     * @param int $tramiteId
     * @return bool
     */
    public function actualizarTramiteExterno(Comprobante $comprobante, $tramiteId)
    {
        $respuestaAutorizacion = $this->autorizacion(self::USER, self::PASS);
		if ($respuestaAutorizacion->status == 'nok') {
            // Fallo la autenticacion
			$this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $respuestaAutorizacion->mensaje);
            $this->container->get('request')->attributes->set('form-error', true);
            return false;
		}
        
        $tramiteExterno = 
                    $comprobante->getTipoComprobante()->getNombre() . ' ' . 
                    $comprobante->getLetraComprobante()->getLetra() . ' ' .
                    $comprobante->getNumero();
        
        $respuestaWSTramite = $this->updateNroTramiteExterno(
                $respuestaAutorizacion->token, $tramiteId, $comprobante->getId(), $tramiteExterno);

        //var_dump($respuestaAutorizacion->token, $tramiteId, $comprobante->getId(), $tramiteExterno);exit;
        if ($respuestaWSTramite->status == 'nok') {
            // Fallo el update
            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $respuestaWSTramite->mensaje);
            $this->container->get('request')->attributes->set('form-error', true);
            return false;
        } else {
			$this->container->get('request')->getSession()->getFlashBag()
                    ->add('success', $respuestaWSTramite->mensaje);
			return true;
		}
    }
}
