<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Definitivo;
use ADIF\ContableBundle\Entity\Ejecutado;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Devengado
 *
 * @author Manuel Becerra
 * created 07/10/2014
 * 
 * @ORM\Table(name="devengado")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DevengadoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "devengado_general" = "Devengado",
 *      "devengado_compra" = "DevengadoCompra",
 *      "devengado_venta" = "DevengadoVenta",
 *      "devengado_sueldo" = "DevengadoSueldo",
 *      "devengado_cargas" = "DevengadoCargas",
 *      "devengado_obra" = "ADIF\ContableBundle\Entity\Obras\DevengadoObra",
 *      "devengado_consultoria" = "DevengadoConsultoria",
 *      "devengado_orden_pago_general" = "DevengadoOrdenPagoGeneral"
 * })
 */
class Devengado extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Devengado")
     * @ORM\JoinColumn(name="id_devengado_origen", referencedColumnName="id")
     */
    protected $devengadoOrigen;

    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     *
     * @ORM\ManyToOne(targetEntity="AsientoContable")
     * @ORM\JoinColumn(name="id_asiento_contable", referencedColumnName="id", nullable=true)
     * 
     */
    protected $asientoContable;

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
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica", inversedBy="devengados")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="devengados")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestaria;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * @ORM\ManyToOne(targetEntity="Definitivo", inversedBy="devengados")
     * @ORM\JoinColumn(name="id_definitivo", referencedColumnName="id")
     */
    protected $definitivo;

    /**
     * @ORM\OneToOne(targetEntity="Ejecutado", mappedBy="devengado")
     */
    protected $ejecutado;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Clone
     */
    public function __clone() {
        $this->id = null;
    }

    /**
     * Set devengadoOrigen
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengadoOrigen
     * @return Devengado
     */
    public function setDevengadoOrigen(\ADIF\ContableBundle\Entity\Devengado $devengadoOrigen = null) {
        $this->devengadoOrigen = $devengadoOrigen;

        return $this;
    }

    /**
     * Get devengadoOrigen
     *
     * @return \ADIF\ContableBundle\Entity\Devengado 
     */
    public function getDevengadoOrigen() {
        return $this->devengadoOrigen;
    }

    /**
     * Get fechaDevengado
     *
     * @return \DateTime 
     */
    public function getFechaDevengado() {

        if ($this->asientoContable != null) {
            return $this->asientoContable->getFechaContable();
        } else {
            return $this->fechaCreacion != null ? $this->fechaCreacion : new \DateTime();
        }
    }

    /**
     * Set asientoContable
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return Devengado
     */
    public function setAsientoContable(\ADIF\ContableBundle\Entity\AsientoContable $asientoContable = null) {
        $this->asientoContable = $asientoContable;

        return $this;
    }

    /**
     * Get asientoContable
     *
     * @return \ADIF\ContableBundle\Entity\AsientoContable 
     */
    public function getAsientoContable() {
        return $this->asientoContable;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return Devengado
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
     * @return Devengado
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
     * @return Devengado
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
     * @return Devengado
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
     * @return Devengado
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
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle() {

        if ($this->asientoContable != null) {
            return $this->asientoContable->getDenominacionAsientoContable();
        }

        return null;
    }

    /**
     * Set definitivo
     *
     * @param Definitivo $definitivo
     * @return Definitivo
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
     * Set ejecutado
     *
     * @param Ejecutado $ejecutado
     * @return Ejecutado
     */
    public function setEjecutado(Ejecutado $ejecutado = null) {
        $this->ejecutado = $ejecutado;
        return $this;
    }

    /**
     * Get ejecutado
     *
     * @return \ADIF\ContableBundle\Entity\Ejecutado 
     */
    public function getEjecutado() {
        return $this->ejecutado;
    }

    /**
     * 
     * @return type
     */
    public function getSaldo() {

        $saldo = $this->getMonto();

        if ($this->ejecutado != null) {

            return 0;
        }

        return $saldo;
    }

}
