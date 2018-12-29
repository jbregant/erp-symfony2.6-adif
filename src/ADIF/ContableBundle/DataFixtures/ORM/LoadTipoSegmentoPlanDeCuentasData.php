<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoSegmentoPlanDeCuentas;

/**
 * LoadTipoSegmentoPlanDeCuentasData
 * 
 * @author Manuel Becerra
 * created 06/10/2014
 *
 */
class LoadTipoSegmentoPlanDeCuentasData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoSegmento($manager, $denominacion, $descripcion) {

        $tipoSegmento = new TipoSegmentoPlanDeCuentas();

        $tipoSegmento->setDenominacionTipoSegmento($denominacion);
        $tipoSegmento->setDescripcionTipoSegmento($descripcion);

        $manager->persist($tipoSegmento);

        $this->addReference($denominacion, $tipoSegmento);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_NATURALEZA, '');
        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_LIQUIDEZ, '');
        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_CENTRO_COSTO, '');
        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_RUBRO, '');
        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_CATEGORIA, '');
        $this->setTipoSegmento($manager, ConstanteTipoSegmentoPlanDeCuentas::TIPO_SEGMENTO_SUBCATEGORIA, '');

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
