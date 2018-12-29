<?php

namespace ADIF\AutenticacionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\AutenticacionBundle\Entity\Empresa;

/**
 * LoadEmpresaData
 * 
 * @author Gustavo Luis
 * created 08/08/2017
 *
 */
class LoadEmpresaData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) 
	{
        $empresa = new Empresa();
		$empresa->setDenominacion('Adifse');
		$empresa->setCuit('30-71069599-3');
		$manager->persist($empresa);
		
		$empresa = new Empresa();
		$empresa->setDenominacion('Fase');
		$empresa->setCuit(null);
		$manager->persist($empresa);
		
		$empresa = new Empresa();
		$empresa->setDenominacion('Consorcio Vossloh');
		$empresa->setCuit('30-71245914-6');
		$manager->persist($empresa);
		
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
