<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas;

/**
 * LoadSegmentoPlanDeCuentasData
 * 
 * @author Manuel Becerra
 * created 24/06/2014
 *
 */
class LoadSegmentoPlanDeCuentasData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $posicion
     * @param type $longitud
     * @param type $separador
     * @param type $indicaCentroDeCosto
     * @param type $denominacionSegmento
     */
    private function setSegmento($manager, $posicion, $longitud, $separador, $indicaCentroDeCosto, $denominacionSegmento) {

        $segmento = new SegmentoPlanDeCuentas();

        $segmento->setPlanDeCuentas($this->getReference('planDeCuentas'));
        $segmento->setPosicion($posicion);
        $segmento->setLongitud($longitud);
        $segmento->setSeparador($separador);
        $segmento->setIndicaCentroDeCosto($indicaCentroDeCosto);
        $segmento->setDenominacionSegmento($denominacionSegmento);

        $manager->persist($segmento);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setSegmento($manager, 1, 1, '.', false, null);
        $this->setSegmento($manager, 2, 1, '.', false, null);
        $this->setSegmento($manager, 3, 2, '.', true, null);
        $this->setSegmento($manager, 4, 2, '.', false, null);
        $this->setSegmento($manager, 5, 2, '.', false, null);
        $this->setSegmento($manager, 6, 2, '.', false, null);
        $this->setSegmento($manager, 7, 2, '.', false, null);
        $this->setSegmento($manager, 8, 2, null, false, null);

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
