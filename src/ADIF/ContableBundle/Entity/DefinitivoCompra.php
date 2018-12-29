<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DefinitivoCompra
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="definitivo_compra")
 * @ORM\Entity
 */
class DefinitivoCompra extends Definitivo {

    /**
     * @ORM\Column(name="id_renglon_orden_compra", type="integer", nullable=true)
     */
    protected $idRenglonOrdenCompra;

    /**
     * @var ADIF\ComprasBundle\Entity\RenglonOrdenCompra
     */
    protected $renglonOrdenCompra;

    /**
     * Set idRenglonOrdenCompra
     *
     * @param integer $idRenglonOrdenCompra
     * @return DefinitivoCompra
     */
    public function setIdRenglonOrdenCompra($idRenglonOrdenCompra) {
        $this->idRenglonOrdenCompra = $idRenglonOrdenCompra;

        return $this;
    }

    /**
     * Get idRenglonOrdenCompra
     *
     * @return integer 
     */
    public function getIdRenglonOrdenCompra() {
        return $this->idRenglonOrdenCompra;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglonOrdenCompra
     */
    public function setRenglonOrdenCompra($renglonOrdenCompra) {

        if (null != $renglonOrdenCompra) {
            $this->idRenglonOrdenCompra = $renglonOrdenCompra->getId();
        } //.
        else {
            $this->idRenglonOrdenCompra = null;
        }

        $this->renglonOrdenCompra = $renglonOrdenCompra;
    }

    /**
     * 
     * @return type
     */
    public function getRenglonOrdenCompra() {
        return $this->renglonOrdenCompra;
    }

}
