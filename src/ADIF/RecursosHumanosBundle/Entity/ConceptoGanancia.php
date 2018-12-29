<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Indica el ConceptoGanancia. 
 * 
 * Por Ejemplo: 
 *      > Prima de Seguro de Vida (Deducciones Generales).
 *      > Gastos de Sepelio (Deducciones Generales).
 *      > Donaciones (Resultado Neto).
 *      > Hijos (Diferencia).
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_concepto_ganancia")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\ConceptoGananciaRepository")
 */
class ConceptoGanancia {
    
    const __CODIGO_DEDUCCION_ESPECIAL = '213';
    const __CODIGO_MINIMO_NO_IMPONIBLE = '221';
    
    const __CODIGO_CUOTA_MEDICA_ASISTENCIAL = 'd_1';
    const __CODIGO_PRIMAS_DE_SEGURO = 'd_2';
    const __CODIGO_DONACIONES = 'd_3';
    const __CODIGO_HIPOTECARIO = 'd_4';
    const __CODIGO_SEPELIO = 'd_5';
    const __CODIGO_RETIRO = 'd_6';
    const __CODIGO_ASISTENCIA_SANITARIA = 'd_7';
    const __CODIGO_SERVICIO_DOMESTICO = 'd_8';
    const __CODIGO_JUBILATORIO = 'd_9';
    const __CODIGO_OBRA_SOCIAL = 'd_10';
	const __CODIGO_ALQUILER = 'd_22';
	

    const __CODIGO_SAC = 'e_1';
    const __CODIGO_OTRO_EMPLEADOR = 'e_2'; // Remuneraciones informadas de otro empleador
    const __CODIGO_AJUSTE_GANANCIA = 'e_3';
    const __CODIGO_AJUSTE_RETROACTIVO = 'e_4';
    const __CODIGO_AJUSTE_REINTEGRO = 'e_5';
	const __CODIGO_PRORRATEO_SAC = 'e_6';
    const __CODIGO_JUBILACION_OTRO_EMPLEADOR = 'e_7';
    const __CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR = 'e_8';
    const __CODIGO_SINDICAL_OTRO_EMPLEADOR = 'e_9';
    
    /**
     * El concepto "Retenciones otros empleos" es un concepto especial del form 572
     * que tiene que aparecer abajo del excel de IG y tiene que restar al saldo impuesto mes
     */
    const __CODIGO_RETENCION_OTROS_EMPLEADOR = 'e_10';

    const __CODIGO_OTRAS_CARGAS = 'p_0';
    const __CODIGO_CONYUGE = 'p_1';
    const __CODIGO_HIJOS = 'p_3';
    
    
    const __CODIGO_IMPUESTOS_SOBRE_CREDITOS_Y_DEBITOS = 'ret_6';
    const __CODIGO_PERCEPCIONES_Y_RETENCIONES_ADUANERAS = 'ret_12';
    const __CODIGO_COMPRAS_EN_EL_EXTERIOR = 'ret_13';
    const __CODIGO_IMPUESTOS_SOBRE_LOS_MOVIMIENTOS = 'ret_14';
    const __CODIGO_COMPRA_DE_PAQUETES_TURISTICOS = 'ret_15';
    const __CODIGO_COMPRA_DE_PASAJES = 'ret_16';
    const __CODIGO_COMPRA_DE_MONEDA = 'ret_17';
    const __CODIGO_ADQUISICION_DE_MONEDA = 'ret_18';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia
     *
     * @ORM\ManyToOne(targetEntity="TipoConceptoGanancia")
     * @ORM\JoinColumn(name="id_tipo_concepto_ganancia", referencedColumnName="id", nullable=false)
     * 
     */
    protected $tipoConceptoGanancia;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del tipo de concepto no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var integer
     * 
     * @ORM\Column(name="orden_aplicacion", type="integer", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El órden de aplicación debe ser de tipo numérico.")
     */
    protected $ordenAplicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_formulario_572", type="boolean", nullable=false)
     */
    protected $aplicaEnFormulario572;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_carga_familiar", type="boolean", nullable=false)
     */
    protected $esCargaFamiliar;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_572", type="string", length=10, nullable=true)
     * 
     */
    protected $codigo572;

    /**
     * @var boolean
     *
     * @ORM\Column(name="indica_sac", type="boolean", nullable=false)
     */
    protected $indicaSAC;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_ganancia_anual", type="boolean", nullable=false)
     */
    protected $aplicaGananciaAnual;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="f572_sobreescribe", type="boolean", nullable=false)
     */
    protected $f572Sobreescribe;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="tiene_detalle", type="boolean", nullable=false)
     */
    protected $tieneDetalle;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ordenAplicacion = 1;
        $this->aplicaEnFormulario572 = true;
        $this->esCargaFamiliar = false;
        $this->indicaSAC = false;
        $this->aplicaGananciaAnual = false;
        $this->f572Sobreescribe = false;
        $this->tieneDetalle = false;
    }

    /**
     * Campo a mostrar
     */
    public function __toString() {
        return $this->denominacion;
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return ConceptoGanancia
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
     * Set ordenAplicacion
     *
     * @param integer $ordenAplicacion
     * @return ConceptoGanancia
     */
    public function setOrdenAplicacion($ordenAplicacion) {
        $this->ordenAplicacion = $ordenAplicacion;

        return $this;
    }

    /**
     * Get ordenAplicacion
     *
     * @return integer 
     */
    public function getOrdenAplicacion() {
        return $this->ordenAplicacion;
    }

    /**
     * Set aplicaEnFormulario572
     *
     * @param boolean $aplicaEnFormulario572
     * @return ConceptoGanancia
     */
    public function setAplicaEnFormulario572($aplicaEnFormulario572) {
        $this->aplicaEnFormulario572 = $aplicaEnFormulario572;

        return $this;
    }

    /**
     * Get aplicaEnFormulario572
     *
     * @return boolean 
     */
    public function getAplicaEnFormulario572() {
        return $this->aplicaEnFormulario572;
    }

    /**
     * Set tipoConceptoGanancia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia $tipoConceptoGanancia
     * @return ConceptoGanancia
     */
    public function setTipoConceptoGanancia(\ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia $tipoConceptoGanancia) {
        $this->tipoConceptoGanancia = $tipoConceptoGanancia;

        return $this;
    }

    /**
     * Get tipoConceptoGanancia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia 
     */
    public function getTipoConceptoGanancia() {
        return $this->tipoConceptoGanancia;
    }

    /**
     * Set esCargaFamiliar
     *
     * @param boolean $esCargaFamiliar
     * @return ConceptoGanancia
     */
    public function setEsCargaFamiliar($esCargaFamiliar) {
        $this->esCargaFamiliar = $esCargaFamiliar;

        return $this;
    }

    /**
     * Get esCargaFamiliar
     *
     * @return boolean 
     */
    public function getEsCargaFamiliar() {
        return $this->esCargaFamiliar;
    }

    /**
     * Set codigo572
     *
     * @param string $codigo572
     * @return ConceptoGanancia
     */
    public function setCodigo572($codigo572) {
        $this->codigo572 = $codigo572;

        return $this;
    }

    /**
     * Get codigo572
     *
     * @return string 
     */
    public function getCodigo572() {
        return $this->codigo572;
    }

    /**
     * Set indicaSAC
     *
     * @param boolean $indicaSAC
     * @return ConceptoGanancia
     */
    public function setIndicaSAC($indicaSAC) {
        $this->indicaSAC = $indicaSAC;

        return $this;
    }

    /**
     * Get indicaSAC
     *
     * @return boolean 
     */
    public function getIndicaSAC() {
        return $this->indicaSAC;
    }


    /**
     * Set aplicaGananciaAnual
     *
     * @param boolean $aplicaGananciaAnual
     * @return ConceptoGanancia
     */
    public function setAplicaGananciaAnual($aplicaGananciaAnual)
    {
        $this->aplicaGananciaAnual = $aplicaGananciaAnual;

        return $this;
    }

    /**
     * Get aplicaGananciaAnual
     *
     * @return boolean 
     */
    public function getAplicaGananciaAnual()
    {
        return $this->aplicaGananciaAnual;
    }
    
    /**
     * Set f572Sobreescribe
     *
     * @param boolean $f572Sobreescribe
     * @return ConceptoGanancia
     */
    public function setF572Sobreescribe($f572Sobreescribe)
    {
        $this->f572Sobreescribe = $f572Sobreescribe;

        return $this;
    }

    /**
     * Get f572Sobreescribe
     *
     * @return boolean 
     */
    public function getF572Sobreescribe()
    {
        return $this->f572Sobreescribe;
    }
    
    /**
     * Set tieneDetalle
     *
     * @param boolean $tieneDetalle
     * @return ConceptoGanancia
     */
    public function setTieneDetalle($tieneDetalle)
    {
        $this->tieneDetalle = $tieneDetalle;

        return $this;
    }

    /**
     * Get tieneDetalle
     *
     * @return boolean 
     */
    public function getTieneDetalle()
    {
        return $this->tieneDetalle;
    }
}
