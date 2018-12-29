<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;

/**
 * LoadEstadoEgresoValorData
 * 
 * @author Manuel Becerra
 * created 15/01/2015
 *
 */
class LoadEstadoEgresoValorData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setEstadoEgresoValor($manager, $codigo, $denominacion, $descripcion) {

        $estadoEgresoValor = new EstadoEgresoValor();

        $estadoEgresoValor->setCodigo($codigo);
        $estadoEgresoValor->setDenominacionEstado($denominacion);
        $estadoEgresoValor->setDescripcionEstado($descripcion);

        $manager->persist($estadoEgresoValor);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoEgresoValor($manager, ConstanteEstadoEgresoValor::ESTADO_INGRESADO, 'Ingresado', '');
        $this->setEstadoEgresoValor($manager, ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE, 'Con autorización contable', '');
        $this->setEstadoEgresoValor($manager, ConstanteEstadoEgresoValor::ESTADO_ACTIVO, 'Activo', '');

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
