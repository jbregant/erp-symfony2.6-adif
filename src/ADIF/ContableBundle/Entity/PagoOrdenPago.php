<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of PagoOrdenPago
 *
 * @author Manuel Becerra
 * created 04/11/2014
 * 
 * @ORM\Table(name="pago_orden_pago")
 * @ORM\Entity
 */
class PagoOrdenPago extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ADIF\ContableBundle\Entity\OrdenPago
     *
     * @ORM\OneToMany(targetEntity="OrdenPago", mappedBy="pagoOrdenPago", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     * 
     */
    protected $ordenesPago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pago", type="datetime", nullable=false)
     */
    protected $fechaPago;

    /**
     * @var \ADIF\ContableBundle\Entity\Cheque
     *
     * @ORM\OneToMany(targetEntity="Cheque", cascade={"all"}, mappedBy="pagoOrdenPago")     
     * 
     */
    protected $cheques;

    /**
     * @var \ADIF\ContableBundle\Entity\TransferenciaBancaria
     *
     * @ORM\OneToMany(targetEntity="TransferenciaBancaria", cascade={"all"}, mappedBy="pagoOrdenPago")     
     * @ORM\JoinColumn(name="id_netcash", referencedColumnName="id", nullable=true) 
     */
    protected $transferencias;

    /**
     * @var \ADIF\ContableBundle\Entity\NetCash
     *
     * @ORM\ManyToOne(targetEntity="NetCash", cascade={"all"}, inversedBy="pagosOrdenPago") 
     * @ORM\JoinColumn(name="id_net_cash", referencedColumnName="id")    
     * 
     */
    protected $netCash;
    
    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $monto;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ordenesPago = new ArrayCollection();
        $this->cheques = new ArrayCollection();
        $this->transferencias = new ArrayCollection();
        $this->fechaPago = new \DateTime();
    }

    /**
     * 
     */
    public function __toString() {
        return $this->getDetalle();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     * @return PagoOrdenPago
     */
    public function setFechaPago($fechaPago) {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return \DateTime 
     */
    public function getFechaPago() {
        return $this->fechaPago;
    }

    /**
     * Add cheque
     *
     * @param \ADIF\ContableBundle\Entity\Cheque $cheque
     * @return PagoOrdenPago
     */
    public function addCheque(\ADIF\ContableBundle\Entity\Cheque $cheque) {
        $this->cheques[] = $cheque;

        return $this;
    }

    /**
     * Remove cheque
     *
     * @param \ADIF\ContableBundle\Entity\Cheque $ordenesPago
     */
    public function removeCheque(\ADIF\ContableBundle\Entity\Cheque $cheque) {
        $this->cheques->removeElement($cheque);
    }

    /**
     * Get cheque
     *
     * @return \ADIF\ContableBundle\Entity\Cheque 
     */
    public function getCheques() {
        return $this->cheques;
    }

    /**
     * Add transferencia
     *
     * @param \ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia
     * @return PagoOrdenPago
     */
    public function addTransferencia(\ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia) {
        $this->transferencias[] = $transferencia;

        return $this;
    }

    /**
     * Remove transferencia
     *
     * @param \ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia
     */
    public function removeTransferencia(\ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia) {
        $this->transferencias->removeElement($transferencia);
    }

    /**
     * Get transferencia
     *
     * @return \ADIF\ContableBundle\Entity\TransferenciaBancaria 
     */
    public function getTransferencias() {
        return $this->transferencias;
    }

    /**
     * Set netCash
     *
     * @param NetCash $netCash
     * @return PagoOrdenPago
     */
    public function setNetCash($netCash) {
        $this->netCash = $netCash;

        return $this;
    }

    /**
     * Get netCash
     *
     * @return NetCash
     */
    public function getNetCash() {
        return $this->netCash;
    }
    
    /**
     * 
     * @return type
     */
    public function getCuentaBancariaADIFOld() {

        $cuentaBancariaADIF = null;

        if (null != $this->getCheque()) {
            $cuentaBancariaADIF = $this->getCheque()->getChequera()->getCuenta();
        } // 
        else if (null != $this->getTransferencia()) {
            $cuentaBancariaADIF = $this->getTransferencia()->getCuenta();
        }

        return $cuentaBancariaADIF;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaBancariaADIF() {
        $cuentaBancariaADIF = null;
        if (!$this->getCheques()->isEmpty()) {
            $cuentaBancariaADIF = $this->getCheques()->first()->getChequera()->getCuenta();
        } // 
        else if (!$this->getTransferencias()->isEmpty()) {
            $cuentaBancariaADIF = $this->getTransferencias()->first()->getCuenta();
        }

        return $cuentaBancariaADIF;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaBancariaADIFString() {
        return $this->getCuentaBancariaADIF()->__toString();
    }

    /**
     * 
     * @return type
     */
    public function getDetalleOld() {

        $detalle = null;

        if (null != $this->getCheque()) {
            $detalle = 'Cheque N&ordm; ' . $this->getCheque()->getNumeroCheque();
        } // 
        else if (null != $this->getTransferencia()) {
            $detalle = 'Transferencia N&ordm; ' . $this->getTransferencia()->getNumeroTransferencia();
        }

        return $detalle;
    }

    /**
     * 
     * @return type
     */
    public function getDetalle() {

        if ($this->getOrdenPagoPagada() != null) {
            return 'Orden pago N&ordm; ' . $this->getOrdenPagoPagada()->getNumeroOrdenPago();
        }

        return '';
    }
    
    /**
     * 
     * @return type
     */
    public function getDetallePagos() {
        if ($this->getNetCash() != null) {
            return $this->getNetCash()->getCuenta()->getIdBanco() . ' - ' . $this->getNetCash()->getCuenta()->getCuentaContable() . ' -- Net Cash N&ordm; ' . str_pad($this->getNetCash()->getNumero(), 8, "0", STR_PAD_LEFT) . ' ($' . number_format($this->getNetCash()->getMonto(), 2, '.', ',') . ')';
        } else {
            $detalle = '';
            foreach($this->getCheques() as $cheque) {
                if($detalle != ''){
                    $detalle .= '<br>';
                }
                $detalle .= $cheque->getChequera()->getCuenta()->getIdBanco().' - '.$cheque->getChequera()->getCuenta()->getCuentaContable().' -- Transferencia N&ordm; '.$cheque->getNumeroCheque().' ($'.number_format($cheque->getMonto(), 2, '.', ',').')';
            }

            foreach($this->getTransferencias() as $transferencia) {
                if($detalle != ''){
                    $detalle .= '<br>';
                }
                $detalle .= $transferencia->getCuenta()->getIdBanco().' - '.$transferencia->getCuenta()->getCuentaContable().' -- Cheque N&ordm; '.$transferencia->getNumeroTransferencia().' ($'.number_format($transferencia->getMonto(), 2, '.', ',').')';
            }
        }
        return $detalle;
    }

    /**
     * Add ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPago $ordenesPago
     * @return PagoOrdenPago
     */
    public function addOrdenesPago(\ADIF\ContableBundle\Entity\OrdenPago $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPago $ordenesPago
     */
    public function removeOrdenesPago(\ADIF\ContableBundle\Entity\OrdenPago $ordenesPago) {
        $this->ordenesPago->removeElement($ordenesPago);
    }

    /**
     * Get ordenesPago
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesPago() {
        return $this->ordenesPago;
    }

    /**
     * Set estadoPago
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPago $estadoPago
     * @return PagoOrdenPago
     */
    public function setEstadoPago(\ADIF\ContableBundle\Entity\EstadoPago $estadoPago = null) {

        foreach ($this->getCheques() as $cheque) {
            $cheque->setEstadoPago($estadoPago);
        }
        foreach ($this->getTransferencias() as $transferencia) {
            $transferencia->setEstadoPago($estadoPago);
        }

        return $this;
    }

    /**
     * Get estadoPago
     * 
     * @return type
     */
    public function getEstadoPago() {

        $estado = null;

        foreach ($this->getCheques() as $cheque) {
            $estado = $cheque->getEstadoPago();
            break;
        }
        foreach ($this->getTransferencias() as $transferencia) {
            $estado = $transferencia->getEstadoPago();
            break;
        }

        return $estado;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return PagoOrdenPago
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Get ordenPagoPagada
     * 
     * @return OrdenPago
     */
    public function getOrdenPagoPagada() {

        $ordenesPagoFilter = $this->ordenesPago->filter(
                function($entry) {
            return $entry->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA;
        }
        );

        return (!$ordenesPagoFilter->isEmpty() ? $ordenesPagoFilter->first() : null);
    }

}
