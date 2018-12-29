<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;

/**
 * EjecutadoConsultoria
 * 
 * @ORM\Table(name="ejecutado_consultoria")
 * @ORM\Entity
 */
class EjecutadoConsultoria extends Ejecutado {

    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id")
     * */
    protected $contrato;

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     */
    protected $ordenPago;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato
     * @return EjecutadoConsultoria
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato = null)
    {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria 
     */
    public function getContrato()
    {
        return $this->contrato;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPago
     * @return EjecutadoConsultoria
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPago = null)
    {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria 
     */
    public function getOrdenPago()
    {
        return $this->ordenPago;
    }
}
