<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Facturacion\IRenglonComprobanteVenta;

/**
 * RenglonComprobanteVentaGeneral
 *
 * @author Manuel Becerra
 * created 17/07/2015
 * 
 * @ORM\Table(name="renglon_comprobante_venta_general")
 * @ORM\Entity 
 */
class RenglonComprobanteVentaGeneral extends RenglonComprobanteVenta implements IRenglonComprobanteVenta 
{

    /**
     * @var ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_venta_general", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoVentaGeneral;

    /**
     * Set conceptoVentaGeneral
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral $conceptoVentaGeneral
     * @return RenglonComprobanteVentaGeneral
     */
    public function setConceptoVentaGeneral(\ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral $conceptoVentaGeneral = null) {
        $this->conceptoVentaGeneral = $conceptoVentaGeneral;

        return $this;
    }

    /**
     * Get conceptoVentaGeneral
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral 
     */
    public function getConceptoVentaGeneral() {
        return $this->conceptoVentaGeneral;
    }

}
