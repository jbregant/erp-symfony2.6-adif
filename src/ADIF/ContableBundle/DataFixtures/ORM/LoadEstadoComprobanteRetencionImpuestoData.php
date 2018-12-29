<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\EstadoComprobanteRetencionImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoComprobanteRetencionImpuesto;

/**
 * LoadEstadoComprobanteRetencionImpuestoData
 * 
 * @author Manuel Becerra
 * created 07/11/2014
 *
 */
class LoadEstadoComprobanteRetencionImpuestoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoComprobanteRetencion($manager, $denominacion, $descripcion) {

        $estadoComprobanteRetencion = new EstadoComprobanteRetencionImpuesto();

        $estadoComprobanteRetencion->setDenominacionEstado($denominacion);
        $estadoComprobanteRetencion->setDescripcionEstado($descripcion);

        $manager->persist($estadoComprobanteRetencion);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoComprobanteRetencion($manager, ConstanteEstadoComprobanteRetencionImpuesto::ESTADO_DECLARADO, '');
        $this->setEstadoComprobanteRetencion($manager, ConstanteEstadoComprobanteRetencionImpuesto::ESTADO_GENERADO, '');
        $this->setEstadoComprobanteRetencion($manager, ConstanteEstadoComprobanteRetencionImpuesto::ESTADO_PAGADO, '');


        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 2;
    }

}
