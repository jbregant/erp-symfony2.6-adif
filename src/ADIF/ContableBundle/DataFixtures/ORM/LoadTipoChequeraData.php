<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoChequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoChequera;

/**
 * LoadTipoChequeraData
 * 
 * @author Manuel Becerra
 * created 08/10/2014
 *
 */
class LoadTipoChequeraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoChequera($manager, $denominacion, $descripcion) {

        $tipoChequera = new TipoChequera();

        $tipoChequera->setDenominacion($denominacion);
        $tipoChequera->setDescripcion($descripcion);

        $manager->persist($tipoChequera);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoChequera($manager, ConstanteTipoChequera::MANUAL_NORMAL, '');
        $this->setTipoChequera($manager, ConstanteTipoChequera::MANUAL_DIFERIDA, '');
        $this->setTipoChequera($manager, ConstanteTipoChequera::CONTINUA_NORMAL, '');
        $this->setTipoChequera($manager, ConstanteTipoChequera::CONTINUA_DIFERIDA, '');

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
