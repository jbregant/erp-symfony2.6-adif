<?php

namespace ADIF\WarehouseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PresupuestoEjecucion
 *
 * @ORM\Table(name="presupuesto_ejecucion")
 * @ORM\Entity
 */
class PresupuestoEjecucion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_cuenta_presupuestaria_economica", type="string", length=255)
     */
    private $codigoCuentaPresupuestariaEconomica;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion_cuenta_presupuestaria_economica", type="string", length=255)
     */
    private $denominacionCuentaPresupuestariaEconomica;

    /**
     * @var integer
     *
     * @ORM\Column(name="monto_inicial", type="bigint")
     */
    private $montoInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="monto_actual", type="bigint")
     */
    private $montoActual;

    /**
     * @var string
     *
     * @ORM\Column(name="provisorio", type="decimal")
     */
    private $provisorio;

    /**
     * @var string
     *
     * @ORM\Column(name="definitivo", type="decimal")
     */
    private $definitivo;

    /**
     * @var string
     *
     * @ORM\Column(name="devengado", type="decimal")
     */
    private $devengado;

    /**
     * @var string
     *
     * @ORM\Column(name="ejecutado", type="decimal")
     */
    private $ejecutado;

    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="decimal")
     */
    private $saldo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ejercicio_contable_fecha_inicio", type="date")
     */
    private $ejercicioContableFechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ejercicio_contable_fecha_fin", type="date")
     */
    private $ejercicioContableFechaFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="presupuesto", type="integer")
     */
    private $presupuesto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="datetime")
     */
    private $fechaCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime")
     */
    private $fechaBaja;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigoCuentaPresupuestariaEconomica
     *
     * @param string $codigoCuentaPresupuestariaEconomica
     * @return PresupuestoEjecucion
     */
    public function setCodigoCuentaPresupuestariaEconomica($codigoCuentaPresupuestariaEconomica)
    {
        $this->codigoCuentaPresupuestariaEconomica = $codigoCuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get codigoCuentaPresupuestariaEconomica
     *
     * @return string 
     */
    public function getCodigoCuentaPresupuestariaEconomica()
    {
        return $this->codigoCuentaPresupuestariaEconomica;
    }

    /**
     * Set denominacionCuentaPresupuestariaEconomica
     *
     * @param string $denominacionCuentaPresupuestariaEconomica
     * @return PresupuestoEjecucion
     */
    public function setDenominacionCuentaPresupuestariaEconomica($denominacionCuentaPresupuestariaEconomica)
    {
        $this->denominacionCuentaPresupuestariaEconomica = $denominacionCuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get denominacionCuentaPresupuestariaEconomica
     *
     * @return string 
     */
    public function getDenominacionCuentaPresupuestariaEconomica()
    {
        return $this->denominacionCuentaPresupuestariaEconomica;
    }

    /**
     * Set montoInicial
     *
     * @param integer $montoInicial
     * @return PresupuestoEjecucion
     */
    public function setMontoInicial($montoInicial)
    {
        $this->montoInicial = $montoInicial;

        return $this;
    }

    /**
     * Get montoInicial
     *
     * @return integer 
     */
    public function getMontoInicial()
    {
        return $this->montoInicial;
    }

    /**
     * Set montoActual
     *
     * @param integer $montoActual
     * @return PresupuestoEjecucion
     */
    public function setMontoActual($montoActual)
    {
        $this->montoActual = $montoActual;

        return $this;
    }

    /**
     * Get montoActual
     *
     * @return integer 
     */
    public function getMontoActual()
    {
        return $this->montoActual;
    }

    /**
     * Set provisorio
     *
     * @param string $provisorio
     * @return PresupuestoEjecucion
     */
    public function setProvisorio($provisorio)
    {
        $this->provisorio = $provisorio;

        return $this;
    }

    /**
     * Get provisorio
     *
     * @return string 
     */
    public function getProvisorio()
    {
        return $this->provisorio;
    }

    /**
     * Set definitivo
     *
     * @param string $definitivo
     * @return PresupuestoEjecucion
     */
    public function setDefinitivo($definitivo)
    {
        $this->definitivo = $definitivo;

        return $this;
    }

    /**
     * Get definitivo
     *
     * @return string 
     */
    public function getDefinitivo()
    {
        return $this->definitivo;
    }

    /**
     * Set devengado
     *
     * @param string $devengado
     * @return PresupuestoEjecucion
     */
    public function setDevengado($devengado)
    {
        $this->devengado = $devengado;

        return $this;
    }

    /**
     * Get devengado
     *
     * @return string 
     */
    public function getDevengado()
    {
        return $this->devengado;
    }

    /**
     * Set ejecutado
     *
     * @param string $ejecutado
     * @return PresupuestoEjecucion
     */
    public function setEjecutado($ejecutado)
    {
        $this->ejecutado = $ejecutado;

        return $this;
    }

    /**
     * Get ejecutado
     *
     * @return string 
     */
    public function getEjecutado()
    {
        return $this->ejecutado;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     * @return PresupuestoEjecucion
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string 
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set ejercicioContableFechaInicio
     *
     * @param \DateTime $ejercicioContableFechaInicio
     * @return PresupuestoEjecucion
     */
    public function setEjercicioContableFechaInicio($ejercicioContableFechaInicio)
    {
        $this->ejercicioContableFechaInicio = $ejercicioContableFechaInicio;

        return $this;
    }

    /**
     * Get ejercicioContableFechaInicio
     *
     * @return \DateTime 
     */
    public function getEjercicioContableFechaInicio()
    {
        return $this->ejercicioContableFechaInicio;
    }

    /**
     * Set ejercicioContableFechaFin
     *
     * @param \DateTime $ejercicioContableFechaFin
     * @return PresupuestoEjecucion
     */
    public function setEjercicioContableFechaFin($ejercicioContableFechaFin)
    {
        $this->ejercicioContableFechaFin = $ejercicioContableFechaFin;

        return $this;
    }

    /**
     * Get ejercicioContableFechaFin
     *
     * @return \DateTime 
     */
    public function getEjercicioContableFechaFin()
    {
        return $this->ejercicioContableFechaFin;
    }

    /**
     * Set presupuesto
     *
     * @param integer $presupuesto
     * @return PresupuestoEjecucion
     */
    public function setPresupuesto($presupuesto)
    {
        $this->presupuesto = $presupuesto;

        return $this;
    }

    /**
     * Get presupuesto
     *
     * @return integer 
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return PresupuestoEjecucion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return PresupuestoEjecucion
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }
}
