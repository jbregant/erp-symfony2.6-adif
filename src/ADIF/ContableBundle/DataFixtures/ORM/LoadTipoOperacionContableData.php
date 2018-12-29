<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoOperacionContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;

/**
 * LoadTipoOperacionContableData
 * 
 * @author Manuel Becerra
 * created 02/10/2014
 *
 */
class LoadTipoOperacionContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoOperacionContable($manager, $denominacion, $descripcion) {

        $tipoOperacionContable = new TipoOperacionContable();

        $tipoOperacionContable->setDenominacion($denominacion);
        $tipoOperacionContable->setDescripcion($descripcion);

        $manager->persist($tipoOperacionContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoOperacionContable($manager, ConstanteTipoOperacionContable::DEBE, '');
        $this->setTipoOperacionContable($manager, ConstanteTipoOperacionContable::HABER, '');

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
