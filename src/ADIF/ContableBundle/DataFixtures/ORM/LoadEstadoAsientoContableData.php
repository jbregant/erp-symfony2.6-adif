<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\EstadoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoAsientoContable;

/**
 * LoadEstadoAsientoContableData
 * 
 * @author Manuel Becerra
 * created 21/10/2014
 *
 */
class LoadEstadoAsientoContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoAsiento($manager, $denominacion, $descripcion) {

        $estadoAsientoContable = new EstadoAsientoContable();

        $estadoAsientoContable->setDenominacionEstado($denominacion);
        $estadoAsientoContable->setDescripcionEstado($descripcion);

        $manager->persist($estadoAsientoContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoAsiento($manager, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_INGRESADO, '');
        $this->setEstadoAsiento($manager, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_PENDIENTE_CONFIRMACION, '');
        $this->setEstadoAsiento($manager, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_PENDIENTE_REGISTRO, '');
        $this->setEstadoAsiento($manager, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO, '');

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
