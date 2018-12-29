<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Ejecutado;

/**
 * EjecutadoEgresoValor
 *
 * @author Manuel Becerra
 * created 16/01/2015
 * 
 * @ORM\Table(name="ejecutado_egreso_valor")
 * @ORM\Entity
 */
class EjecutadoEgresoValor extends Ejecutado {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoEgresoValor")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     */
    protected $ordenPagoEgresoValor;

    /**
     * @ORM\OneToOne(targetEntity="RenglonComprobanteEgresoValor")
     * @ORM\JoinColumn(name="id_renglon_comprobante_egreso_valor", referencedColumnName="id", nullable=true)
     * */
    protected $renglonComprobanteEgresoValor;

    /**
     * Set renglonComprobanteEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor $renglonComprobanteEgresoValor
     * @return EjecutadoEgresoValor
     */
    public function setRenglonComprobanteEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor $renglonComprobanteEgresoValor = null) {
        $this->renglonComprobanteEgresoValor = $renglonComprobanteEgresoValor;

        return $this;
    }

    /**
     * Get renglonComprobanteEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor 
     */
    public function getRenglonComprobanteEgresoValor() {
        return $this->renglonComprobanteEgresoValor;
    }

    /**
     * Set ordenPagoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenPagoEgresoValor
     * @return EjecutadoEgresoValor
     */
    public function setOrdenPagoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenPagoEgresoValor = null) {
        $this->ordenPagoEgresoValor = $ordenPagoEgresoValor;

        return $this;
    }

    /**
     * Get ordenPagoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor 
     */
    public function getOrdenPagoEgresoValor() {
        return $this->ordenPagoEgresoValor;
    }

}
