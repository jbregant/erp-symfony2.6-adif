<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable;

/**
 * DevolucionDinero
 * 
 * @ORM\Table(name="devolucion_dinero")
 * @ORM\Entity
 */
class DevolucionDinero extends MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var RendicionEgresoValor
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor", inversedBy="devoluciones", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_rendicion_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    protected $rendicionEgresoValor;

    /**
     * @var double
     * @ORM\Column(name="monto_devolucion", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $montoDevolucion;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta;

    /**
     * @var string
     * 
     * @ORM\Column(name="numero", type="string", length=50, unique=true, nullable=false)
     * 
     */
    protected $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=true)
     */
    protected $numeroReferencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_adif", type="datetime", nullable=false)
     */
    protected $fechaIngresoADIF;

    /**
     * Constructor
     */
    public function __construct() {
        $this->montoDevolucion = 0;
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
     * Set rendicionEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor
     * @return RendicionEgresoValor
     */
    public function setRendicionEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor) {
        $this->rendicionEgresoValor = $rendicionEgresoValor;

        return $this;
    }

    /**
     * Get egresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor 
     */
    public function getRendicionEgresoValor() {
        return $this->rendicionEgresoValor;
    }

    /**
     * Set montoDevolucion
     *
     * @param double $montoDevolucion
     * @return DevolucionDinero
     */
    public function setMontoDevolucion($montoDevolucion) {
        $this->montoDevolucion = $montoDevolucion;

        return $this;
    }

    /**
     * Get montoDevolucion
     *
     * @return double 
     */
    public function getMontoDevolucion() {
        return $this->montoDevolucion;
    }

    /**
     * Get montoDevolucion
     *
     * @return double 
     */
    public function getMonto() {
        return $this->montoDevolucion;
    }

    /**
     * 
     * @param type $idCuenta
     * @return DevolucionDinero
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * 
     * @return type
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
     * Set numero
     *
     * @param string $numero
     * @return DevolucionDinero
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }

    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return $this->rendicionEgresoValor->getEstadoRendicionEgresoValor()->getCodigo() == ConstanteEstadoRendicionEgresoValor::ESTADO_GENERADA && $this->idCuenta == $cuentaBancaria->getId() && ($fecha_inicio ? $this->getFechaCreacion() >= $fecha_inicio : true) && ($fecha_fin ? $this->getFechaCreacion() <= $fecha_fin : true);
    }

    public function getConcepto() {
        return 'Devolucion de dinero N&ordm;: ' . $this->numero;
    }

    public function getReferencia() {
        return $this->numero;
    }

    public function getTipo() {
        return 'Devoluci&oacute;n de dinero';
    }

    public function getFecha() {
        return $this->getFechaCreacion();
    }

    public function getMontoMovimiento($cuentaBancaria = null) {
        return $this->montoDevolucion * (-1);
    }

    public function getEsContabilizable() {
        return false;
    }

    public function getCodigo() {
        return 3;
    }

    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF
     * @return DevolucionDinero
     */
    public function setFechaIngresoADIF($fechaIngresoADIF) {
        $this->fechaIngresoADIF = $fechaIngresoADIF;

        return $this;
    }

    /**
     * Get fechaIngresoADIF
     *
     * @return \DateTime 
     */
    public function getFechaIngresoADIF() {
        return $this->fechaIngresoADIF;
    }

    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return DevolucionDinero
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

}
