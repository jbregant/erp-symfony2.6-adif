<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\ConceptoPercepcion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion;

/**
 * LoadConceptoPercepcionData
 * 
 * @author Manuel Becerra
 * created 28/10/2014
 *
 */
class LoadConceptoPercepcionData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     */
    private function setConceptoPercepcion($manager, $denominacion, $estaActivo) {

        $conceptoPercepcion = new ConceptoPercepcion();

        $conceptoPercepcion->setDenominacion($denominacion);
        $conceptoPercepcion->setEstaActivo($estaActivo);

        $manager->persist($conceptoPercepcion);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setConceptoPercepcion($manager, ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA, false);
        $this->setConceptoPercepcion($manager, ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_GANANCIAS, false);
        $this->setConceptoPercepcion($manager, ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB, true);
        $this->setConceptoPercepcion($manager, ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_SUSS, false);

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
