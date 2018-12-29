<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoPago;

/**
 * LoadTipoPagoData
 * 
 * @author Manuel Becerra
 * created 10/07/2014
 *
 */
class LoadTipoPagoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoPago($manager, $denominacion, $descripcion) {

        $tipoPago = new TipoPago();

        $tipoPago->setDenominacionTipoPago($denominacion);
        $tipoPago->setDescripcionTipoPago($descripcion);

        $manager->persist($tipoPago);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoPago($manager, ConstanteTipoPago::CHEQUE, '');
        $this->setTipoPago($manager, ConstanteTipoPago::EFECTIVO, '');
        $this->setTipoPago($manager, ConstanteTipoPago::TRANSFERENCIA_BANCARIA, '');
        $this->setTipoPago($manager, ConstanteTipoPago::DOMICILIACION_BANCARIA, '');

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
