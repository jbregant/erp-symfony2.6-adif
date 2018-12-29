<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;

/**
 * LoadConceptoGananciaData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadConceptoGananciaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param \ADIF\RecursosHumanosBundle\DataFixtures\ORM\TipoConceptoGanancia $conceptoGanancia
     * @param type $denominacion
     * @param type $aplicaEnFormulario572
     */
    private function setConceptoGanancia($manager, $tipoConceptoGanancia, $denominacion, $aplicaEnFormulario572, $esCantidad, $indicaSAC = false, $aplicaGananciaAnual = false) {

        $conceptoGanancia = new ConceptoGanancia();

        $conceptoGanancia->setTipoConceptoGanancia($tipoConceptoGanancia);
        $conceptoGanancia->setDenominacion($denominacion);
        $conceptoGanancia->setAplicaEnFormulario572($aplicaEnFormulario572);
        $conceptoGanancia->setEsCargaFamiliar($esCantidad);
        $conceptoGanancia->setIndicaSAC($indicaSAC);
        $conceptoGanancia->setAplicaGananciaAnual($aplicaGananciaAnual);

        $manager->persist($conceptoGanancia);

        $this->addReference($denominacion, $conceptoGanancia);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {


        // Otros Ingresos
        $otrosIngresos = $this->getReference('Otros Ingresos');

        $this->setConceptoGanancia($manager, $otrosIngresos, 'SAC primer semestre', true, false, true);
        $this->setConceptoGanancia($manager, $otrosIngresos, 'Remuneraciones informadas de otro empleador', true, false, false);
        $this->setConceptoGanancia($manager, $otrosIngresos, 'Ajustes de ganancia', true, false, false);


        // Deducciones Generales
        $deduccionesGenerales = $this->getReference('Deducciones Generales');

        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Primas de seguro de vida', true, false, false);
        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Gastos de sepelio', true, false, false);
        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Seguros de retiro', true, false, false);
        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Servicio doméstico', true, false, false);
        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Aportes jubilatorios', true, false, false);
        $this->setConceptoGanancia($manager, $deduccionesGenerales, 'Aporte a la obra social', true, false, false);

        // Resultado Neto
        $resultadoNeto = $this->getReference('Resultado Neto');

        $this->setConceptoGanancia($manager, $resultadoNeto, 'Cuota médica asistencial', true, false, false);
        $this->setConceptoGanancia($manager, $resultadoNeto, 'Donaciones', true, false, false);
        $this->setConceptoGanancia($manager, $resultadoNeto, 'Honorarios de servicios de asistencia sanitaria, médica y paramédica', true, false, false, true);
        $this->setConceptoGanancia($manager, $resultadoNeto, 'Intereses de créditos hipotecarios', true, false, false);

        // Diferencia
        $diferencia = $this->getReference('Diferencia');

        $this->setConceptoGanancia($manager, $diferencia, 'Deducción especial', false, false, false);
        $this->setConceptoGanancia($manager, $diferencia, 'Mínimo no imponible', false, false, false);
        $this->setConceptoGanancia($manager, $diferencia, 'Cónyuge', true, true, false);
        $this->setConceptoGanancia($manager, $diferencia, 'Hijos', true, true, false);
        $this->setConceptoGanancia($manager, $diferencia, 'Otras cargas de familia', true, true, false);

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
