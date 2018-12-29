<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Criteria;

/**
 * Concepto
 *
 * @ORM\Table(name="concepto", indexes={@ORM\Index(name="tipo_concepto", columns={"id_tipo_concepto"})})
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\ConceptosRepository")
 * @UniqueEntity(fields={"codigo","fechaBaja"}, ignoreNull=false, message="Ya existe otro concepto con ese código.")
 */
class Concepto extends BaseEntity {

    const __CODIGO_SAC_1_SEMESTRE = '51';
    const __CODIGO_SAC_2_SEMESTRE = '52';
    const __CODIGO_AJUSTE_GANANCIAS_SAC = '994';
    const __CODIGO_998 = '998';
    const __CODIGO_DEVOLUCION_649 = '998.1';
    const __CODIGO_DEVOLUCION_RESOLUCION_3770 = '998.2';
    const __CODIGO_GANANCIAS = '999';
	const __CODIGO_998_3 = '998.3';
    const __CODIGO_AJUSTE_LIQUIDACION_IMPUESTO_GANANCIAS = '998.4';
    //para f649
    const __CODIGO_JUBILACION = '100';
    const __CODIGO_OBRA_SOCIAL_3 = '101';
    const __CODIGO_LEY_19032 = '102';
    const __CODIGO_CUOTA_SINDICAL_UF = '103';
    const __CODIGO_APDFA_CUOTA_SINDICAL = '110';
    const __CODIGO_CUOTA_SINDICAL_APOC = '103.1';
    const __CODIGO_1011 = '101.1';
    const __CODIGO_1012 = '101.2'; // Anssal
	
	// contribuciones
	const __CODIGO_APORTE_PATRONAL_JUBILACION = '200';
	const __CODIGO_APORTE_PATRONAL_LEY_19032 = '201';
	const __CODIGO_APORTE_PATRONAL_ASIGNACIONES_FAMILIARES = '202';
	const __CODIGO_APORTE_PATRONAL_FONDO_NACIONAL_EMPLEO = '203';
	const __CODIGO_REGIMEN_NACIONAL_OBRAS_SOCIALES = '204';
	const __CODIGO_ART_FIJA = '205';
	const __CODIGO_ART_VARIABLE = '206';
	const __CODIGO_SEGURO_VIDA = '207';
	const __CODIGO_CONTRIBUCION_UF = '208';
	
    const __CODIGO_EMBARGO = '114';
    const __CODIGO_ANTICIPO_SUELDO = '119';
    const __CODIGO_ANTICIPO_NETO_NEGATIVO = '995';
    
    // Indeminizaciones - no remunerativo
    const __CODIGO_INDEMIZACION_ANTIGUEDAD = '94';
    const __CODIGO_INDEMIZACION_FALLECIMIENTO = '94.2';
    const __CODIGO_GRATIFICACION_ESPECIAL_EXTRAORNINARIA = '99.1';

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
     * @Gedmo\Timestampable(on="create")
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
     *
     * @var \ADIF\RecursosHumanosBundle\Entity\Convenio
     * 
     * @ORM\ManyToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\Convenio", inversedBy="conceptos", cascade={"persist"})
     * @ORM\JoinTable(name="concepto_convenio",
     *      joinColumns={@ORM\JoinColumn(name="id_concepto", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_convenio", referencedColumnName="id")}
     *  )
     */
    private $convenios;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoConcepto
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoConcepto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_concepto", referencedColumnName="id", nullable=false)
     * })
     */
    private $idTipoConcepto;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection 
     * 
     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\ConceptoVersion", mappedBy="concepto")
     * 
     */
    private $versiones;

    /**
     * @var ConceptoLiquidacionAdicional
     *
     * @ORM\OneToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\ConceptoLiquidacionAdicional", mappedBy="concepto")     
     */
    private $conceptoLiquidacionAdicional;

    /**
     * @var ConceptoLicenciaSAC
     *
     * @ORM\OneToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\ConceptoLicenciaSAC", mappedBy="concepto")     
     */
    private $conceptoLicenciaSAC;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;

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
	

    public function __construct() {
        $this->convenios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->versiones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->valor = 0;
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
     * Set codigo
     *
     * @param integer $codigo
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * @return Concepto
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
     * Add convenio
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Convenio $convenio
     * @return Concepto
     */
    public function addConvenio(\ADIF\RecursosHumanosBundle\Entity\Convenio $convenio) {
        $this->convenios[] = $convenio;

        return $this;
    }

    /**
     * Remove convenio
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Convenio $convenio
     */
    public function removeConvenio(\ADIF\RecursosHumanosBundle\Entity\Convenio $convenio) {
        $this->convenios->removeElement($convenio);
    }

    /**
     * Get convenio
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConvenios() {
        return $this->convenios;
    }

    /**
     * Set idTipoConcepto
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoConcepto $idTipoConcepto
     * @return Concepto
     */
    public function setIdTipoConcepto(\ADIF\RecursosHumanosBundle\Entity\TipoConcepto $idTipoConcepto = null) {
        $this->idTipoConcepto = $idTipoConcepto;

        return $this;
    }

    /**
     * Get idTipoConcepto
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoConcepto 
     */
    public function getIdTipoConcepto() {
        return $this->idTipoConcepto;
    }

    /**
     * Get getnombreCodigo
     *
     * @return String
     */
    public function getNombreCodigo() {
        return $this->descripcion . ' - Código: ' . $this->codigo;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVersiones() {
        return $this->versiones;
    }

    /**
     * Set versiones
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $versiones
     * @return \ADIF\RecursosHumanosBundle\Entity\Concepto
     */
    public function setVersiones(\Doctrine\Common\Collections\ArrayCollection $versiones) {
        $this->versiones = $versiones;
        return $this;
    }

    public function getUltimaVersion() {
        $criteria = Criteria::create()->orderBy(array("fechaVersion" => Criteria::DESC));
        return $this->versiones->matching($criteria)->first();
    }

    public function setUltimaVersion(ConceptoVersion $ultimaVersion) {
//        $this->ultimaVersion = $ultimaVersion;
//        return $this;
    }

    /**
     * Set conceptoLiquidacionAdicional
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoLiquidacionAdicional $conceptoLiquidacionAdicional
     * @return Concepto
     */
    public function setConceptoLiquidacionAdicional(\ADIF\RecursosHumanosBundle\Entity\ConceptoLiquidacionAdicional $conceptoLiquidacionAdicional) {
        $this->conceptoLiquidacionAdicional = $conceptoLiquidacionAdicional;

        return $this;
    }

    /**
     * Get conceptoLiquidacionAdicional
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoLiquidacionAdicional
     */
    public function getConceptoLiquidacionAdicional() {
        return $this->conceptoLiquidacionAdicional;
    }

    /**
     * Set conceptoLicenciaSAC
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoLicenciaSAC $conceptoLicenciaSAC
     * @return Concepto
     */
    public function setConceptoLicenciaSAC(\ADIF\RecursosHumanosBundle\Entity\ConceptoLicenciaSAC $conceptoLicenciaSAC) {
        $this->conceptoLicenciaSAC = $conceptoLicenciaSAC;

        return $this;
    }

    /**
     * Get conceptoLicenciaSAC
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoLicenciaSAC
     */
    public function getConceptoLicenciaSAC() {
        return $this->conceptoLicenciaSAC;
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
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {

        if (null != $cuentaContable) {
            $this->idCuentaContable = $cuentaContable->getId();
        } else {
            $this->idCuentaContable = null;
        }

        $this->cuentaContable = $cuentaContable;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set esAjuste
     *
     * @param boolean $esAjuste
     * @return Concepto
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
