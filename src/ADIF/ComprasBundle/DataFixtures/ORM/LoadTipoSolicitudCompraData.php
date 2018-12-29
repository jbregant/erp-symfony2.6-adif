<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\TipoSolicitudCompra;

/**
 * LoadTipoSolicitudCompraData
 * 
 * @author Manuel Becerra
 * created 14/07/2014
 *
 */
class LoadTipoSolicitudCompraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setTipoSolicitudCompra($manager, $denominacion, $descripcion, $tipoImportancia) {

        $tipoSolicitudCompra = new TipoSolicitudCompra();

        $tipoSolicitudCompra->setDenominacionTipoSolicitudCompra($denominacion);
        $tipoSolicitudCompra->setDescripcionTipoSolicitudCompra($descripcion);
        $tipoSolicitudCompra->setTipoImportancia($tipoImportancia);

        $manager->persist($tipoSolicitudCompra);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoSolicitudCompra($manager, 'Compra Particular', 'Compra Particular', $this->getReference('Default'));
        $this->setTipoSolicitudCompra($manager, 'Compra Anual', 'Compra Anual', $this->getReference('Success'));

        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 3;
    }

}
