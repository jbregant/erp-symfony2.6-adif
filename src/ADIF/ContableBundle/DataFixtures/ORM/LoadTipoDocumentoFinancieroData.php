<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadTipoDocumentoFinancieroData
 *
 */
class LoadTipoDocumentoFinancieroData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $nombre
     */
    private function setData($manager, $nombre) {

        $entity = new \ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero();

        $entity->setNombre($nombre);

        $manager->persist($entity);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setData($manager, 'Certificado de obra');
        $this->setData($manager, 'Redeterminación de obra');
        $this->setData($manager, 'Anticipo financiero');
        $this->setData($manager, 'Fondo de reparo');

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
