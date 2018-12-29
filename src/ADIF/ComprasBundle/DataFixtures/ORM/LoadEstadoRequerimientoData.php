<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoRequerimiento;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;

/**
 * LoadEstadoRequerimientoData
 * 
 * @author Manuel Becerra
 * created 31/07/2014
 *
 */
class LoadEstadoRequerimientoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoRequerimiento($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoSolicitudCompra = new EstadoRequerimiento();

        $estadoSolicitudCompra->setDenominacionEstadoRequerimiento($denominacion);
        $estadoSolicitudCompra->setDescripcionEstadoRequerimiento($descripcion);
        $estadoSolicitudCompra->setTipoImportancia($tipoImportancia);
        $estadoSolicitudCompra->setEsEditable(false);

        $manager->persist($estadoSolicitudCompra);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_CREADO, '', $this->getReference('Success'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_BORRADOR, '', $this->getReference('Default'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_ENVIO, '', $this->getReference('Warning'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_A_CORREGIR, '', $this->getReference('Default'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ANULADO, '', $this->getReference('Danger'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_APROBACION_CONTABLE, '', $this->getReference('Warning'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_DESAPROBADO, '', $this->getReference('Danger'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_COTIZACION, '', $this->getReference('Info'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_COTIZADO, '', $this->getReference('Success'));

        $this->setEstadoRequerimiento($manager, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ARCHIVADO, '', $this->getReference('Default'));

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
