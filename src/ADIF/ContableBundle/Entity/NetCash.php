<?php

namespace ADIF\ContableBundle\Entity;

//use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoNetCash;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of NetCash
 *
 * 
 * @ORM\Table(name="netcash")
 * @ORM\Entity
 */
class NetCash extends ConciliacionBancaria\MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", length=8, nullable=true)
     */
    protected $numero;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta;

    /**
     * @var EstadoNetCash
     *
     * @ORM\ManyToOne(targetEntity="EstadoNetCash")
     * @ORM\JoinColumn(name="id_estado_netcash", referencedColumnName="id")    
     */
    protected $estadoNetCash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_emision", type="date", nullable=false)
     */
    protected $fechaEmision;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="date", nullable=true)
     */
    protected $fechaEntrega;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pago", type="date", nullable=true)
     */
    protected $fechaPago;

    /**
     * @var PagoOrdenPago
     *
     * @ORM\OneToMany(targetEntity="PagoOrdenPago", mappedBy="netCash")     
     */
    protected $pagosOrdenPago;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $monto;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->fechaEmision = new \DateTime();
        $this->pagosOrdenPago = new ArrayCollection();
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
     * Set numero
     *
     * @param integer $numero
     * @return NetCash
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set idCuenta
     *
     * @param integer $idCuenta
     * @return NetCash
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
        } else {
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
     * Set estadoNetCash
     *
     * @param EstadoNetCash $estadoNetCash
     * @return NetCash
     */
    public function setEstadoNetCash(EstadoNetCash $estadoNetCash = null) {
        $this->estadoNetCash = $estadoNetCash;

        return $this;
    }

    /**
     * Get estadoNetCash
     *
     * @return EstadoNetCash
     */
    public function getEstadoNetCash() {
        return $this->estadoNetCash;
    }

    /**
     * Set fechaEmision
     *
     * @param \DateTime $fechaEmision
     * @return NetCash
     */
    public function setFechaEmision($fechaEmision) {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    /**
     * Get fechaEmision
     *
     * @return \DateTime 
     */
    public function getFechaEmision() {
        return $this->fechaEmision;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     * @return NetCash
     */
    public function setFechaEntrega($fechaEntrega) {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime 
     */
    public function getFechaEntrega() {
        return $this->fechaEntrega;
    }

    /**
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     * @return NetCash
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
     * Add pagosOrdenPago
     *
     * @param \ADIF\ContableBundle\Entity\PagoOrdenPago $pagosOrdenPago
     * @return NetCash
     */
    public function addPagosOrdenPago(\ADIF\ContableBundle\Entity\PagoOrdenPago $pagosOrdenPago) {
        $this->pagosOrdenPago[] = $pagosOrdenPago;

        return $this;
    }

    /**
     * Remove pagosOrdenPago
     *
     * @param \ADIF\ContableBundle\Entity\PagoOrdenPago $pagosOrdenPago
     */
    public function removePagosOrdenPago(\ADIF\ContableBundle\Entity\PagoOrdenPago $pagosOrdenPago) {
        $this->pagosOrdenPago->removeElement($pagosOrdenPago);
    }

    /**
     * Get pagosOrdenPago
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPagosOrdenPago() {
        return $this->pagosOrdenPago;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return Cheque
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
     * @param type $cuentaBancaria
     * @param type $fecha_inicio
     * @param type $fecha_fin
     * @return type
     */
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return
                $this->getIdCuenta() == $cuentaBancaria->getId() &&
                ($this->getEstadoNetCash() && ($this->getEstadoNetCash()->getDenominacion() != Constantes\ConstanteEstadoNetCash::ESTADO_GENERADO || $this->getEstadoNetCash()->getDenominacion() != Constantes\ConstanteEstadoNetCash::ESTADO_ENVIADO)) &&
                ($this->getPagoOrdenPago()->getOrdenPagoPagada() != null) &&
                ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) &&
                ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true);
    }

    /**
     * 
     * @return type
     */
    public function getConcepto() {
        return 'Net Cash N&ordm; ' . $this->getNumero();
    }

    /**
     * 
     * @return type
     */
    public function getReferencia() {
        return $this->getNumero();
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        return 'Net Cash';
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
                ? $this->getPagoOrdenPago()->getOrdenPagoPagada()->getMontoNeto() //
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
        return false;
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
