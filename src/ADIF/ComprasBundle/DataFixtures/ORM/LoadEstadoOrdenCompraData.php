<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoOrdenCompra;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoOrdenCompra;

/**
 * LoadEstadoOrdenCompraData
 * 
 * @author Manuel Becerra
 * created 03/12/2014
 *
 */
class LoadEstadoOrdenCompraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoOrdenCompra($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoOrdenCompra = new EstadoOrdenCompra();

        $estadoOrdenCompra->setDenominacionEstado($denominacion);
        $estadoOrdenCompra->setDescripcionEstado($descripcion);
        $estadoOrdenCompra->setTipoImportancia($tipoImportancia);
        $estadoOrdenCompra->setEsEditable(false);

        $manager->persist($estadoOrdenCompra);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoOrdenCompra($manager, ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR, '', $this->getReference('Default'));
        $this->setEstadoOrdenCompra($manager, ConstanteEstadoOrdenCompra::ESTADO_OC_GENERADA, '', $this->getReference('Success'));
        $this->setEstadoOrdenCompra($manager, ConstanteEstadoOrdenCompra::ESTADO_OC_CANCELADA, '', $this->getReference('Danger'));
        $this->setEstadoOrdenCompra($manager, ConstanteEstadoOrdenCompra::ESTADO_OC_ANULADA, '', $this->getReference('Danger'));

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
