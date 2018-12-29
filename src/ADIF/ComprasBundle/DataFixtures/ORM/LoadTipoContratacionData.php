<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\TipoContratacion;

/**
 * LoadTipoContratacionData
 * 
 * @author Manuel Becerra
 * created 14/07/2014
 *
 */
class LoadTipoContratacionData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $requiereOC
     * @param type $montoDesde
     * @param type $montoHasta
     * @param type $cantidadMinimaOferentes
     */
    private function setTipoContacto($manager, $denominacion, $requiereOC, $montoDesde, $montoHasta, $cantidadMinimaOferentes) {

        $tipoContratacion = new TipoContratacion();

        $tipoContratacion->setDenominacionTipoContratacion($denominacion);
        $tipoContratacion->setRequiereOC($requiereOC);
        $tipoContratacion->setMontoDesde($montoDesde);
        $tipoContratacion->setMontoHasta($montoHasta);
        $tipoContratacion->setCantidadMinimaOferentes($cantidadMinimaOferentes);

        $manager->persist($tipoContratacion);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoContacto($manager, 'Contratación directa', true, 0, 6500, 0);
        $this->setTipoContacto($manager, 'Compulsa de precios o contratación directa', true, 6501, 19500, 2);
        $this->setTipoContacto($manager, 'Compulsa de precios o contratación directa', true, 19501, 26000, 2);
        $this->setTipoContacto($manager, 'Compulsa de precios o contratación directa', true, 26001, 130000, 3);
        $this->setTipoContacto($manager, 'Licitación o concurso privado', true, 130001, 3900000, 5);

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
