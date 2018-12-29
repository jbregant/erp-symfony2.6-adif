<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Devengado;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ejecutado
 *
 * @author Esteban Primost
 * created 02/12/2014
 * 
 * @ORM\Table(name="ejecutado")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\EjecutadoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "ejecutado_general" = "Ejecutado",
 *      "ejecutado_compra" = "EjecutadoCompra",
 *      "ejecutado_sueldo" = "EjecutadoSueldo",
 *      "ejecutado_anticipo_sueldo" = "EjecutadoAnticipoSueldo",
 *      "ejecutado_anticipo_proveedor" = "EjecutadoAnticipoProveedor",
 *      "ejecutado_cargas" = "EjecutadoCargas",
 *      "ejecutado_egreso_valor" = "ADIF\ContableBundle\Entity\EgresoValor\EjecutadoEgresoValor",
 *      "ejecutado_consultoria" = "EjecutadoConsultoria",
 *      "ejecutado_obra" = "ADIF\ContableBundle\Entity\Obras\EjecutadoObra",
 *      "ejecutado_orden_pago_general" = "EjecutadoOrdenPagoGeneral"
 * })
 */
class Ejecutado extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

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
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica", inversedBy="ejecutados")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="ejecutados")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria", referencedColumnName="id")
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
     * @ORM\OneToOne(targetEntity="Devengado", inversedBy="ejecutado")
     * @ORM\JoinColumn(name="id_devengado", referencedColumnName="id")
     */
    protected $devengado;

    /**
     * @ORM\ManyToOne(targetEntity="Definitivo", inversedBy="ejecutados")
     * @ORM\JoinColumn(name="id_definitivo", referencedColumnName="id")
     */
    protected $definitivo;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get fechaEjecutado
     *
     * @return \DateTime 
     */
    public function getFechaEjecutado() {

        if ($this->asientoContable != null) {
            return $this->asientoContable->getFechaContable();
        } else {
            return $this->fechaCreacion != null ? $this->fechaCreacion : new \DateTime();
        }
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return Ejecutado
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
     * Set asientoContable
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return Ejecutado
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
     * @return Ejecutado
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
     * @return Ejecutado
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
     * @return Ejecutado
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
     * @return Ejecutado
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
     * Set devengado
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengado
     * @return Ejecutado
     */
    public function setDevengado(\ADIF\ContableBundle\Entity\Devengado $devengado = null) {
        $this->devengado = $devengado;

        return $this;
    }

    /**
     * Get devengado
     *
     * @return \ADIF\ContableBundle\Entity\Devengado 
     */
    public function getDevengado() {
        return $this->devengado;
    }

    /**
     * Set definitivo
     *
     * @param Definitivo $definitivo
     * @return Ejecutado
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

}
