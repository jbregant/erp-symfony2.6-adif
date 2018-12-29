<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use DateTime;

/**
 * Description of TipoMonedaService
 * 
 */
class TipoMonedaService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * Devuelve el tipo de cambio para cada tipo de moneda
     * 
     * @return type
     */
    public function getTiposDeMoneda() {

        $tiposMonedaArray = [];

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());
        $tiposMoneda = $em->getRepository('ADIFContableBundle:TipoMoneda')->findAll();

        foreach ($tiposMoneda as $tipoMoneda) {
            $tiposMonedaArray[$tipoMoneda->getId()] = array(
                'corriente' => $tipoMoneda->getEsMCL(),
                'tipoCambio' => $tipoMoneda->getTipoCambio(),
                'fecha' => $tipoMoneda->getUltimaActualizacion()->format('d/m/Y')
            );
        }

        return $tiposMonedaArray;
    }

    /**
     * 
     * Actualiza tipoCambio de un TipoMoneda
     * 
     * @param type $idMoneda
     * @param type $tipoCambio
     * @throws type
     */
    public function setTipoCambio($idMoneda, $tipoCambio) {
        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $moneda = $em->getRepository('ADIFContableBundle:TipoMoneda')
                ->find($idMoneda);
        if (!$moneda) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMoneda.');
        }

        $moneda->setTipoCambio($tipoCambio);
        $moneda->setFechaUltimaActualizacion(new DateTime());
        $em->flush();
    }

}
