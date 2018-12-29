<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Definitivo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Provisorio
 *
 * @author Manuel Becerra
 * created 06/10/2014
 * 
 * @ORM\Table(name="provisorio")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ProvisorioRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "provisorio_general" = "Provisorio",
 *      "provisorio_compra" = "ProvisorioCompra",
 *      "provisorio_sueldo" = "ProvisorioSueldo",
 *      "provisorio_obra" = "ADIF\ContableBundle\Entity\Obras\ProvisorioObra",
 *      "provisorio_servicio" = "ProvisorioServicio"
 * })
 */
class Provisorio extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Provisorio")
     * @ORM\JoinColumn(name="id_provisorio_origen", referencedColumnName="id")
     */
    protected $provisorioOrigen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_provisorio", type="datetime", nullable=false)
     */
    protected $fechaProvisorio;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaContable
     *
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cuentaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaObjetoGasto")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_objeto_gasto", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cuentaPresupuestariaObjetoGasto;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica", inversedBy="provisorios")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id", nullable=false)
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="provisorios")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria", referencedColumnName="id", nullable=false)
     * 
     */
    protected $cuentaPresupuestaria;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_manual", type="boolean", nullable=false)
     */
    protected $esManual;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="El detalle no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $detalle;

    /**
     * @ORM\OneToOne(targetEntity="Definitivo", mappedBy="provisorio")
     * */
    protected $definitivo;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaProvisorio = new \DateTime();
        $this->esManual = true;
    }

    /**
     * Clone
     */
    public function __clone() {
        $this->id = null;
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
     * Set provisorioOrigen
     *
     * @param \ADIF\ContableBundle\Entity\Provisorio $provisorioOrigen
     * @return Provisorio
     */
    public function setProvisorioOrigen(\ADIF\ContableBundle\Entity\Provisorio $provisorioOrigen = null) {
        $this->provisorioOrigen = $provisorioOrigen;

        return $this;
    }

    /**
     * Get provisorioOrigen
     *
     * @return \ADIF\ContableBundle\Entity\Provisorio 
     */
    public function getProvisorioOrigen() {
        return $this->provisorioOrigen;
    }

    /**
     * Set fechaProvisorio
     *
     * @param \DateTime $fechaProvisorio
     * @return Provisorio
     */
    public function setFechaProvisorio($fechaProvisorio) {
        $this->fechaProvisorio = $fechaProvisorio;

        return $this;
    }

    /**
     * Get fechaProvisorio
     *
     * @return \DateTime 
     */
    public function getFechaProvisorio() {
        return $this->fechaProvisorio;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return Provisorio
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable = null) {
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
     * Set cuentaPresupuestariaObjetoGasto
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentaPresupuestariaObjetoGasto
     * @return Provisorio
     */
    public function setCuentaPresupuestariaObjetoGasto(\ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentaPresupuestariaObjetoGasto = null) {
        $this->cuentaPresupuestariaObjetoGasto = $cuentaPresupuestariaObjetoGasto;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaObjetoGasto
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto 
     */
    public function getCuentaPresupuestariaObjetoGasto() {
        return $this->cuentaPresupuestariaObjetoGasto;
    }

    /**
     * Set cuentaPresupuestariaEconomica
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return Provisorio
     */
    public function setCuentaPresupuestariaEconomica(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica = null) {
        $this->cuentaPresupuestariaEconomica = $cuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaEconomica
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica 
     */
    public function getCuentaPresupuestariaEconomica() {
        return $this->cuentaPresupuestariaEconomica;
    }

    /**
     * Set cuentaPresupuestaria
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestaria
     * @return Provisorio
     */
    public function setCuentaPresupuestaria(\ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestaria = null) {
        $this->cuentaPresupuestaria = $cuentaPresupuestaria;

        return $this;
    }

    /**
     * Get cuentaPresupuestaria
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestaria 
     */
    public function getCuentaPresupuestaria() {
        return $this->cuentaPresupuestaria;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return Provisorio
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set esManual
     *
     * @param boolean $esManual
     * @return Provisorio
     */
    public function setEsManual($esManual) {
        $this->esManual = $esManual;

        return $this;
    }

    /**
     * Get esManual
     *
     * @return boolean 
     */
    public function getEsManual() {
        return $this->esManual;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return Provisorio
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
        return html_entity_decode($this->detalle, ENT_QUOTES);
    }

    /**
     * Set definitivo
     *
     * @param Definitivo $definitivo
     * @return Provisorio
     */
    public function setDefinitivo(Definitivo $definitivo = null) {
        $this->definitivo = $definitivo;
        return $this;
    }

    /**
     * Get definitivo
     *
     * @return \ADIF\ContableBundle\Entity\Definitivo 
     */
    public function getDefinitivo() {
        return $this->definitivo;
    }

    /**
     * 
     * @return type
     */
    public function getSaldo() {

        $saldo = $this->getMonto();

        if ($this->definitivo != null) {

            return 0;
        }

        return $saldo;
    }

}
