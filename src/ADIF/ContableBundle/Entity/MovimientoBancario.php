<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MovimientoBancario
 *
 * @author Augusto Villa Monte
 * created 21/01/2015
 * 
 * 
 * Description of MovimientoBancario
 *
 * 
 * @ORM\Table(name="movimiento_bancario")
 * @ORM\Entity
 * @UniqueEntity("numeroReferencia", message="El número de referencia ingresado ya se encuentra en uso.")
 */
class MovimientoBancario extends ConciliacionBancaria\MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_cuenta_origen", type="integer", nullable=false)
     */
    protected $idCuentaOrigen;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuentaOrigen;

    /**
     * @ORM\Column(name="id_cuenta_destino", type="integer", nullable=false)
     */
    protected $idCuentaDestino;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuentaDestino;

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
     * @ORM\OneToMany(targetEntity="OrdenPagoMovimientoBancario", mappedBy="movimientoBancario")
     */
    protected $ordenesPago;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->ordenesPago = new ArrayCollection();
    }

    /**
     * Set idCuentaOrigen
     *
     * @param integer $idCuentaOrigen
     * @return MovimientoBancario
     */
    public function setIdCuentaOrigen($idCuentaOrigen) {
        $this->idCuentaOrigen = $idCuentaOrigen;

        return $this;
    }

    /**
     * Get idCuentaOrigen
     *
     * @return integer 
     */
    public function getIdCuentaOrigen() {
        return $this->idCuentaOrigen;
    }

    /**
     * Set idCuentaDestino
     *
     * @param integer $idCuentaDestino
     * @return MovimientoBancario
     */
    public function setIdCuentaDestino($idCuentaDestino) {
        $this->idCuentaDestino = $idCuentaDestino;

        return $this;
    }

    /**
     * Get idCuentaDestino
     *
     * @return integer 
     */
    public function getIdCuentaDestino() {
        return $this->idCuentaDestino;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return MovimientoBancario
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
     * @return MovimientoBancario
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
     * @return MovimientoBancario
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
     * @return MovimientoBancario
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

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuentaOrigen
     */
    public function setCuentaOrigen($cuentaOrigen) {

        if (null != $cuentaOrigen) {
            $this->idCuentaOrigen = $cuentaOrigen->getId();
        } //.
        else {
            $this->idCuentaOrigen = null;
        }

        $this->cuentaOrigen = $cuentaOrigen;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaOrigen() {
        return $this->cuentaOrigen;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuentaDestino
     */
    public function setCuentaDestino($cuentaDestino) {

        if (null != $cuentaDestino) {
            $this->idCuentaDestino = $cuentaDestino->getId();
        } //.
        else {
            $this->idCuentaDestino = null;
        }

        $this->cuentaDestino = $cuentaDestino;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaDestino() {
        return $this->cuentaDestino;
    }

    /**
     * Add ordenesPago
     *
     * @param OrdenPagoMovimientoBancario $ordenesPago
     * @return MovimientoBancario
     */
    public function addOrdenesPago(OrdenPagoMovimientoBancario $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param OrdenPagoMovimientoBancario $ordenesPago
     */
    public function removeOrdenesPago(OrdenPagoMovimientoBancario $ordenesPago) {
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
     * 
     * @param type $cuentaBancaria
     * @param type $fecha_inicio
     * @param type $fecha_fin
     * @return type
     */
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return $this->getEstaOrdenPagoPagada() //
                //&& ($this->getIdCuentaOrigen() == $cuentaBancaria->getId() || $this->getIdCuentaDestino() == $cuentaBancaria->getId()) //
                && ($this->getIdCuentaDestino() == $cuentaBancaria->getId()) // solo destino porque en origen se muestra el cheque o transferencia de la OP
                && ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) //
                && ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true);
    }

    /**
     * 
     * @return type
     */
    public function getConcepto() {
        //return 'Movimiento Bancario N&ordm; ' . $this->getNumeroReferencia();
        return ($this->getTransferenciaOCheque() != null) ? $this->getTransferenciaOCheque()->getConcepto() : 'Movimiento Bancario N&ordm; ' . $this->getNumeroReferencia();
    }

    /**
     * 
     * @return type
     */
    public function getReferencia() {
        return !is_null($this->getTransferenciaOCheque()) ? $this->getTransferenciaOCheque()->getReferencia() : '';
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        return 'Movimiento';
    }

    /**
     * 
     * @param type $cuentaBancaria
     * @return type
     */
    public function getMontoMovimiento($cuentaBancaria = null) {
        return $this->getIdCuentaOrigen() == $cuentaBancaria->getId() //
                ? $this->getMonto() //
                : ($this->getMonto() * -1);
    }

    /**
     * 
     * @return int
     */
    public function getCodigoConcepto() {
        return !is_null($this->getTransferenciaOCheque()) ? $this->getTransferenciaOCheque()->getCodigoConcepto() : '';
    }

    /**
     * Get estaCreada
     * 
     * @return boolean
     */
    public function getEstaOrdenPagoPagada() {

        $estaOrdenPagoPagada = false;

        if (!$this->ordenesPago->isEmpty()) {

            /* @var $ordenPago OrdenPagoMovimientoBancario */
            foreach ($this->ordenesPago as $ordenPago) {

                if (null != $ordenPago->getEstadoOrdenPago()) {

                    if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA && $ordenPago->getNumeroOrdenPago() != null) {

                        $estaOrdenPagoPagada = true;

                        break;
                    }
                }
            }
        }

        return $estaOrdenPagoPagada;
    }

    private function getTransferenciaOCheque() {
        foreach ($this->getOrdenesPago() as $ordenPago) {
            if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                if ($ordenPago->getPagoOrdenPago()->getTransferencias() != null) {
                    return $ordenPago->getPagoOrdenPago()->getTransferencias()[0];
                } else {
                    return $ordenPago->getPagoOrdenPago()->getCheques()[0];
                }
            }
        }
        return null;
    }

    public function getFechaParaMayor() {
        return !is_null($this->getTransferenciaOCheque()) ? $this->getTransferenciaOCheque()->getFechaParaMayor() : '';
    }
    
    /**
     * 
     * @return boolean
     */
    public function getEsContabilizable() {
        return false;
    }    

}
