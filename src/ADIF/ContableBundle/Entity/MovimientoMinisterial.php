<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MovimientoMinisterial
 *
 * @author Darío Rapetti
 * created 26/01/2015
 * 
 * 
 * Description of MovimientoMinisterial
 *
 * 
 * @ORM\Table(name="movimiento_ministerial")
 * @ORM\Entity
 * @UniqueEntity("numeroReferencia", message="El n&uacute;mero de referencia ya se encuentra en uso.")
 */
class MovimientoMinisterial extends ConciliacionBancaria\MovimientoConciliable {

    /**
     * @ORM\Column(name="id_cuenta_bancaria", type="integer", nullable=false)
     */
    protected $idCuentaBancariaADIF;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuentaBancariaADIF;

    /**
     * @ORM\ManyToOne(targetEntity="ConceptoTransaccionMinisterial")
     * @ORM\JoinColumn(name="id_concepto_transaccion_ministerial", referencedColumnName="id", nullable=false)
     */
    protected $conceptoTransaccionMinisterial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_ingreso", type="boolean", nullable=false)
     */
    private $esIngreso = true;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="text", nullable=true)
     */
    protected $detalle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, unique=true, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de referencia no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroReferencia;
    
    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="OrdenPagoMovimientoMinisterial", mappedBy="movimientoMinisterial")
     */
    protected $ordenesPago;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;    

        /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->ordenesPago = new ArrayCollection();
    }
    
    /**
     * Set idCuentaBancariaADIF
     *
     * @param integer $idCuentaBancariaADIF
     * @return MovimientoMinisterial
     */
    public function setIdCuentaBancariaADIF($idCuentaBancariaADIF) {
        $this->idCuentaBancariaADIF = $idCuentaBancariaADIF;

        return $this;
    }

    /**
     * Get idCuentaBancariaADIF
     *
     * @return integer 
     */
    public function getIdCuentaBancariaADIF() {
        return $this->idCuentaBancariaADIF;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuentaBancariaAdif
     */
    public function setCuentaBancariaADIF($cuentaBancariaAdif) {
        if (null != $cuentaBancariaAdif) {
            $this->idCuentaBancariaADIF = $cuentaBancariaAdif->getId();
        } else {
            $this->idCuentaBancariaADIF = null;
        }

        $this->cuentaBancariaADIF = $cuentaBancariaAdif;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaBancariaADIF() {
        return $this->cuentaBancariaADIF;
    }

    /**
     * Set conceptoTransaccionMinisterial
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoTransaccionMinisterial $conceptoTransaccionMinisterial
     * @return MovimientoMinisterial
     */
    public function setConceptoTransaccionMinisterial(\ADIF\ContableBundle\Entity\ConceptoTransaccionMinisterial $conceptoTransaccionMinisterial) {
        $this->conceptoTransaccionMinisterial = $conceptoTransaccionMinisterial;

        return $this;
    }

    /**
     * Get conceptoTransaccionMinisterial
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoTransaccionMinisterial 
     */
    public function getConceptoTransaccionMinisterial() {
        return $this->conceptoTransaccionMinisterial;
    }

    /**
     * Set esIngreso
     *
     * @param boolean $esIngreso
     * @return MovimientoMinisterial
     */
    public function setEsIngreso($esIngreso) {
        $this->esIngreso = $esIngreso;

        return $this;
    }

    /**
     * Get esIngreso
     *
     * @return boolean 
     */
    public function getEsIngreso() {
        return $this->esIngreso;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return MovimientoMinisterial
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
     * Set detalle
     *
     * @param string $detalle
     * @return MovimientoMinisterial
     */
    public function setDetalle($detalle) {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle() {
        return $this->detalle;
    }

    /**
     * Set fecha
     *
     * @param string $fecha
     * @return MovimientoMinisterial
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return string 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return MovimientoMinisterial
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return ($this->getEsIngreso() ? 
            ($this->getIdCuentaBancariaADIF() == $cuentaBancaria->getId()) && 
            ($this->getFechaAnulacion() == null) && 
            ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) && 
            ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true)
            : false) ;
    }

    public function getConcepto() {
        return 'Movimiento ministerial N&ordm;: ' . $this->getNumeroReferencia();
    }

    public function getReferencia() {
        return $this->getNumeroReferencia();
    }

    public function getTipo() {
        return 'MOVIMIENTO MINISTERIAL';
    }

    public function getMontoMovimiento($cuentaBancaria = null) {
        return $this->getEsIngreso() ? ($this->getMonto() * -1) : $this->getMonto();
    }

    public function getCodigoConcepto() {
        return 2;
    }

    
    /**
     * Add ordenesPago
     *
     * @param OrdenPagoMovimientoMinisterial $ordenesPago
     * @return MovimientoMinisterial
     */
    public function addOrdenesPago(OrdenPagoMovimientoMinisterial $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param OrdenPagoMovimientoMinisterial $ordenesPago
     */
    public function removeOrdenesPago(OrdenPagoMovimientoMinisterial $ordenesPago) {
        $this->ordenesPago->removeElement($ordenesPago);
    }

    /**
     * Get ordenesPago
     *
     * @return ArrayCollection
     */
    public function getOrdenesPago() {
        return $this->ordenesPago;
    }
    
    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return ComprobanteCompra
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }  
    
    /**
     * 
     * @return boolean
     */
    public function getEsContabilizable() {
        return false;
    }    
    
}
