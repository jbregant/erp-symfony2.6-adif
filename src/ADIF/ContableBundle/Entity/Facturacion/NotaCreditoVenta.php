<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Facturacion\IConciliableCreditoVenta;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="nota_credito_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "nota_credito_venta" = "NotaCreditoVenta",
 *      "nota_credito_venta_general" = "NotaCreditoVentaGeneral",
 *      "nota_credito_pliego" = "NotaCreditoPliego"
 * })
 */
class NotaCreditoVenta extends ComprobanteVenta implements BaseAuditable, IConciliableCreditoVenta {

    /**
     * @var \string
     *
     * @ORM\Column(name="cae_numero", type="string", length=14, nullable=true)
     */
    protected $caeNumero;

    /**
     * @var \Date
     *
     * @ORM\Column(name="cae_vencimiento", type="date", nullable=true)
     */
    protected $caeVencimiento;

    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta", mappedBy="notasCreditoVenta")
     * */
    protected $cobrosNotaCreditoVenta;
   
    /**
     * @ORM\OneToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta")
     * @ORM\JoinColumn(name="comprobante_cancelado_id", referencedColumnName="id")
     * */
    private $comprobanteCancelado;

    /**
     * Set caeNumero
     *
     * @param string $caeNumero
     * @return NotaCreditoVenta
     */
    public function setCaeNumero($caeNumero) {
        $this->caeNumero = $caeNumero;

        return $this;
    }

    /**
     * Get caeNumero
     *
     * @return string 
     */
    public function getCaeNumero() {
        return $this->caeNumero;
    }

//    /**
//     * Set caeVencimiento
//     *
//     * @param \DateTime $caeVencimiento
//     * @return NotaCreditoVenta
//     */
//    public function setFechaVencimiento($caeVencimiento) {
//        $this->caeVencimiento = $caeVencimiento;
//
//        return $this;
//    }

    /**
     * Get caeVencimiento
     *
     * @return \DateTime 
     */
    public function getCaeVencimiento() {
        return $this->caeVencimiento;
    }

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {
        return $this->getContrato()->getFechaInicio();
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {
        return $this->getContrato()->getFechaFin();
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->cobrosNotaCreditoVenta = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set caeVencimiento
     *
     * @param \DateTime $caeVencimiento
     * @return NotaCreditoVenta
     */
    public function setCaeVencimiento($caeVencimiento) {
        $this->caeVencimiento = $caeVencimiento;

        return $this;
    }

    /**
     * Add cobrosNotaCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta
     * @return NotaCreditoVenta
     */
    public function addCobrosNotaCreditoVenta(\ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta) {
        $this->cobrosNotaCreditoVenta[] = $cobrosNotaCreditoVenta;

        return $this;
    }

    /**
     * Remove cobrosNotaCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta
     */
    public function removeCobrosNotaCreditoVenta(\ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta) {
        $this->cobrosNotaCreditoVenta->removeElement($cobrosNotaCreditoVenta);
    }

    /**
     * Get cobrosNotaCreditoVenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobrosNotaCreditoVenta() {
        return $this->cobrosNotaCreditoVenta;
    }

    public function getEsNotaCredito() {
        return true;
    }

    /**
     *
     * @return string 
     */
    public function anular() {
        $cobros = $this->getCobrosNotaCreditoVenta();
        if (sizeOf($cobros) == 0) {
            $rsp = '';
        } //tb podría preguntarse && $this->getSaldo() == $this->getTotal()
        else {

            $detalle = '<ul>';
            foreach ($cobros as $cobro) {
                $comprobante = $cobro->getComprobantes()[0];
                $letra = $comprobante->getLetraComprobante();
                $detalle_aux = $comprobante->getTipoComprobante() . ($letra ? ' (' . $letra . ')' : '') . ' N° ' . $comprobante->getNumeroCompleto();
                $detalle .= '<li>' . $detalle_aux . '</li>';
            }
            $detalle .= '</ul>';
            //die();
            $rsp = "No se puede anular la nota de cr&eacute;dito porque est&aacute; siendo utilizada para cancelar los siguientes comprobantes: <br/>" . $detalle;
        }

        return $rsp;
    }


    /**
     * Add cobrosNotaCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta
     * @return NotaCreditoVenta
     */
    public function addCobrosNotaCreditoVentum(\ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta)
    {
        $this->cobrosNotaCreditoVenta[] = $cobrosNotaCreditoVenta;

        return $this;
    }

    /**
     * Remove cobrosNotaCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta
     */
    public function removeCobrosNotaCreditoVentum(\ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta $cobrosNotaCreditoVenta)
    {
        $this->cobrosNotaCreditoVenta->removeElement($cobrosNotaCreditoVenta);
    }

    /**
     * Set comprobanteCancelado
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobanteCancelado
     * @return NotaCreditoVenta
     */
    public function setComprobanteCancelado(\ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobanteCancelado = null)
    {
        $this->comprobanteCancelado = $comprobanteCancelado;

        return $this;
    }

    /**
     * Get comprobanteCancelado
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta 
     */
    public function getComprobanteCancelado()
    {
        return $this->comprobanteCancelado;
    }
    
    public function getEsCupon() 
    {
        return false;
    }
}
