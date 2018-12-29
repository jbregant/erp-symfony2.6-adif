<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Devengado;
use ADIF\ContableBundle\Entity\Provisorio;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Definitivo
 *
 * @author Manuel Becerra
 * created 07/10/2014
 * 
 * @ORM\Table(name="definitivo")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DefinitivoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "definitivo_general" = "Definitivo",
 *      "definitivo_compra" = "DefinitivoCompra",
 *      "definitivo_sueldo" = "DefinitivoSueldo",
 *      "definitivo_obra" = "DefinitivoObra",
 *      "definitivo_consultoria" = "DefinitivoConsultoria",
 *      "definitivo_contrato_venta" = "DefinitivoContratoVenta"
 * })
 */
class Definitivo extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Definitivo")
     * @ORM\JoinColumn(name="id_definitivo_origen", referencedColumnName="id")
     */
    protected $definitivoOrigen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_definitivo", type="datetime", nullable=false)
     */
    protected $fechaDefinitivo;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaContable
     *
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id")
     * 
     */
    protected $cuentaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaObjetoGasto")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_objeto_gasto", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaObjetoGasto;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica", inversedBy="definitivos")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="definitivos")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria", referencedColumnName="id", nullable=true)
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
     * @ORM\OneToOne(targetEntity="Provisorio", inversedBy="definitivo")
     * @ORM\JoinColumn(name="id_provisorio", referencedColumnName="id")
     */
    protected $provisorio;

    /**
     * @ORM\OneToMany(targetEntity="Devengado", mappedBy="definitivo")
     */
    protected $devengados;

    /**
     * @ORM\OneToMany(targetEntity="Ejecutado", mappedBy="definitivo")
     */
    protected $ejecutados;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaDefinitivo = new \DateTime();
        $this->devengados = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set definitivoOrigen
     *
     * @param \ADIF\ContableBundle\Entity\Definitivo $definitivoOrigen
     * @return Definitivo
     */
    public function setDefinitivoOrigen(\ADIF\ContableBundle\Entity\Definitivo $definitivoOrigen = null) {
        $this->definitivoOrigen = $definitivoOrigen;

        return $this;
    }

    /**
     * Get definitivoOrigen
     *
     * @return \ADIF\ContableBundle\Entity\Definitivo 
     */
    public function getDefinitivoOrigen() {
        return $this->definitivoOrigen;
    }

    /**
     * Set fechaDefinitivo
     *
     * @param \DateTime $fechaDefinitivo
     * @return Definitivo
     */
    public function setFechaDefinitivo($fechaDefinitivo) {
        $this->fechaDefinitivo = $fechaDefinitivo;

        return $this;
    }

    /**
     * Get fechaDefinitivo
     *
     * @return \DateTime 
     */
    public function getFechaDefinitivo() {
        return $this->fechaDefinitivo;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return Definitivo
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
     * @return Definitivo
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
     * @return Definitivo
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
     * @return Definitivo
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
     * @return Definitivo
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
     * Set detalle
     *
     * @param string $detalle
     * @return Definitivo
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
     * Set provisorio
     *
     * @param Provisorio $provisorio
     * @return Definitivo
     */
    public function setProvisorio(Provisorio $provisorio = null) {
        $this->provisorio = $provisorio;
        return $this;
    }

    /**
     * Get provisorio
     *
     * @return Provisorio
     */
    public function getProvisorio() {
        return $this->provisorio;
    }

    /**
     * Get montoTotal
     */
    public function getMontoTotal() {

        $montoTotal = 0;

        foreach ($this->getRenglones() as $renglon) {
            $montoTotal += $renglon->getMonto();
        }

        return $montoTotal;
    }

    /**
     * Add devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     * @return Definitivo
     */
    public function addDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados[] = $devengados;

        return $this;
    }

    /**
     * Remove devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     */
    public function removeDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados->removeElement($devengados);
    }

    /**
     * Get devengados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevengados() {
        return $this->devengados;
    }

    /**
     * Add ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     * @return Definitivo
     */
    public function addEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados[] = $ejecutados;

        return $this;
    }

    /**
     * Remove ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     */
    public function removeEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados->removeElement($ejecutados);
    }

    /**
     * Get ejecutados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEjecutados() {
        return $this->ejecutados;
    }

    /**
     * 
     * @return type
     */
    public function getSaldo() {

        $saldo = $this->getMonto();

        foreach ($this->devengados as $devengado) {
            $saldo -= $devengado->getMonto();
        }

        foreach ($this->ejecutados as $ejecutado) {
            $saldo -= $ejecutado->getMonto();
        }

        return $saldo;
    }

}
