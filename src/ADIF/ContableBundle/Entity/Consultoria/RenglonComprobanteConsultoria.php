<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\RenglonComprobante;

/**
 * RenglonComprobanteConsultoria
 *
 * @author Manuel Becerra
 * created 10/04/2015
 * 
 * @ORM\Table(name="renglon_comprobante_consultoria")
 * @ORM\Entity 
 */
class RenglonComprobanteConsultoria extends RenglonComprobante {

    /**
     * @var integer
     * 
     * @ORM\Column(name="numero_cuota", type="integer", nullable=true)
     */
    protected $numeroCuota;

    /**
     * @var CicloFacturacion
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion")
     * @ORM\JoinColumn(name="id_ciclo_facturacion", referencedColumnName="id", nullable=true)
     */
    protected $cicloFacturacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cancelado", type="boolean", nullable=true, options={"default":0})
     */
    protected $cancelado;

    /**
     * @ORM\OneToOne(targetEntity="RenglonComprobanteConsultoria")
     * @ORM\JoinColumn(name="renglon_cancelado_id", referencedColumnName="id")
     * */
    private $renglonCancelado;

    /**
     * Set numeroCuota
     *
     * @param integer $numeroCuota
     * @return RenglonComprobanteConsultoria
     */
    public function setNumeroCuota($numeroCuota) {
        $this->numeroCuota = $numeroCuota;

        return $this;
    }

    /**
     * Get numeroCuota
     *
     * @return integer 
     */
    public function getNumeroCuota() {
        return $this->numeroCuota;
    }

    /**
     * Set cicloFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion
     * @return RenglonComprobanteConsultoria
     */
    public function setCicloFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion) {
        $this->cicloFacturacion = $cicloFacturacion;

        return $this;
    }

    /**
     * Get cicloFacturacion
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion 
     */
    public function getCicloFacturacion() {
        return $this->cicloFacturacion;
    }

    /**
     * Set cancelado
     *
     * @param boolean $cancelado
     * @return RenglonComprobanteConsultoria
     */
    public function setCancelado($cancelado) {
        $this->cancelado = $cancelado;

        return $this;
    }

    /**
     * Get cancelado
     *
     * @return boolean 
     */
    public function getCancelado() {
        return $this->cancelado;
    }

    /**
     * Set renglonCancelado
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria $renglonCancelado
     * @return RenglonComprobanteConsultoria
     */
    public function setRenglonCancelado(\ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria $renglonCancelado = null) {
        $this->renglonCancelado = $renglonCancelado;

        return $this;
    }

    /**
     * Get renglonCancelado
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria 
     */
    public function getRenglonCancelado() {
        return $this->renglonCancelado;
    }

}
