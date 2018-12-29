<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\UnidadMedida;

/**
 * LoadUnidadMedidaData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadUnidadMedidaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     */
    private function setUnidadMedida($manager, $denominacion) {

        $unidadMedida = new UnidadMedida();

        $unidadMedida->setDenominacionUnidadMedida($denominacion);

        $manager->persist($unidadMedida);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setUnidadMedida($manager, 'Unidad');
        $this->setUnidadMedida($manager, 'Metro');
        $this->setUnidadMedida($manager, 'Kilogramo');

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
