<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;

/**
 * LoadTipoImpuestoData
 * 
 * @author Manuel Becerra
 * created 06/11/2014
 *
 */
class LoadTipoImpuestoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoImpuesto($manager, $denominacion, $descripcion) {

        $tipoImpuesto = new TipoImpuesto();

        $tipoImpuesto->setDenominacion($denominacion);
        $tipoImpuesto->setDescripcion($descripcion);

        $manager->persist($tipoImpuesto);

        $this->addReference($denominacion, $tipoImpuesto);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoImpuesto($manager, ConstanteTipoImpuesto::Ganancias, 'Ganancias');
        $this->setTipoImpuesto($manager, ConstanteTipoImpuesto::IIBB, 'Ingresos Brutos');
        $this->setTipoImpuesto($manager, ConstanteTipoImpuesto::IVA, 'IVA');
        $this->setTipoImpuesto($manager, ConstanteTipoImpuesto::SUSS, 'SUSS');

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
