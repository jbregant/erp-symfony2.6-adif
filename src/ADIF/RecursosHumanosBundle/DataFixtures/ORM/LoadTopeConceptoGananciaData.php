<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia;

/**
 * LoadTopeConceptoGananciaData
 * 
 * @author Manuel Becerra
 * created 24/07/2014
 *
 */
class LoadTopeConceptoGananciaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $rangoRemuneracion
     * @param type $conceptoGanancia
     * @param type $valorTope
     * @param type $esPorcentaje
     */
    private function setTopeConceptoGanancia($manager, $rangoRemuneracion, $conceptoGanancia, $valorTope, $esPorcentaje) {

        $topeConceptoGanancia = new TopeConceptoGanancia();

        $topeConceptoGanancia->setRangoRemuneracion($rangoRemuneracion);
        $topeConceptoGanancia->setConceptoGanancia($conceptoGanancia);
        $topeConceptoGanancia->setValorTope($valorTope);
        $topeConceptoGanancia->setEsPorcentaje($esPorcentaje);
        $topeConceptoGanancia->setEsValorAnual(true);

        $manager->persist($topeConceptoGanancia);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        /**         * ** RangoRemuneracion 1 -> 0 - 15000 ******** */
        $rangoRemuneracion = $this->getReference('RangoRemuneracion 1');

        // Mínimo no imponible
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Mínimo no imponible'), 20217.60, false);

        // Cónyuge
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Cónyuge'), 22464, false);

        // Hijos
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Hijos'), 11232, false);

        // Otras cargas de familia
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Otras cargas de familia'), 8424, false);

        // Deducción especial
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Deducción especial'), 97044.48, false);

        // Servicio doméstico
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Servicio doméstico'), 20217.60, false);

        // Deducciones Generales
        $this->setDeduccionesGenerales($manager, $rangoRemuneracion);




        /**         * ** RangoRemuneracion 2 -> 15001 - 25000 ******** */
        $rangoRemuneracion = $this->getReference('RangoRemuneracion 2');

        // Mínimo no imponible
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Mínimo no imponible'), 18662.40, false);

        // Cónyuge
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Cónyuge'), 20736, false);

        // Hijos
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Hijos'), 10368, false);

        // Otras cargas de familia
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Otras cargas de familia'), 7776, false);

        // Deducción especial
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Deducción especial'), 89579.52, false);

        // Servicio doméstico
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Servicio doméstico'), 18662.40, false);

        // Deducciones Generales
        $this->setDeduccionesGenerales($manager, $rangoRemuneracion);




        /**         * ** RangoRemuneracion 3 -> 25001 - INFINITO ******** */
        $rangoRemuneracion = $this->getReference('RangoRemuneracion 3');


        // Mínimo no imponible
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Mínimo no imponible'), 15552, false);

        // Cónyuge
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Cónyuge'), 17280, false);

        // Hijos
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Hijos'), 8640, false);

        // Otras cargas de familia
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Otras cargas de familia'), 6480, false);

        // Deducción especial
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Deducción especial'), 74649.60, false);

        // Servicio doméstico
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Servicio doméstico'), 15552, false);

        // Deducciones Generales
        $this->setDeduccionesGenerales($manager, $rangoRemuneracion);



        $manager->flush();
    }

    /**
     * 
     * @param type $manager
     * @param type $rangoRemuneracion
     */
    private function setDeduccionesGenerales($manager, $rangoRemuneracion) {

        // Intereses de créditos hipotecarios
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Intereses de créditos hipotecarios'), 20000, false);

        // Primas de seguro de vida
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Primas de seguro de vida'), 996.12, false);

        // Donaciones
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Donaciones'), 0.05, true);

        // Honorarios de servicios de asistencia sanitaria, médica y paramédica
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Honorarios de servicios de asistencia sanitaria, médica y paramédica'), 0.05, true);

        // Cuota médica asistencial
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Cuota médica asistencial'), 0.05, true);

        // Gastos de sepelio
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Gastos de sepelio'), 996.12, false);

        // Aportes jubilatorios
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Aportes jubilatorios'), 9999999, false);

        // Aporte a la obra social
        $this->setTopeConceptoGanancia($manager, $rangoRemuneracion, //
                $this->getReference('Aporte a la obra social'), 9999999, false);
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 3;
    }

}
