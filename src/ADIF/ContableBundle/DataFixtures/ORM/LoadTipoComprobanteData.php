<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\TipoComprobante;

/**
 * LoadTipoComprobanteData
 * 
 * @author Manuel Becerra
 * created 23/10/2014
 *
 */
class LoadTipoComprobanteData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $nombre
     */
    private function setTipoComprobante($manager, $nombre) {

        $tipoComprobante = new TipoComprobante();

        $tipoComprobante->setNombre($nombre);

        $manager->persist($tipoComprobante);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $this->setTipoComprobante($manager, 'Factura');
        $this->setTipoComprobante($manager, 'Nota débito');
        $this->setTipoComprobante($manager, 'Nota crédito');
        $this->setTipoComprobante($manager, 'Recibo');
        $this->setTipoComprobante($manager, 'Ticket factura');
        $this->setTipoComprobante($manager, 'Cupón');
        $this->setTipoComprobante($manager, 'Nota débito intereses');

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
