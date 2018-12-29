<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoTramo;

/**
 * LoadEstadoTramoData
 * 
 * @author Manuel Becerra
 * created 04/06/2015
 *
 */
class LoadEstadoTramoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     * @param type $generaAsientoObraFinalizada
     */
    private function setData($manager, $codigo, $denominacion, $generaAsientoObraFinalizada) {

        $entity = new \ADIF\ContableBundle\Entity\Obras\EstadoTramo();

        $entity->setCodigo($codigo);
        $entity->setDenominacionEstado($denominacion);
        $entity->setEsEditable(!$generaAsientoObraFinalizada);
        $entity->setGeneraAsientoObraFinalizada($generaAsientoObraFinalizada);

        $manager->persist($entity);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setData($manager, ConstanteEstadoTramo::ESTADO_IDEA, 'Idea', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_EN_RELEVAMIENTO, 'En relevamiento', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_EN_LICITACION, 'En licitación', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_EN_EJECUCION, 'En ejecución', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_POSTERGADO, 'Postergado', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_CANCELADO, 'Cancelado', false);
        $this->setData($manager, ConstanteEstadoTramo::ESTADO_FINALIZADO, 'Finalizado', true);

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
