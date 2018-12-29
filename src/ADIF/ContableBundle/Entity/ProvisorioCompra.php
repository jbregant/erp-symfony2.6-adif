<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProvisorioCompra
 *
 * @author Manuel Becerra
 * created 16/10/2014
 * 
 * @ORM\Table(name="provisorio_compra")
 * @ORM\Entity
 */
class ProvisorioCompra extends Provisorio {

    /**
     * @ORM\Column(name="id_renglon_requerimiento", type="integer", nullable=true)
     */
    protected $idRenglonRequerimiento;

    /**
     * @var ADIF\ComprasBundle\Entity\RenglonRequerimiento
     */
    protected $renglonRequerimiento;

    

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esManual = false;
    }

    /**
     * Set idRenglonRequerimiento
     *
     * @param integer $idRenglonRequerimiento
     * @return ProvisorioCompra
     */
    public function setIdRenglonRequerimiento($idRenglonRequerimiento) {
        $this->idRenglonRequerimiento = $idRenglonRequerimiento;

        return $this;
    }

    /**
     * Get idRenglonRequerimiento
     *
     * @return integer 
     */
    public function getIdRenglonRequerimiento() {
        return $this->idRenglonRequerimiento;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonRequerimiento
     */
    public function setRenglonRequerimiento($renglonRequerimiento) {

        if (null != $renglonRequerimiento) {
            $this->idRenglonRequerimiento = $renglonRequerimiento->getId();
        } //.
        else {
            $this->idRenglonRequerimiento = null;
        }

        $this->renglonRequerimiento = $renglonRequerimiento;
    }

    /**
     * 
     * @return type
     */
    public function getRenglonRequerimiento() {
        return $this->renglonRequerimiento;
    }
}
