<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoAsientoContable;

/**
 * LoadTipoAsientoContableData
 * 
 * @author Manuel Becerra
 * created 30/09/2014
 *
 */
class LoadTipoAsientoContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoAsientoContable($manager, $denominacion, $descripcion) {

        $tipoAsientoContable = new TipoAsientoContable();

        $tipoAsientoContable->setDenominacion($denominacion);
        $tipoAsientoContable->setDescripcion($descripcion);

        $manager->persist($tipoAsientoContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoAsientoContable($manager, ConstanteTipoAsientoContable::TIPO_ASIENTO_MANUAL, '');
        $this->setTipoAsientoContable($manager, ConstanteTipoAsientoContable::TIPO_ASIENTO_SEMI_AUTOMATICO, '');
        $this->setTipoAsientoContable($manager, ConstanteTipoAsientoContable::TIPO_ASIENTO_AUTOMATICO, '');

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
