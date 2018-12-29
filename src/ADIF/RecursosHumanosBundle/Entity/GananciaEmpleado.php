<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GananciaEmpleado
 *
 * @author Manuel Becerra
 * created 25/07/2014
 * 
 * @ORM\Table(name="g_ganancia_empleado")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\GananciaEmpleadoRepository")
 */
class GananciaEmpleado {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado
     * 
     * @ORM\OneToOne(targetEntity="LiquidacionEmpleado", mappedBy="gananciaEmpleado", cascade={"all"})
     */
    private $liquidacionEmpleado;

    /**
     * @var integer
     * @ORM\Column(name="haber_neto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $haberNeto;

    /**
     * @var integer
     * @ORM\Column(name="resultado_neto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $resultadoNeto;

    /**
     * @var integer
     * @ORM\Column(name="diferencia", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $diferencia;

    /**
     * @var integer
     * @ORM\Column(name="total_deducciones", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $totalDeducciones;

    /**
     * @var integer
     * @ORM\Column(name="ganancia_sujeta_impuesto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $gananciaSujetaImpuesto;

    /**
     * @var integer
     * @ORM\Column(name="porcentaje_a_sumar", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeASumar;

    /**
     * @var integer
     * @ORM\Column(name="monto_fijo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoFijo;

    /**
     * @var integer
     * @ORM\Column(name="monto_sin_excedente", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoSinExcedente;

    /**
     * @var integer
     * @ORM\Column(name="excedente", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $excedente;

    /**
     * @var integer
     * @ORM\Column(name="total_impuesto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $totalImpuesto;

    /**
     * @var integer
     * @ORM\Column(name="saldo_impuesto_mes", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $saldoImpuestoMes;

    /**
     *
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado
     * 
     * @ORM\OneToMany(targetEntity="ConceptoGananciaCalculado", mappedBy="gananciaEmpleado", cascade={"all"})
     */
    protected $conceptos;

    /**
     * @var integer
     * @ORM\Column(name="haber_neto_acumulado", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $haberNetoAcumulado;
    
    /**
     * @var integer
     * @ORM\Column(name="impuesto_retenido_anual", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $impuestoRetenidoAnual;

	/**
     * @var integer
     * @ORM\Column(name="acumulado_conceptos_no_cambian_escala", type="decimal", precision=10, scale=2, nullable=true)
     */
	protected $acumuladoConceptosNoCambianEscala;
    /**
     * Constructor
     */
    public function __construct() {
        $this->conceptos = new ArrayCollection();
        $this->haberNeto = 0;
        $this->impuestoRetenidoAnual = 0;
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
     * Set haberNeto
     *
     * @param string $haberNeto
     * @return GananciaEmpleado
     */
    public function setHaberNeto($haberNeto) {
        $this->haberNeto = $haberNeto;

        return $this;
    }

    /**
     * Get haberNeto
     *
     * @return string 
     */
    public function getHaberNeto() {
        return $this->haberNeto;
    }

    /**
     * Set resultadoNeto
     *
     * @param string $resultadoNeto
     * @return GananciaEmpleado
     */
    public function setResultadoNeto($resultadoNeto) {
        $this->resultadoNeto = $resultadoNeto;

        return $this;
    }

    /**
     * Get resultadoNeto
     *
     * @return string 
     */
    public function getResultadoNeto() {
        return $this->resultadoNeto;
    }

    /**
     * Set diferencia
     *
     * @param string $diferencia
     * @return GananciaEmpleado
     */
    public function setDiferencia($diferencia) {
        $this->diferencia = $diferencia;

        return $this;
    }

    /**
     * Get diferencia
     *
     * @return string 
     */
    public function getDiferencia() {
        return $this->diferencia;
    }

    /**
     * Set totalDeducciones
     *
     * @param string $totalDeducciones
     * @return GananciaEmpleado
     */
    public function setTotalDeducciones($totalDeducciones) {
        $this->totalDeducciones = $totalDeducciones;

        return $this;
    }

    /**
     * Get totalDeducciones
     *
     * @return string 
     */
    public function getTotalDeducciones() {
        return $this->totalDeducciones;
    }

    /**
     * Set gananciaSujetaAImpuesto
     *
     * @param string $gananciaSujetaAImpuesto
     * @return GananciaEmpleado
     */
    public function setGananciaSujetaImpuesto($gananciaSujetaAImpuesto) {
        $this->gananciaSujetaImpuesto = $gananciaSujetaAImpuesto;

        return $this;
    }

    /**
     * Get gananciaSujetaAImpuesto
     *
     * @return string 
     */
    public function getGananciaSujetaImpuesto() {
        return $this->gananciaSujetaImpuesto;
    }

    /**
     * Set porcentajeASumar
     *
     * @param string $porcentajeASumar
     * @return GananciaEmpleado
     */
    public function setPorcentajeASumar($porcentajeASumar) {
        $this->porcentajeASumar = $porcentajeASumar;

        return $this;
    }

    /**
     * Get porcentajeASumar
     *
     * @return string 
     */
    public function getPorcentajeASumar() {
        return $this->porcentajeASumar;
    }

    /**
     * Set montoFijo
     *
     * @param string $montoFijo
     * @return GananciaEmpleado
     */
    public function setMontoFijo($montoFijo) {
        $this->montoFijo = $montoFijo;

        return $this;
    }

    /**
     * Get montoFijo
     *
     * @return string 
     */
    public function getMontoFijo() {
        return $this->montoFijo;
    }

    /**
     * Set montoSinExcedente
     *
     * @param string $montoSinExcedente
     * @return GananciaEmpleado
     */
    public function setMontoSinExcedente($montoSinExcedente) {
        $this->montoSinExcedente = $montoSinExcedente;

        return $this;
    }

    /**
     * Get montoSinExcedente
     *
     * @return string 
     */
    public function getMontoSinExcedente() {
        return $this->montoSinExcedente;
    }

    /**
     * Set excedente
     *
     * @param string $excedente
     * @return GananciaEmpleado
     */
    public function setExcedente($excedente) {
        $this->excedente = $excedente;

        return $this;
    }

    /**
     * Get excedente
     *
     * @return string 
     */
    public function getExcedente() {
        return $this->excedente;
    }

    /**
     * Set totalImpuesto
     *
     * @param string $totalImpuesto
     * @return GananciaEmpleado
     */
    public function setTotalImpuesto($totalImpuesto) {
        $this->totalImpuesto = $totalImpuesto;

        return $this;
    }

    /**
     * Get totalImpuesto
     *
     * @return string 
     */
    public function getTotalImpuesto() {
        return $this->totalImpuesto;
    }

    /**
     * Set saldoImpuestoMes
     *
     * @param string $saldoImpuestoMes
     * @return GananciaEmpleado
     */
    public function setSaldoImpuestoMes($saldoImpuestoMes) {
        $this->saldoImpuestoMes = $saldoImpuestoMes;

        return $this;
    }

    /**
     * Get saldoImpuestoMes
     *
     * @return string 
     */
    public function getSaldoImpuestoMes() {
        return $this->saldoImpuestoMes;
    }

    /**
     * Add conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado $conceptos
     * @return Ganancia
     */
    public function addConcepto(\ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado $conceptos) {
        $conceptos->setGananciaEmpleado($this);
        $this->conceptos[] = $conceptos;

        return $this;
    }

    /**
     * Remove conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado $conceptos
     */
    public function removeConcepto(\ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado $conceptos) {
        $this->conceptos->removeElement($conceptos);
    }

    /**
     * Get conceptos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConceptos() {
        return $this->conceptos;
    }

    /**
     * Set haberNetoAcumulado
     *
     * @param string $haberNetoAcumulado
     * @return GananciaEmpleado
     */
    public function setHaberNetoAcumulado($haberNetoAcumulado) {
        $this->haberNetoAcumulado = $haberNetoAcumulado;

        return $this;
    }

    /**
     * Get haberNetoAcumulado
     *
     * @return string 
     */
    public function getHaberNetoAcumulado() {
        return $this->haberNetoAcumulado;
    }


    /**
     * Set liquidacionEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleado
     * @return GananciaEmpleado
     */
    public function setLiquidacionEmpleado(\ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleado = null)
    {
        $this->liquidacionEmpleado = $liquidacionEmpleado;

        return $this;
    }

    /**
     * Get liquidacionEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado 
     */
    public function getLiquidacionEmpleado()
    {
        return $this->liquidacionEmpleado;
    }

    /**
     * Set impuestoRetenidoAnual
     *
     * @param string $impuestoRetenidoAnual
     * @return GananciaEmpleado
     */
    public function setImpuestoRetenidoAnual($impuestoRetenidoAnual)
    {
        $this->impuestoRetenidoAnual = $impuestoRetenidoAnual;

        return $this;
    }

    /**
     * Get impuestoRetenidoAnual
     *
     * @return string 
     */
    public function getImpuestoRetenidoAnual()
    {
        return $this->impuestoRetenidoAnual;
    }
	
	public function setAcumuladoConceptosNoCambianEscala($acumuladoConceptosNoCambianEscala)
	{
		$this->acumuladoConceptosNoCambianEscala = $acumuladoConceptosNoCambianEscala;
		
		return $this;
	}
	
	public function getAcumuladoConceptosNoCambianEscala()
	{
		return $this->acumuladoConceptosNoCambianEscala;
	}
}
