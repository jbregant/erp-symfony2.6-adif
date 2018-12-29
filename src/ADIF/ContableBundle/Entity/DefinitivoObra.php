<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Obras\Tramo;
use Doctrine\ORM\Mapping as ORM;

/**
 * DefinitivoObra
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="definitivo_obra")
 * @ORM\Entity
 */
class DefinitivoObra extends Definitivo {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Obras\Tramo")
     * @ORM\JoinColumn(name="id_tramo", referencedColumnName="id", nullable=true)
     */
    protected $tramo;

    /**
     * Set tramo
     *
     * @param Tramo $tramo
     * @return DefinitivoObra
     */
    public function setTramo(Tramo $tramo) {
        $this->tramo = $tramo;

        return $this;
    }

    /**
     * Get tramo
     *
     * @return Tramo 
     */
    public function getTramo() {
        return $this->tramo;
    }

}
