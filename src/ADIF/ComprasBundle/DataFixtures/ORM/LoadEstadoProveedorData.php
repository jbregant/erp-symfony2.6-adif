<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoProveedor;

/**
 * LoadEstadoProveedorData
 * 
 * @author Manuel Becerra
 * created 12/07/2014
 *
 */
class LoadEstadoProveedorData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoProveedor($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoProveedor = new EstadoProveedor();

        $estadoProveedor->setDenominacionEstadoProveedor($denominacion);
        $estadoProveedor->setDescripcionEstadoProveedor($descripcion);
        $estadoProveedor->setTipoImportancia($tipoImportancia);
        $estadoProveedor->setEsEditable(false);

        $manager->persist($estadoProveedor);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoProveedor($manager, 'Activo', 'Activo', $this->getReference('Success'));
        $this->setEstadoProveedor($manager, 'Inactivo', 'Inactivo', $this->getReference('Default'));
        $this->setEstadoProveedor($manager, 'Suspendido', 'Suspendido', $this->getReference('Danger'));

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
