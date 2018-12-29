<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\NaturalezaCuentaContable;

/**
 * LoadNaturalezaCuentaContableData
 * 
 * @author Manuel Becerra
 * created 06/10/2014
 *
 */
class LoadNaturalezaCuentaContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $codigo
     */
    private function setNaturalezaCuentaContable($manager, $denominacion, $codigo) {

        $naturalezaCuentaContable = new NaturalezaCuentaContable();

        $naturalezaCuentaContable->setDenominacion($denominacion);
        $naturalezaCuentaContable->setCodigo($codigo);

        $manager->persist($naturalezaCuentaContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setNaturalezaCuentaContable($manager, 'Activo', 1);
        $this->setNaturalezaCuentaContable($manager, 'Pasivo', 2);
        $this->setNaturalezaCuentaContable($manager, 'Patrimonio neto', 3);
        $this->setNaturalezaCuentaContable($manager, 'Ingresos', 4);
        $this->setNaturalezaCuentaContable($manager, 'Gastos', 5);
        $this->setNaturalezaCuentaContable($manager, 'Costos', 6);

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
