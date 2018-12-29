<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\Facturacion\UnidadTiempo;
use ADIF\ContableBundle\Entity\Constantes\ConstanteUnidadTiempo;

/**
 * LoadUnidadTiempoData
 * 
 * @author Manuel Becerra
 * created 03/02/2015
 *
 */
class LoadUnidadTiempoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     * @param type $cantidadSemanas
     */
    private function setUnidadTiempo($manager, $codigo, $denominacion, $cantidadSemanas) {

        $unidadTiempo = new UnidadTiempo();

        $unidadTiempo->setCodigo($codigo);
        $unidadTiempo->setDenominacion($denominacion);
        $unidadTiempo->setCantidadSemanas($cantidadSemanas);

        $manager->persist($unidadTiempo);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setUnidadTiempo($manager, ConstanteUnidadTiempo::MES, 'Mes', 4);
        $this->setUnidadTiempo($manager, ConstanteUnidadTiempo::ANIO, 'Año', 48);

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
