<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoMoneda;

/**
 * LoadTipoMonedaData
 * 
 * @author Manuel Becerra
 * created 25/06/2014
 *
 */
class LoadTipoMonedaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $simbolo
     * @param type $denominacion
     * @param type $descripcion
     * @param type $esMCL
     */
    private function setTipoMoneda($manager, $codigo, $simbolo, $denominacion, $descripcion, $esMCL = false) {

        $tipoMoneda = new TipoMoneda();

        $tipoMoneda->setCodigoTipoMoneda($codigo);
        $tipoMoneda->setSimboloTipoMoneda($simbolo);
        $tipoMoneda->setDenominacionTipoMoneda($denominacion);
        $tipoMoneda->setDescripcionTipoMoneda($descripcion);
        $tipoMoneda->setEsMCL($esMCL);

        $manager->persist($tipoMoneda);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoMoneda($manager, 'ARS', '$', 'Peso Argentino', null, true);
        $this->setTipoMoneda($manager, 'BRL', 'R$', 'Real Brasileño', null);
        $this->setTipoMoneda($manager, 'EUR', '€', 'Euro', null);
        $this->setTipoMoneda($manager, 'USD', 'U$D', 'Dólar Estadounidense', null);

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
