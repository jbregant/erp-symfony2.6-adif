<?php

namespace ADIF\AutenticacionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\AutenticacionBundle\Entity\Grupo;

/**
 * LoadGrupoData
 * 
 * @author Manuel Becerra
 * created 14/10/2014
 *
 */
class LoadGrupoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $nombre
     * @param type $roles
     * @param type $descripcion
     */
    private function setGrupo($manager, $nombre, $roles, $descripcion) {

        $grupo = new Grupo();

        $grupo->setName($nombre);
        $grupo->setRoles($roles);
        $grupo->setDescripcion($descripcion);


        //$manager->persist($grupo);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setGrupo($manager, 'Envío de pedidos internos', //
                array('ROLE_COMPRAS_ENVIO_PEDIDO_INTERNO'), //
                'Permite crear y enviar pedidos internos.')
        ;

        $this->setGrupo($manager, 'Administración de pedidos internos', //
                array('ROLE_COMPRAS_ADMINISTRA_PEDIDO_INTERNO'), //
                'Permite administrar pedidos internos y crear solicitudes de compra a partir de los mismos.')
        ;

        $this->setGrupo($manager, 'Creación de solicitudes de compra', //
                array('ROLE_COMPRAS_CREACION_SOLICITUD'), //
                'Permite crear solicitudes de compra, pero NO enviarlas a aprobar.')
        ;

        $this->setGrupo($manager, 'Envío de solicitudes de compra', //
                array('ROLE_COMPRAS_ENVIO_SOLICITUD'), //
                'Permite enviar a aprobar las solicitudes de compra creadas por los usuarios de su área.')
        ;

        // Entidades autorizantes
        $this->setGrupo($manager, 'Entidad autorizante - Departamento de compras', //
                array('ROLE_COMPRAS_DEPARTAMENTO_COMPRAS'), //
                '')
        ;

        $this->setGrupo($manager, 'Entidad autorizante - Subgerente', //
                array('ROLE_COMPRAS_SUBGERENTE'), //
                '')
        ;

        $this->setGrupo($manager, 'Entidad autorizante - Gerente', //
                array('ROLE_COMPRAS_GERENTE'), //
                '')
        ;

        $this->setGrupo($manager, 'Entidad autorizante - Presidente', //
                array('ROLE_COMPRAS_PRESIDENTE'), //
                '')
        ;

        $this->setGrupo($manager, 'Entidad autorizante - Directorio', //
                array('ROLE_COMPRAS_DIRECTORIO'), //
                '')
        ;

        $this->setGrupo($manager, 'Entidad autorizante - Todas', //
                array('ROLE_COMPRAS_TODAS_ENTIDADES_AUTORIZANTES'), //
                '')
        ;
        // FIN entidades autorizantes

        $this->setGrupo($manager, 'Visar solicitudes de compra', //
                array('ROLE_COMPRAS_VISAR_SOLICITUD'), //
                'Permite visar las solicitudes de compra aprobadas.')
        ;

        $this->setGrupo($manager, 'Creación de requerimientos de compra', //
                array('ROLE_COMPRAS_CREACION_REQUERIMIENTO'), //
                'Permite crear requerimientos de compra, pero NO enviarlos a aprobar.')
        ;

        $this->setGrupo($manager, 'Envío de requerimientos de compra', //
                array('ROLE_COMPRAS_ENVIO_REQUERIMIENTO'), //
                'Permite enviar a aprobar los requerimientos de compra.')
        ;

        $this->setGrupo($manager, 'Aprobar contablemente requerimientos de compra', //
                array('ROLE_COMPRAS_APROBACION_CONTABLE_REQUERIMIENTO'), //
                'Permite aprobar contablemente los requerimientos de compra enviados.')
        ;

        $this->setGrupo($manager, 'Anular orden de compra', //
                array('ROLE_COMPRAS_ANULAR_ORDEN_COMPRA'), //
                'Permite anular una orden de compra.')
        ;

        //$manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 2;
    }

}
