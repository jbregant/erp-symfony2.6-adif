<?php

namespace ADIF\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ADIF\BaseBundle\Controller\GeneralController as BaseController;

/**
 * Controller que se va a comunicar con el servicio del Sistema de Trámites
 *
 * @author gluis
 * @Route("/tramites")
 */
class TramitesController extends BaseController
{
	const USER = 'sigaweb';
	const PASS = 'a76fad5ea7496b87efeef0aff09712d3';
	
	public function __construct() 
	{
		ini_set("soap.wsdl_cache_enabled", 0);
	}
	
	/**
	 * Comprueba por WS, si existe el "nro_tramite_interno" en el sistema de tramites.
     * @Route("/comprobar_nro_tramite", name="comprobar_nro_tramite")
     * @Method("POST")     
     */
    public function comprobarNroTramiteAction()
    {
        $request = $this->get('request');
        $nroTramite = $request->get('nro_tramite');
		$respuestaAutorizacion = $this->get('adif.tramites_service')->autorizacion(self::USER, self::PASS);
		if ($respuestaAutorizacion->status == 'nok') {
			$response = new Response(json_encode($respuestaAutorizacion));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
		
        $respuestaWSTramite = $this->get('adif.tramites_service')->getTramite($respuestaAutorizacion->token, $nroTramite);
        $response = new Response(json_encode($respuestaWSTramite));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
