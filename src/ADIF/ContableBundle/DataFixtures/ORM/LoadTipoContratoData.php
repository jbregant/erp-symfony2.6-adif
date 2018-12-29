<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCategoriaContrato;

/**
 * LoadTipoContratoData
 * 
 * @author Manuel Becerra
 * created 23/01/2015
 *
 */
class LoadTipoContratoData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     */
    private function setTipoContratoValor($manager, $codigo, $denominacion) {

        $tipoContrato = new CategoriaContrato();

        $tipoContrato->setCodigo($codigo);
        $tipoContrato->setDenominacion($denominacion);

        $manager->persist($tipoContrato);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoContratoValor($manager, ConstanteCategoriaContrato::CONTRATO_ORIGINAL, 'Original');
        $this->setTipoContratoValor($manager, ConstanteCategoriaContrato::ADENDA, 'Adenda');
        $this->setTipoContratoValor($manager, ConstanteCategoriaContrato::PRORROGA, 'Prorroga');

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
