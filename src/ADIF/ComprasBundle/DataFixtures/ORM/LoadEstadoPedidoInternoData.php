<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoPedidoInterno;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoPedidoInterno;

/**
 * LoadEstadoPedidoInternoData
 * 
 * @author Manuel Becerra
 * created 18/09/2014
 *
 */
class LoadEstadoPedidoInternoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoPedidoInterno($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoPedidoInterno = new EstadoPedidoInterno();

        $estadoPedidoInterno->setDenominacionEstadoPedidoInterno($denominacion);
        $estadoPedidoInterno->setDescripcionEstadoPedidoInterno($descripcion);
        $estadoPedidoInterno->setTipoImportancia($tipoImportancia);
        $estadoPedidoInterno->setEsEditable(false);

        $manager->persist($estadoPedidoInterno);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoPedidoInterno($manager, ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_BORRADOR, '', $this->getReference('Default'));
        $this->setEstadoPedidoInterno($manager, ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_ENVIADO, '', $this->getReference('Warning'));
        $this->setEstadoPedidoInterno($manager, ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_CON_STOCK, '', $this->getReference('Success'));

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
