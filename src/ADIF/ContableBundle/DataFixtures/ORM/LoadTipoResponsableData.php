<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoResponsable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;

/**
 * LoadTipoResponsableData
 * 
 * @author Manuel Becerra
 * created 10/07/2014
 *
 */
class LoadTipoResponsableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $codigo
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setTipoResponsable($manager, $codigo, $denominacion, $descripcion) {

        $tipoResponsable = new TipoResponsable();

        $tipoResponsable->setCodigoTipoResponsable($codigo);
        $tipoResponsable->setDenominacionTipoResponsable($denominacion);
        $tipoResponsable->setDescripcionTipoResponsable($descripcion);

        $manager->persist($tipoResponsable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoResponsable($manager, 'SNC', ConstanteTipoResponsable::SUJETO_NO_CATEGORIZADO, '');
        $this->setTipoResponsable($manager, 'CF', ConstanteTipoResponsable::CONSUMIDOR_FINAL, '');
        $this->setTipoResponsable($manager, 'CM', ConstanteTipoResponsable::CONVENIO_MULTILATERAL, '');
        $this->setTipoResponsable($manager, 'I', ConstanteTipoResponsable::INSCRIPTO, '');
        $this->setTipoResponsable($manager, 'RM', ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO, '');

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
