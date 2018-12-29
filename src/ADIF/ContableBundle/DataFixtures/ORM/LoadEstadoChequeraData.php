<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\EstadoChequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoChequera;

/**
 * LoadEstadoChequeraData
 * 
 * @author Manuel Becerra
 * created 07/10/2014
 *
 */
class LoadEstadoChequeraData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoChequera($manager, $denominacion, $descripcion) {

        $estadoChequera = new EstadoChequera();

        $estadoChequera->setDenominacionEstado($denominacion);
        $estadoChequera->setDescripcionEstado($descripcion);

        $manager->persist($estadoChequera);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_INGRESADA, '');
        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_HABILITADA_ACTIVA, '');
        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_HABILITADA_INACTIVA, '');
        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_DESHABILITADA, '');
        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_CERRADA, '');
        $this->setEstadoChequera($manager, ConstanteEstadoChequera::ESTADO_CHEQUERA_AGOTADA, '');

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
