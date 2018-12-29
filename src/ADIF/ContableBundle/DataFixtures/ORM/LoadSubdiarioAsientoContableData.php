<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\SubdiarioAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteSubdiarioAsientoContable;

/**
 * LoadSubdiarioAsientoContableData
 * 
 * @author Manuel Becerra
 * created 23/10/2014
 *
 */
class LoadSubdiarioAsientoContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setSubdiarioAsientoContable($manager, $denominacion, $descripcion) {

        $subdiarioAsientoContable = new SubdiarioAsientoContable();

        $subdiarioAsientoContable->setDenominacion($denominacion);
        $subdiarioAsientoContable->setDescripcion($descripcion);

        $manager->persist($subdiarioAsientoContable);

        $this->addReference($denominacion, $subdiarioAsientoContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setSubdiarioAsientoContable($manager, ConstanteSubdiarioAsientoContable::SUBDIARIO_COMPRAS, '');
        $this->setSubdiarioAsientoContable($manager, ConstanteSubdiarioAsientoContable::SUBDIARIO_VENTAS, '');
        $this->setSubdiarioAsientoContable($manager, ConstanteSubdiarioAsientoContable::SUBDIARIO_CAJA_BANCOS, '');
        $this->setSubdiarioAsientoContable($manager, ConstanteSubdiarioAsientoContable::SUBDIARIO_CONTABILIDAD_GENERAL, '');
        $this->setSubdiarioAsientoContable($manager, ConstanteSubdiarioAsientoContable::SUBDIARIO_OBRAS, '');

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
