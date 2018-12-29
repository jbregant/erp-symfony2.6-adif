<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\PlanDeCuentas;

/**
 * LoadPlanDeCuentasData
 * 
 * @author Manuel Becerra
 * created 24/06/2014
 *
 */
class LoadPlanDeCuentasData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $planDeCuentas = new PlanDeCuentas();

        $manager->persist($planDeCuentas);
        $manager->flush();

        $this->addReference('planDeCuentas', $planDeCuentas);
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 1;
    }

}
