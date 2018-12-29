<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\ContableBundle\Entity\PolizaSeguro;
use Doctrine\ORM\Mapping as ORM;

/**
 * PolizaSeguroObra
 *
 * @author Manuel Becerra
 * created 31/05/2015
 * 
 * @ORM\Table(name="poliza_seguro_obra")
 * @ORM\Entity
 */
class PolizaSeguroObra extends PolizaSeguro {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Obras\Tramo", inversedBy="polizasSeguro")
     * @ORM\JoinColumn(name="id_tramo", referencedColumnName="id", nullable=false)
     */
    protected $tramo;

    /**
     * Set tramo
     *
     * @param Tramo $tramo
     * @return PolizaSeguroObra
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
