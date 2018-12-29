<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\TipoImportancia;

/**
 * LoadTipoImportanciaData
 * 
 * @author Manuel Becerra
 * created 13/07/2014
 *
 */
class LoadTipoImportanciaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $alias
     * @param type $color
     */
    private function setTipoImportancia($manager, $denominacion, $alias, $color) {

        $tipoImportancia = new TipoImportancia();

        $tipoImportancia->setDenominacionTipoImportancia($denominacion);
        $tipoImportancia->setAliasTipoImportancia($alias);
        $tipoImportancia->setColorTipoImportancia($color);

        $manager->persist($tipoImportancia);

        $this->addReference($denominacion, $tipoImportancia);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoImportancia($manager, 'Default', 'default', 'grey');
        $this->setTipoImportancia($manager, 'Primary', 'primary', 'purple');
        $this->setTipoImportancia($manager, 'Info', 'info', 'blue');
        $this->setTipoImportancia($manager, 'Success', 'success', 'green');
        $this->setTipoImportancia($manager, 'Danger', 'danger', 'red');
        $this->setTipoImportancia($manager, 'Warning', 'warning', 'yellow');

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
