<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevengadoOrdenPagoGeneral
 *
 * @author Manuel Becerra
 * created 01/09/2015
 * 
 * @ORM\Table(name="devengado_orden_pago_general")
 * @ORM\Entity
 */
class DevengadoOrdenPagoGeneral extends Devengado {

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\OrdenPagoGeneral")
     * @ORM\JoinColumn(name="id_orden_pago_general", referencedColumnName="id")
     * */
    protected $ordenPagoGeneral;

    /**
     * Set ordenPagoGeneral
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoGeneral $ordenPagoGeneral
     * @return DevengadoOrdenPagoGeneral
     */
    public function setOrdenPagoGeneral(\ADIF\ContableBundle\Entity\OrdenPagoGeneral $ordenPagoGeneral = null) {
        $this->ordenPagoGeneral = $ordenPagoGeneral;

        return $this;
    }

    /**
     * Get ordenPagoGeneral
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoGeneral 
     */
    public function getOrdenPagoGeneral() {
        return $this->ordenPagoGeneral;
    }

}
