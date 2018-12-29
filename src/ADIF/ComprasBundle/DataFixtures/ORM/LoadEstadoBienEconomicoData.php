<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoBienEconomico;
use ADIF\ComprasBundle\Entity\EstadoBienEconomico;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadEstadoBienEconomicoData
 * 
 * @author Manuel Becerra
 * created 12/07/2014
 *
 */
class LoadEstadoBienEconomicoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoBienEconomico($manager, $denominacion, $descripcion, $tipoImportancia) {

        $estadoBienEconomico = new EstadoBienEconomico();

        $estadoBienEconomico->setDenominacionEstadoBienEconomico($denominacion);
        $estadoBienEconomico->setDescripcionEstadoBienEconomico($descripcion);
        $estadoBienEconomico->setTipoImportancia($tipoImportancia);
        $estadoBienEconomico->setEsEditable(false);

        $manager->persist($estadoBienEconomico);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoBienEconomico($manager, ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO, '', $this->getReference('Success'));
        $this->setEstadoBienEconomico($manager, ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_INACTIVO, '', $this->getReference('Default'));
        $this->setEstadoBienEconomico($manager, ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_PENDIENTE_CARGA, '', $this->getReference('Info'));

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
