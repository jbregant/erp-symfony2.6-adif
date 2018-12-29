<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoRenglonSolicitudCompra;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonSolicitud;

/**
 * LoadEstadoRenglonSolicitudCompraData
 * 
 * @author Manuel Becerra
 * created 14/07/2014
 *
 */
class LoadEstadoRenglonSolicitudCompraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoRenglonSolicitudCompra($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoRenglonSolicitudCompra = new EstadoRenglonSolicitudCompra();

        $estadoRenglonSolicitudCompra->setDenominacionEstadoRenglonSolicitudCompra($denominacion);
        $estadoRenglonSolicitudCompra->setDescripcionEstadoRenglonSolicitudCompra($descripcion);
        $estadoRenglonSolicitudCompra->setTipoImportancia($tipoImportancia);
        $estadoRenglonSolicitudCompra->setEsEditable(false);

        $manager->persist($estadoRenglonSolicitudCompra);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CREADO, '', $this->getReference('Success'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_BORRADOR, '', $this->getReference('Default'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_PENDIENTE_ENVIO, '', $this->getReference('Warning'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_A_CORREGIR, '', $this->getReference('Default'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_PENDIENTE_AUTORIZACION, '', $this->getReference('Warning'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_ANULADO, '', $this->getReference('Danger'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_APROBADO, '', $this->getReference('Success'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_DESAPROBADO, '', $this->getReference('Danger'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO, '', $this->getReference('Success'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_PARCIAL, '', $this->getReference('Info'));
        $this->setEstadoRenglonSolicitudCompra($manager, ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_TOTAL, '', $this->getReference('Info'));

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
