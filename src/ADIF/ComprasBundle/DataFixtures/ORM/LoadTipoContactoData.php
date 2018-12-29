<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\TipoContacto;

/**
 * LoadTipoContactoData
 * 
 * @author Manuel Becerra
 * created 10/07/2014
 *
 */
class LoadTipoContactoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoContacto($manager, $denominacion, $descripcion) {

        $tipoContacto = new TipoContacto();

        $tipoContacto->setDenominacionTipoContacto($denominacion);
        $tipoContacto->setDescripcionTipoContacto($descripcion);

        $manager->persist($tipoContacto);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoContacto($manager, 'Teléfono', 'Teléfono');
        $this->setTipoContacto($manager, 'Celular', 'Celular');
        $this->setTipoContacto($manager, 'Email', 'Email');
        $this->setTipoContacto($manager, 'Fax', 'Fax');
        $this->setTipoContacto($manager, 'Página Web', 'Página Web');

        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 1;
    }

}
