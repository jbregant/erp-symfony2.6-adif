<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia;

/**
 * LoadTipoConceptoGananciaData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadTipoConceptoGananciaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $ordenAplicacion
     */
    private function setTipoConceptoGanancia($manager, $denominacion, $ordenAplicacion) {

        $tipoConceptoGanancia = new TipoConceptoGanancia();

        $tipoConceptoGanancia->setDenominacion($denominacion);
        $tipoConceptoGanancia->setOrdenAplicacion($ordenAplicacion);

        $manager->persist($tipoConceptoGanancia);

        $this->addReference($denominacion, $tipoConceptoGanancia);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoConceptoGanancia($manager, 'Otros Ingresos', 0);
        $this->setTipoConceptoGanancia($manager, 'Deducciones Generales', 1);
        $this->setTipoConceptoGanancia($manager, 'Resultado Neto', 2);
        $this->setTipoConceptoGanancia($manager, 'Diferencia', 3);

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
