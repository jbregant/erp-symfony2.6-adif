<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ConceptoVersion
 *
 * @ORM\Table(name="concepto_version")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\ConceptoVersionRepository")
 */
class ConceptoVersion extends BaseEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Concepto
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Concepto", inversedBy="versiones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto", referencedColumnName="id", nullable=false)
     * })
     */
    private $concepto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_concepto", type="integer", nullable=false)
     */
    private $idTipoConcepto;

    /**
     * @var string
     *
     * @ORM\Column(name="convenios", type="string", nullable=true)
     */
    private $convenios;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="leyenda", type="string", length=255, nullable=false)
     */
    private $leyenda;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_tope", type="boolean", nullable=false)
     */
    private $aplicaTope;

    /**
     * @var boolean
     *
     * @ORM\Column(name="integra_sac", type="boolean", nullable=false)
     */
    private $integraSac;

    /**
     * @var boolean
     *
     * @ORM\Column(name="integra_ig", type="boolean", nullable=false)
     */
    private $integraIg;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_novedad", type="boolean", nullable=false)
     */
    private $esNovedad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="imprime_recibo", type="boolean", nullable=false)
     */
    private $imprimeRecibo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="imprime_ley", type="boolean", nullable=false)
     */
    private $imprimeLey;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_porcentaje", type="boolean", nullable=false)
     */
    private $esPorcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", precision=10, scale=4, nullable=false, options={"default": 0})
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="formula", type="string", length=255, nullable=false)
     */
    private $formula;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=false)
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_version", type="datetime", nullable=false)
     */
    private $fechaVersion;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_ajuste", type="boolean", nullable=false)
     */
    private $esAjuste;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_negativo", type="boolean", nullable=false)
     */
    private $esNegativo;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_indemnizatorio", type="boolean", nullable=false)
     */
    private $esIndemnizatorio;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="cambia_escala_impuesto", type="boolean", nullable=false)
     */
	private $cambiaEscalaImpuesto;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set concepto
     *
     * @param integer $concepto
     * @return ConceptoVersion
     */
    public function setConcepto($concepto) {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return integer 
     */
    public function getConcepto() {
        return $this->concepto;
    }

    /**
     * Set idTipoConcepto
     *
     * @param integer $idTipoConcepto
     * @return ConceptoVersion
     */
    public function setIdTipoConcepto($idTipoConcepto) {
        $this->idTipoConcepto = $idTipoConcepto;

        return $this;
    }

    /**
     * Get idTipoConcepto
     *
     * @return integer 
     */
    public function getIdTipoConcepto() {
        return $this->idTipoConcepto;
    }

    /**
     * Set convenios
     *
     * @param string $idConvenio
     * @return ConceptoVersion
     */
    public function setConvenios($convenios) {
        $this->convenios = $convenios;

        return $this;
    }

    /**
     * Get convenios
     *
     * @return string
     */
    public function getConvenios() {
        return $this->convenios;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return ConceptoVersion
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return ConceptoVersion
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
     * Set leyenda
     *
     * @param string $leyenda
     * @return ConceptoVersion
     */
    public function setLeyenda($leyenda) {
        $this->leyenda = $leyenda;

        return $this;
    }

    /**
     * Get leyenda
     *
     * @return string 
     */
    public function getLeyenda() {
        return $this->leyenda;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return ConceptoVersion
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * Set aplicaTope
     *
     * @param boolean $aplicaTope
     * @return ConceptoVersion
     */
    public function setAplicaTope($aplicaTope) {
        $this->aplicaTope = $aplicaTope;

        return $this;
    }

    /**
     * Get aplicaTope
     *
     * @return boolean 
     */
    public function getAplicaTope() {
        return $this->aplicaTope;
    }

    /**
     * Set integraSac
     *
     * @param boolean $integraSac
     * @return ConceptoVersion
     */
    public function setIntegraSac($integraSac) {
        $this->integraSac = $integraSac;

        return $this;
    }

    /**
     * Get integraSac
     *
     * @return boolean 
     */
    public function getIntegraSac() {
        return $this->integraSac;
    }

    /**
     * Set integraIg
     *
     * @param boolean $integraIg
     * @return ConceptoVersion
     */
    public function setIntegraIg($integraIg) {
        $this->integraIg = $integraIg;

        return $this;
    }

    /**
     * Get integraIg
     *
     * @return boolean 
     */
    public function getIntegraIg() {
        return $this->integraIg;
    }

    /**
     * Set esNovedad
     *
     * @param boolean $esNovedad
     * @return ConceptoVersion
     */
    public function setEsNovedad($esNovedad) {
        $this->esNovedad = $esNovedad;

        return $this;
    }

    /**
     * Get esNovedad
     *
     * @return boolean 
     */
    public function getEsNovedad() {
        return $this->esNovedad;
    }

    /**
     * Set imprimeRecibo
     *
     * @param boolean $imprimeRecibo
     * @return ConceptoVersion
     */
    public function setImprimeRecibo($imprimeRecibo) {
        $this->imprimeRecibo = $imprimeRecibo;

        return $this;
    }

    /**
     * Get imprimeRecibo
     *
     * @return boolean 
     */
    public function getImprimeRecibo() {
        return $this->imprimeRecibo;
    }

    /**
     * Set imprimeLey
     *
     * @param boolean $imprimeLey
     * @return ConceptoVersion
     */
    public function setImprimeLey($imprimeLey) {
        $this->imprimeLey = $imprimeLey;

        return $this;
    }

    /**
     * Get imprimeLey
     *
     * @return boolean 
     */
    public function getImprimeLey() {
        return $this->imprimeLey;
    }

    /**
     * Set esPorcentaje
     *
     * @param boolean $esPorcentaje
     * @return ConceptoVersion
     */
    public function setEsPorcentaje($esPorcentaje) {
        $this->esPorcentaje = $esPorcentaje;

        return $this;
    }

    /**
     * Get esPorcentaje
     *
     * @return boolean 
     */
    public function getEsPorcentaje() {
        return $this->esPorcentaje;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ConceptoVersion
     */
    public function setValor($valor) {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor() {
        return $this->valor;
    }

    /**
     * Set formula
     *
     * @param string $formula
     * @return ConceptoVersion
     */
    public function setFormula($formula) {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string 
     */
    public function getFormula() {
        return $this->formula;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return ConceptoVersion
     */
    public function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta() {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return ConceptoVersion
     */
    public function setFechaBaja($fechaBaja) {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja() {
        return $this->fechaBaja;
    }

    /**
     * Set fechaVersion
     *
     * @param \DateTime $fechaVersion
     * @return ConceptoVersion
     */
    public function setFechaVersion($fechaVersion) {
        $this->fechaVersion = $fechaVersion;

        return $this;
    }

    /**
     * Get fechaVersion
     *
     * @return \DateTime 
     */
    public function getFechaVersion() {
        return $this->fechaVersion;
    }

    /**
     * Set idCuentaContable
     *
     * @param integer $idCuentaContable
     * @return Concepto
     */
    public function setIdCuentaContable($idCuentaContable) {
        $this->idCuentaContable = $idCuentaContable;

        return $this;
    }

    /**
     * Get idCuentaContable
     *
     * @return integer 
     */
    public function getIdCuentaContable() {
        return $this->idCuentaContable;
    }

    /**
     * Set esAjuste
     *
     * @param boolean $esAjuste
     * @return ConceptoVersion
     */
    public function setEsAjuste($esAjuste) {
        $this->esAjuste = $esAjuste;

        return $this;
    }

    /**
     * Get esAjuste
     *
     * @return boolean 
     */
    public function getEsAjuste() {
        return $this->esAjuste;
    }

    /**
     * Set esNegativo
     *
     * @param boolean $esNegativo
     * @return Concepto
     */
    public function setEsNegativo($esNegativo) {
        $this->esNegativo = $esNegativo;

        return $this;
    }

    /**
     * Get esNegativo
     *
     * @return boolean 
     */
    public function getEsNegativo() {
        return $this->esNegativo;
    }
    
    /**
     * Set esIndemnizatorio
     *
     * @param boolean $esIndemnizatorio
     * @return Concepto
     */
    public function setEsIndemnizatorio($esIndemnizatorio) {
        $this->esIndemnizatorio = $esIndemnizatorio;

        return $this;
    }

    /**
     * Get esIndemnizatorio
     *
     * @return boolean 
     */
    public function getEsIndemnizatorio() {
        return $this->esIndemnizatorio;
    }
	
	public function setCambiaEscalaImpuesto($cambiaEscalaImpuesto)
	{
		$this->cambiaEscalaImpuesto = $cambiaEscalaImpuesto;
		
		return $this;
	}
	
	public function getCambiaEscalaImpuesto()
	{
		return $this->cambiaEscalaImpuesto;
	}

}
