<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\RecursosHumanosBundle\Entity\EscalaImpuesto;

/**
 * LoadEscalaImpuestoData
 * 
 * @author Manuel Becerra
 * created 24/07/2014
 *
 */
class LoadEscalaImpuestoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $mes
     * @param type $montoDesde
     * @param type $montoHasta
     * @param type $montoFijo
     * @param type $porcentajeASumar
     */
    private function setEscalaImpuesto($manager, $mes, $montoDesde, $montoHasta, $montoFijo, $porcentajeASumar) {

        $escalaImpuesto = new EscalaImpuesto();

        $escalaImpuesto->setMes($mes);
        $escalaImpuesto->setMontoDesde($montoDesde);
        $escalaImpuesto->setMontoHasta($montoHasta);
        $escalaImpuesto->setMontoFijo($montoFijo);
        $escalaImpuesto->setPorcentajeASumar($porcentajeASumar);

        $manager->persist($escalaImpuesto);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // ENERO
        $mes = 1;

        $this->setEscalaImpuesto($manager, $mes, 0, 833.33, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 833.33, 1666.67, 75, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 1666.67, 2500, 191.67, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 2500, 5000, 350, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 5000, 7500, 925, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 7500, 10000, 1600, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 10000, 9999999, 2375, 0.35);

        // FEBRERO
        $mes = 2;

        $this->setEscalaImpuesto($manager, $mes, 0, 1666.67, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 1666.67, 3333.33, 150, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 3333.33, 5000, 383.33, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 5000, 10000, 700, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 10000, 15000, 1850, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 15000, 20000, 3200, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 20000, 9999999, 4750, 0.35);

        // MARZO
        $mes = 3;

        $this->setEscalaImpuesto($manager, $mes, 0, 2500, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 2500, 5000, 225, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 5000, 7500, 575, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 7500, 15000, 1050, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 15000, 22500, 2775, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 22500, 30000, 4800, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 30000, 9999999, 7125, 0.35);

        // ABRIL
        $mes = 4;

        $this->setEscalaImpuesto($manager, $mes, 0, 3333.33, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 3333.33, 6666.67, 300, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 6666.67, 10000, 766.67, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 10000, 20000, 1400, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 20000, 30000, 3700, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 30000, 40000, 6400, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 40000, 9999999, 9500, 0.35);

        // MAYO
        $mes = 5;

        $this->setEscalaImpuesto($manager, $mes, 0, 4166.67, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 4166.67, 8333.33, 375, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 8333.33, 12500, 958.33, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 12500, 25000, 1750, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 25000, 37500, 4625, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 37500, 50000, 8000, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 50000, 9999999, 11875, 0.35);

        // JUNIO
        $mes = 6;

        $this->setEscalaImpuesto($manager, $mes, 0, 5000, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 5000, 10000, 450, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 10000, 15000, 1150, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 15000, 30000, 2100, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 30000, 45000, 5550, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 45000, 60000, 9600, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 60000, 9999999, 14250, 0.35);

        // JULIO
        $mes = 7;

        $this->setEscalaImpuesto($manager, $mes, 0, 5833.33, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 5833.33, 11666.67, 525, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 11666.67, 17500, 1341.67, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 17500, 35000, 2450, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 35000, 52500, 6475, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 52500, 70000, 11200, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 70000, 9999999, 16625, 0.35);

        // AGOSTO
        $mes = 8;

        $this->setEscalaImpuesto($manager, $mes, 0, 6666.67, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 6666.67, 13333.33, 600, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 13333.33, 20000, 1533.33, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 20000, 40000, 2800, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 40000, 60000, 7400, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 60000, 80000, 12800, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 80000, 9999999, 19000, 0.35);

        // SEPTIEMBRE
        $mes = 9;

        $this->setEscalaImpuesto($manager, $mes, 0, 7500, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 7500, 15000, 675, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 15000, 22500, 1725, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 22500, 45000, 3150, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 45000, 67500, 8325, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 67500, 90000, 14400, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 90000, 9999999, 21375, 0.35);

        // OCTUBRE
        $mes = 10;

        $this->setEscalaImpuesto($manager, $mes, 0, 8333.33, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 8333.33, 16666.67, 750, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 16666.67, 25000, 1916.67, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 25000, 50000, 3500, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 50000, 75000, 9250, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 75000, 100000, 16000, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 100000, 9999999, 23750, 0.35);

        // NOVIEMBRE
        $mes = 11;

        $this->setEscalaImpuesto($manager, $mes, 0, 9166.67, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 9166.67, 18333.33, 825, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 18333.33, 27500, 2108.33, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 27500, 55000, 3850, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 55000, 82500, 10175, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 82500, 110000, 17600, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 110000, 9999999, 26125, 0.35);

        // DICIEMBRE
        $mes = 12;

        $this->setEscalaImpuesto($manager, $mes, 0, 10000, 0, 0.09);
        $this->setEscalaImpuesto($manager, $mes, 10000, 20000, 900, 0.14);
        $this->setEscalaImpuesto($manager, $mes, 20000, 30000, 2300, 0.19);
        $this->setEscalaImpuesto($manager, $mes, 30000, 60000, 4200, 0.23);
        $this->setEscalaImpuesto($manager, $mes, 60000, 90000, 11100, 0.27);
        $this->setEscalaImpuesto($manager, $mes, 90000, 120000, 19200, 0.31);
        $this->setEscalaImpuesto($manager, $mes, 120000, 9999999, 28500, 0.35);


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
