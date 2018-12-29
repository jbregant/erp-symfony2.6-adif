<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of TransferenciaBancaria
 *
 * @author Manuel Becerra
 * created 05/11/2014
 * 
 * @ORM\Table(name="transferencia_bancaria")
 * @ORM\Entity
 */
class TransferenciaBancaria extends ConciliacionBancaria\MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de transferencia no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroTransferencia;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta;

    /**
     * @var EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumn(name="id_estado_pago", referencedColumnName="id")
     * 
     */
    protected $estadoPago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_modificacion_estado", type="date", nullable=false)
     */
    protected $fechaUltimaModificacionEstado;

    /**
     * @var PagoOrdenPago
     *
     * @ORM\ManyToOne(targetEntity="PagoOrdenPago", inversedBy="transferencias")
     * @ORM\JoinColumn(name="id_pago_orden_pago", referencedColumnName="id", nullable=true)
     * 
     */
    protected $pagoOrdenPago;

    /**
     *
     * @var EstadoPagoHistorico
     * 
     * @ORM\OneToMany(targetEntity="EstadoPagoHistorico", mappedBy="transferencia", cascade={"all"})
     * @ORM\OrderBy({"fecha" = "DESC"})
     */
    protected $historicoEstados;

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
        parent::__construct();

        $this->fechaUltimaModificacionEstado = new \DateTime();
        $this->historicoEstados = new ArrayCollection();
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
     * Set idCuenta
     *
     * @param integer $idCuenta
     * @return PagoOrdenPago
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * Get idCuenta
     *
     * @return integer 
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuenta
     */
    public function setCuenta($cuenta) {

        if (null != $cuenta) {
            $this->idCuenta = $cuenta->getId();
        } //.
        else {
            $this->idCuenta = null;
        }

        $this->cuenta = $cuenta;
    }

    /**
     * 
     * @return type
     */
    public function getCuenta() {
        return $this->cuenta;
    }

    /**
     * Set numeroTransferencia
     *
     * @param string $numeroTransferencia
     * @return TransferenciaBancaria
     */
    public function setNumeroTransferencia($numeroTransferencia) {
        $this->numeroTransferencia = $numeroTransferencia;

        return $this;
    }

    /**
     * Get numeroTransferencia
     *
     * @return string 
     */
    public function getNumeroTransferencia() {
        return $this->numeroTransferencia;
    }

    /**
     * Set estadoPago
     *
     * @param EstadoPago $estadoPago
     * @return TransferenciaBancaria
     */
    public function setEstadoPago(EstadoPago $estadoPago = null) {
        $this->estadoPago = $estadoPago;

        return $this;
    }

    /**
     * Get estadoPago
     *
     * @return EstadoPago
     */
    public function getEstadoPago() {
        return $this->estadoPago;
    }

    /**
     * Set pagoOrdenPago
     *
     * @param \ADIF\ContableBundle\Entity\pagoOrdenPago $pagoOrdenPago
     * @return TransferenciaBancaria
     */
    public function setPagoOrdenPago(PagoOrdenPago $pagoOrdenPago = null) {
        $this->pagoOrdenPago = $pagoOrdenPago;

        return $this;
    }

    /**
     * Get pagoOrdenPago
     *
     * @return PagoOrdenPago
     */
    public function getPagoOrdenPago() {
        return $this->pagoOrdenPago;
    }

    /**
     * Set fechaUltimaModificacionEstado
     *
     * @param \DateTime $fechaUltimaModificacionEstado
     * @return TransferenciaBancaria
     */
    public function setFechaUltimaModificacionEstado($fechaUltimaModificacionEstado) {
        $this->fechaUltimaModificacionEstado = $fechaUltimaModificacionEstado;

        return $this;
    }

    /**
     * Get fechaUltimaModificacionEstado
     *
     * @return \DateTime 
     */
    public function getFechaUltimaModificacionEstado() {
        return $this->fechaUltimaModificacionEstado;
    }

    /**
     * Add historicoEstados
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPagoHistorico $transferenciaBancariaHistorico
     * @return TransferenciaBancaria
     */
    public function addHistoricoEstado(\ADIF\ContableBundle\Entity\EstadoPagoHistorico $transferenciaBancariaHistorico) {
        $this->historicoEstados[] = $transferenciaBancariaHistorico;

        return $this;
    }

    /**
     * Remove historicoEstados
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPagoHistorico $transferenciaBancariaHistorico
     */
    public function removeHistoricoEstado(\ADIF\ContableBundle\Entity\EstadoPagoHistorico $transferenciaBancariaHistorico) {
        $this->historicoEstados->removeElement($transferenciaBancariaHistorico);
    }

    /**
     * Get historicoEstados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistoricoEstados() {
        return $this->historicoEstados;
    }

    /**
     * 
     * @param type $cuentaBancaria
     * @param type $fecha_inicio
     * @param type $fecha_fin
     * @return type
     */
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return
                $this->getIdCuenta() == $cuentaBancaria->getId() &&
                ($this->getPagoOrdenPago()->getOrdenPagoPagada() != null) &&
                ($this->getEstadoPago() && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO) &&
                ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) &&
                ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true);
    }

    /**
     * 
     * @return type
     */
    public function getConcepto() {
        return 'Transferencia N&ordm; ' . $this->getNumeroTransferencia();
    }

    /**
     * 
     */
    public function getReferencia() {
        return $this->getNumeroTransferencia();
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        return 'TRANSFERENCIA';
    }

    /**
     * 
     * @return type
     */
    public function getFecha() {
        return $this->getPagoOrdenPago()->getFechaPago();
    }

    /**
     * 
     * @param type $cuentaBancaria
     * @return type
     */
    public function getMontoMovimiento($cuentaBancaria = null) {
        return ($this->getPagoOrdenPago()->getOrdenPagoPagada() != null) //
                ? $this->getMonto() //
                : 0;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsContabilizable() {
        return false;
    }

    /**
     * 
     * @return int
     */
    public function getCodigoConcepto() {
        return 1;
    }

    /**
     * 
     * @return type
     */
    public function getFechaParaMayor() {
        return $this->getPagoOrdenPago()->getOrdenPagoPagada()->getFechaContable();
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEditable() {

        return $this->getEstadoPago() != null //
                && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO //
                && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_REEMPLAZADO;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return TransferenciaBancaria
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return double 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * 
     * @return type
     */
    public function getEstaAnulado() {
        return $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO;
    }

    /**
     * 
     * @return type
     */
    public function getEsAnulable() {
        return (($this->getPagoOrdenPago() == null)//
                || ($this->getPagoOrdenPago() != null) && ($this->getPagoOrdenPago()->getOrdenPagoPagada() == null));
    }

}
