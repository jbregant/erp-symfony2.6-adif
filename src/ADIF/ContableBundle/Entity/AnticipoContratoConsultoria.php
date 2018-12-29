<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnticipoContratoLocacion
 * 
 * @author Darío Rapetti
 * created 05/05/2015
 *
 * @ORM\Table(name="anticipo_contrato_consultoria")
 * @ORM\Entity
 */
class AnticipoContratoConsultoria extends Anticipo {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoAnticipoContratoConsultoria", mappedBy="anticipoContratoConsultoria")
     * */
    protected $ordenPago;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria", inversedBy="anticipos")
     * @ORM\JoinColumn(name="id_contrato_consultoria", referencedColumnName="id")
     */
    protected $contrato;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria", inversedBy="anticipos")
     * @ORM\JoinColumn(name="id_orden_pago_cancelada", referencedColumnName="id")
     */
    private $ordenPagoCancelada;

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria $ordenPago
     * @return AnticipoContratoConsultoria
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set contrato
     *
     * @param ContratoConsultoria $contrato
     * @return AnticipoContratoConsultoria
     */
    public function setContrato(Consultoria\ContratoConsultoria $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return ContratoConsultoria 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Get contrato
     *
     * @return ContratoConsultoria 
     */
    public function getConsultor() {
        return $this->getContrato()->getConsultor();
    }


    /**
     * Set ordenPagoCancelada
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPagoCancelada
     * @return AnticipoContratoConsultoria
     */
    public function setOrdenPagoCancelada(\ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPagoCancelada = null)
    {
        $this->ordenPagoCancelada = $ordenPagoCancelada;

        return $this;
    }

    /**
     * Get ordenPagoCancelada
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria 
     */
    public function getOrdenPagoCancelada()
    {
        return $this->ordenPagoCancelada;
    }
}
