<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\EstadoCliente;

/**
 * LoadEstadoClienteData
 * 
 * @author Manuel Becerra
 * created 10/10/2014
 *
 */
class LoadEstadoClienteData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     * @param type $tipoImportancia
     */
    private function setEstadoCliente($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoCliente = new EstadoCliente();

        $estadoCliente->setDenominacionEstado($denominacion);
        $estadoCliente->setDescripcionEstado($descripcion);
        $estadoCliente->setTipoImportancia($tipoImportancia);
        $estadoCliente->setEsEditable(false);

        $manager->persist($estadoCliente);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoCliente($manager, 'Activo', 'Activo', $this->getReference('Success'));
        $this->setEstadoCliente($manager, 'Inactivo', 'Inactivo', $this->getReference('Default'));
        $this->setEstadoCliente($manager, 'Suspendido', 'Suspendido', $this->getReference('Danger'));

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
