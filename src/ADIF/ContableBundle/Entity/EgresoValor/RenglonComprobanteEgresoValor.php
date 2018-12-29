<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\RenglonComprobante;

/**
 * RenglonComprobanteEgresoValor
 * 
 * @ORM\Table(name="renglon_comprobante_egreso_valor")
 * @ORM\Entity
 */
class RenglonComprobanteEgresoValor extends RenglonComprobante {

    /**
     * @var ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_egreso_valor", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoEgresoValor;

    /**
     * Set conceptoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor $conceptoEgresoValor
     * @return RenglonComprobanteEgresoValor
     */
    public function setConceptoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor $conceptoEgresoValor = null) {
        $this->conceptoEgresoValor = $conceptoEgresoValor;

        return $this;
    }

    /**
     * Get conceptoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor 
     */
    public function getConceptoEgresoValor() {
        return $this->conceptoEgresoValor;
    }

    /**
     * 
     * @return type
     */
    public function getPrecioTotalProrrateado() {
        return $this->getPrecioTotal() + $this->getComprobante()->getMontoProrrateado($this);
    }
    
    /**
     * 
     * @return type
     */
    public function getPrecioNetoTotalProrrateado() {
        return $this->getPrecioTotalProrrateado();
    }

}
