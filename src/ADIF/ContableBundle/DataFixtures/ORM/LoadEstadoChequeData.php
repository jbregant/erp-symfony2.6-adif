<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\EstadoPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;

/**
 * LoadEstadoChequeData
 * 
 * @author Manuel Becerra
 * created 08/01/2015
 *
 */
class LoadEstadoChequeData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoCheque($manager, $denominacion, $descripcion) {

        $estadoCheque = new EstadoPago();

        $estadoCheque->setDenominacionEstado($denominacion);
        $estadoCheque->setDescripcionEstado($descripcion);

        $manager->persist($estadoCheque);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoCheque($manager, ConstanteEstadoPago::ESTADO_PAGO_CREADO, '');
        $this->setEstadoCheque($manager, ConstanteEstadoPago::ESTADO_PAGO_ANULADO, '');

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
