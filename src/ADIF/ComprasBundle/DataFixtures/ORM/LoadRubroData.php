<?php

namespace ADIF\ComprasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ComprasBundle\Entity\Rubro;

/**
 * LoadRubroData
 * 
 * @author Manuel Becerra
 * created 21/07/2014
 *
 */
class LoadRubroData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setRubro($manager, $denominacion, $descripcion) {

        $rubro = new Rubro();

        $rubro->setDenominacionRubro($denominacion);
        $rubro->setDescripcionRubro($descripcion);

        // $manager->persist($rubro);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setRubro($manager, 'Artículos de Librería', '');
        $this->setRubro($manager, 'Ceremonial - Protocolo', '');
        $this->setRubro($manager, 'Electricidad - Telefonía', '');
        $this->setRubro($manager, 'Gastos de Edificio', '');
        $this->setRubro($manager, 'Gastos en Personal', '');
        $this->setRubro($manager, 'Gastos Varios', '');
        $this->setRubro($manager, 'Informática - Hardware', '');
        $this->setRubro($manager, 'Informática - Insumos', '');
        $this->setRubro($manager, 'Informática - Software', '');
        $this->setRubro($manager, 'Libros de Texto', '');
        $this->setRubro($manager, 'Mantenimiento Automotriz', '');
        $this->setRubro($manager, 'Material Rodante', '');
        $this->setRubro($manager, 'Medicina Laboral', '');
        $this->setRubro($manager, 'Muebles para Oficina', '');
        $this->setRubro($manager, 'Obras Ferroviarias', '');
        $this->setRubro($manager, 'Publicidad', '');
        $this->setRubro($manager, 'Papelería', '');
        $this->setRubro($manager, 'Servicios', '');
        $this->setRubro($manager, 'Servicios Generales', '');


        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 3;
    }

}
