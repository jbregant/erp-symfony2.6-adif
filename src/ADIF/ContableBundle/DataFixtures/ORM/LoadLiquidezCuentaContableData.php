<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\LiquidezCuentaContable;

/**
 * LoadLiquidezCuentaContableData
 * 
 * @author Manuel Becerra
 * created 06/10/2014
 *
 */
class LoadLiquidezCuentaContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $codigo
     */
    private function setLiquidezCuentaContable($manager, $denominacion, $codigo) {

        $liquidezCuentaContable = new LiquidezCuentaContable();

        $liquidezCuentaContable->setDenominacion($denominacion);
        $liquidezCuentaContable->setCodigo($codigo);

        $manager->persist($liquidezCuentaContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setLiquidezCuentaContable($manager, 'No aplica ', 0);
        $this->setLiquidezCuentaContable($manager, 'Corriente', 1);
        $this->setLiquidezCuentaContable($manager, 'No corriente', 2);

        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 3;
    }

}
