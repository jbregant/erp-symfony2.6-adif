<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoSolicitudCompra;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoSolicitud;

/**
 * LoadEstadoSolicitudCompraData
 * 
 * @author Manuel Becerra
 * created 14/07/2014
 *
 */
class LoadEstadoSolicitudCompraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoSolicitudCompra($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoSolicitudCompra = new EstadoSolicitudCompra();

        $estadoSolicitudCompra->setDenominacionEstadoSolicitudCompra($denominacion);
        $estadoSolicitudCompra->setDescripcionEstadoSolicitudCompra($descripcion);
        $estadoSolicitudCompra->setTipoImportancia($tipoImportancia);
        $estadoSolicitudCompra->setEsEditable(false);

        $manager->persist($estadoSolicitudCompra);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_CREADO, '', $this->getReference('Success'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR, '', $this->getReference('Default'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_ENVIO, '', $this->getReference('Warning'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_A_CORREGIR, '', $this->getReference('Default'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_AUTORIZACION, '', $this->getReference('Warning'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_APROBADA, '', $this->getReference('Info'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_ANULADA, '', $this->getReference('Danger'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_DESAPROBADA, '', $this->getReference('Danger'));
        $this->setEstadoSolicitudCompra($manager, ConstanteEstadoSolicitud::ESTADO_SOLICITUD_SUPERVISADA, '', $this->getReference('Success'));

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
