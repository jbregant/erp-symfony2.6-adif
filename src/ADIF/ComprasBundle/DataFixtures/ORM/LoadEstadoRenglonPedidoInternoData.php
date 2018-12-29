<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoRenglonPedidoInterno;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonPedidoInterno;

/**
 * LoadEstadoRenglonPedidoInternoData
 * 
 * @author Manuel Becerra
 * created 18/09/2014
 *
 */
class LoadEstadoRenglonPedidoInternoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoRenglonPedidoInterno($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoRenglonPedidoInterno = new EstadoRenglonPedidoInterno();

        $estadoRenglonPedidoInterno->setDenominacionEstadoRenglonPedidoInterno($denominacion);
        $estadoRenglonPedidoInterno->setDescripcionEstadoRenglonPedidoInterno($descripcion);
        $estadoRenglonPedidoInterno->setTipoImportancia($tipoImportancia);
        $estadoRenglonPedidoInterno->setEsEditable(false);

        $manager->persist($estadoRenglonPedidoInterno);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoRenglonPedidoInterno($manager, ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_BORRADOR, '', $this->getReference('Default'));
        $this->setEstadoRenglonPedidoInterno($manager, ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_ENVIADO, '', $this->getReference('Warning'));
        $this->setEstadoRenglonPedidoInterno($manager, ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_CON_STOCK, '', $this->getReference('Success'));
        $this->setEstadoRenglonPedidoInterno($manager, ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_SOLICITADO, '', $this->getReference('Info'));

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
