<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion;

/**
 * LoadRangoRemuneracionData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadRangoRemuneracionData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $montoDesde
     * @param type $montoHasta
     * @param type $aplicaGanancias
     */
    private function setRangoRemuneracion($manager, $montoDesde, $montoHasta, $aplicaGanancias, $orden) {

        $rangoRemuneracion = new RangoRemuneracion();

        $rangoRemuneracion->setMontoDesde($montoDesde);
        $rangoRemuneracion->setMontoHasta($montoHasta);
        $rangoRemuneracion->setAplicaGanancias($aplicaGanancias);

        $manager->persist($rangoRemuneracion);

        $this->addReference('RangoRemuneracion ' . $orden, $rangoRemuneracion);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setRangoRemuneracion($manager, 0, 15000, false, 1);
        $this->setRangoRemuneracion($manager, 15001, 25000, true, 2);
        $this->setRangoRemuneracion($manager, 25001, 9999999, true, 3);

        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 1;
    }

}
