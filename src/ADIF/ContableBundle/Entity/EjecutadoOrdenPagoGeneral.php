<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EjecutadoOrdenPagoGeneral
 * 
 * @ORM\Table(name="ejecutado_orden_pago_general")
 * @ORM\Entity
 */
class EjecutadoOrdenPagoGeneral extends Ejecutado {

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\OrdenPagoGeneral")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     * */
    protected $ordenPagoGeneral;

    /**
     * Set ordenPagoGeneral
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoGeneral $ordenPagoGeneral
     * @return EjecutadoOrdenPagoGeneral
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
