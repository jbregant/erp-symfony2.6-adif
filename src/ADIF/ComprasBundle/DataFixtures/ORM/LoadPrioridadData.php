<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\Prioridad;

/**
 * LoadPrioridadData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadPrioridadData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     */
    private function setPrioridad($manager, $denominacion, $cantidadDias) {

        $prioridad = new Prioridad();

        $prioridad->setDenominacionPrioridad($denominacion);
        $prioridad->setCantidadDias($cantidadDias);

        $manager->persist($prioridad);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setPrioridad($manager, 'Baja', 45);
        $this->setPrioridad($manager, 'Media', 30);
        $this->setPrioridad($manager, 'Alta', 15);

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
