<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DefinitivoContratoVenta
 *
 * @author Manuel Becerra
 * created 10/06/2015
 * 
 * @ORM\Table(name="definitivo_contrato_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DefinitivoContratoVentaRepository")
 */
class DefinitivoContratoVenta extends Definitivo {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ContratoVenta")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=false)
     */
    protected $contrato;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ContratoVenta $contrato
     * @return DefinitivoContratoVenta
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Facturacion\ContratoVenta $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ContratoVenta 
     */
    public function getContrato() {
        return $this->contrato;
    }

}
