<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\Facturacion\EstadoContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;

/**
 * LoadEstadoContratoData
 * 
 * @author Manuel Becerra
 * created 03/02/2015
 *
 */
class LoadEstadoContratoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     */
    private function setEstadoContrato($manager, $codigo, $denominacion) {

        $estadoContrato = new EstadoContrato();

        $estadoContrato->setCodigo($codigo);
        $estadoContrato->setDenominacionEstado($denominacion);

        $manager->persist($estadoContrato);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setEstadoContrato($manager, ConstanteEstadoContrato::ACTIVO_OK, 'Activo - OK');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::ACTIVO_COMENTADO, 'Activo - Comentado');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::INACTIVO_PENDIENTE_REGULARIZAR, 'Inactivo - Pendiente regularizar');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::INACTIVO_PENDIENTE_ESCRITURACION, 'Inactivo - Pendiente escrituración');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::INACTIVO_EN_ACCION_JUDICIAL, 'Inactivo - En acción judicial');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::INACTIVO_CON_DEUDA, 'Inactivo - Con deuda');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::INACTIVO_RESCINDIDO, 'Inactivo - Rescindido');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::FINALIZADO, 'Finalizado');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::ADENDADO, 'Adendado');
        $this->setEstadoContrato($manager, ConstanteEstadoContrato::PRORROGADO, 'Prorrogado');

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
