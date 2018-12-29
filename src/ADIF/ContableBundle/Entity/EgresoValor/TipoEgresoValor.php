<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Define los distintos tipos de TipoEgresoValor:
 *  Caja chica
 *  Cargo a rendir
 *  Viáticos
 *  Combustible
 *
 * @author Manuel Becerra
 * created 14/01/2015
 * 
 * @ORM\Table(name="tipo_egreso_valor")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class TipoEgresoValor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permite_reposicion", type="boolean", nullable=false)
     */
    protected $permiteReposicion;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContable;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable_reconocimiento", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContablReconocimiento;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable_ganancia", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContablGanancia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="limita_persona", type="boolean", nullable=false)
     */
    protected $limitaPersona;

    /**
     * @var boolean
     *
     * @ORM\Column(name="limita_gerencia", type="boolean", nullable=false)
     */
    protected $limitaGerencia;

    /**
     * @ORM\Column(name="cantidad_maxima", type="integer", nullable=false)
     */
    protected $cantidadMaxima;

    /**
     * @var double
     * @ORM\Column(name="maximo_comprobante", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $maximoComprobante;

    /**
     * @var double
     * @ORM\Column(name="minimo_rendicion", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $minimoRendicion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->permiteReposicion = false;
        $this->limitaGerencia = false;
        $this->limitaPersona = false;
        $this->cantidadMaxima = 0;
        $this->maximoComprobante = 0;
        $this->minimoRendicion = 0;
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
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoEgresoValor
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return TipoEgresoValor
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set permiteReposicion
     *
     * @param boolean $permiteReposicion
     * @return TipoEgresoValor
     */
    public function setPermiteReposicion($permiteReposicion) {
        $this->permiteReposicion = $permiteReposicion;

        return $this;
    }

    /**
     * Get permiteReposicion
     *
     * @return boolean 
     */
    public function getPermiteReposicion() {
        return $this->permiteReposicion;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return TipoEgresoValor
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set cuentaContablReconocimiento
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContablReconocimiento
     * @return TipoEgresoValor
     */
    public function setCuentaContablReconocimiento(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContablReconocimiento = null) {
        $this->cuentaContablReconocimiento = $cuentaContablReconocimiento;

        return $this;
    }

    /**
     * Get cuentaContablReconocimiento
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContablReconocimiento() {
        return $this->cuentaContablReconocimiento;
    }

    /**
     * Set cuentaContablGanancia
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContablGanancia
     * @return TipoEgresoValor
     */
    public function setCuentaContablGanancia(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContablGanancia = null) {
        $this->cuentaContablGanancia = $cuentaContablGanancia;

        return $this;
    }

    /**
     * Get cuentaContablGanancia
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContablGanancia() {
        return $this->cuentaContablGanancia;
    }

    /**
     * Set limitaPersona
     *
     * @param boolean $limitaPersona
     * @return TipoEgresoValor
     */
    public function setLimitaPersona($limitaPersona) {
        $this->limitaPersona = $limitaPersona;

        return $this;
    }

    /**
     * Get limitaPersona
     *
     * @return boolean 
     */
    public function getLimitaPersona() {
        return $this->limitaPersona;
    }

    /**
     * Set limitaGerencia
     *
     * @param boolean $limitaGerencia
     * @return TipoEgresoValor
     */
    public function setLimitaGerencia($limitaGerencia) {
        $this->limitaGerencia = $limitaGerencia;

        return $this;
    }

    /**
     * Get limitaGerencia
     *
     * @return boolean 
     */
    public function getLimitaGerencia() {
        return $this->limitaGerencia;
    }

    /**
     * Set cantidadMaxima
     *
     * @param integer $cantidadMaxima
     * @return TipoEgresoValor
     */
    public function setCantidadMaxima($cantidadMaxima) {
        $this->cantidadMaxima = $cantidadMaxima;

        return $this;
    }

    /**
     * Get cantidadMaxima
     *
     * @return integer 
     */
    public function getCantidadMaxima() {
        return $this->cantidadMaxima;
    }

    /**
     * Set maximoComprobante
     *
     * @param string $maximoComprobante
     * @return TipoEgresoValor
     */
    public function setMaximoComprobante($maximoComprobante) {
        $this->maximoComprobante = $maximoComprobante;

        return $this;
    }

    /**
     * Get maximoComprobante
     *
     * @return string 
     */
    public function getMaximoComprobante() {
        return $this->maximoComprobante;
    }

    /**
     * Set minimoRendicion
     *
     * @param string $minimoRendicion
     * @return TipoEgresoValor
     */
    public function setMinimoRendicion($minimoRendicion) {
        $this->minimoRendicion = $minimoRendicion;

        return $this;
    }

    /**
     * Get minimoRendicion
     *
     * @return string 
     */
    public function getMinimoRendicion() {
        return $this->minimoRendicion;
    }

}
