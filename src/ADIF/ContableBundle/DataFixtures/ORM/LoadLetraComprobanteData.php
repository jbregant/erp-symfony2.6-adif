<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\LetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;

/**
 * LoadLetraComprobanteData
 * 
 * @author Manuel Becerra
 * created 23/10/2014
 *
 */
class LoadLetraComprobanteData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $letra
     */
    private function setLetraComprobante($manager, $letra) {

        $letraComprobante = new LetraComprobante();

        $letraComprobante->setLetra($letra);

        $manager->persist($letraComprobante);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setLetraComprobante($manager, ConstanteLetraComprobante::A);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::A_CON_LEYENDA);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::B);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::C);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::E);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::M);
        $this->setLetraComprobante($manager, ConstanteLetraComprobante::Y);

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
