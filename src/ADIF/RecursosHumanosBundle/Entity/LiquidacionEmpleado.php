<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LiquidacionEmpleadoConcepto
 * 
 * @ORM\Table(name="liquidacion_empleado", indexes={@ORM\Index(name="liquidacion", columns={"id_liquidacion"}), @ORM\Index(name="empleado", columns={"id_empleado"})})
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\LiquidacionEmpleadoRepository")
 */
class LiquidacionEmpleado extends BaseEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Liquidacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Liquidacion", inversedBy="liquidacionEmpleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_liquidacion", referencedColumnName="id", nullable=false)
     * })
     */
    private $liquidacion;

    /**
     * @var Empleado
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="liquidacionEmpleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * })
     */
    private $empleado;

    /**
     * @var double
     * @ORM\Column(name="bruto_1", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $bruto1;

    /**
     * @var double
     * @ORM\Column(name="bruto_2", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $bruto2;

    /**
     * @var double
     * @ORM\Column(name="monto_remunerativo_con_tope", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoRemunerativoConTope;

    /**
     * @var double
     * @ORM\Column(name="descuentos", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $descuentos;

    /**
     * @var double
     * @ORM\Column(name="no_remunerativo", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $noRemunerativo;

    /**
     * @var double
     * @ORM\Column(name="neto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $neto;

    /**
     * @var double
     * @ORM\Column(name="redondeo", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $redondeo;

    /**
     *
     * @var LiquidacionEmpleadoConcepto
     * 
     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto", mappedBy="liquidacionEmpleado", cascade={"all"})
     */
    private $liquidacionEmpleadoConceptos;

    /**
     *
     * @var GananciaEmpleado
     * 
     * @ORM\OneToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado", inversedBy="liquidacionEmpleado", cascade={"all"})
     * @ORM\JoinColumn(name="id_ganancia_empleado", referencedColumnName="id")
     */
    private $gananciaEmpleado;

    /**
     * @var string
     * @ORM\Column(name="basico", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $basico;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Banco
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Banco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_banco", referencedColumnName="id", nullable=true)
     * })
     */
    private $banco;

    /**
     * @var string
     * @ORM\Column(name="cbu", type="string", length=255, nullable=true)
     * 
     */
    private $cbu;

    /**
     * @var string
     * @ORM\Column(name="contribuciones", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $contribuciones;


    /* ------ Campos subtotales ganancias ------ */

    /**
     * @var double
     * @ORM\Column(name="bruto_1_ganancias", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $bruto1Ganancias;

    /**
     * @var double
     * @ORM\Column(name="bruto_2_ganancias", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $bruto2Ganancias;

    /**
     * @var double
     * @ORM\Column(name="no_remunerativo_ganancias", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $noRemunerativoGanancias;

    /**
     *
     * @var GananciaEmpleadoResolucion
     * 
     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\GananciaEmpleadoResolucion", mappedBy="liquidacionEmpleado")
     */
    private $gananciasEmpleadoResolucion;
	
	
	/**
     * @var double
     * @ORM\Column(name="aportes", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $aportes;
	
	
	/**
     * @var double
     * @ORM\Column(name="monto_remunerativo_con_tope_menos_aportes", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoRemunerativoConTopeMenosAportes;
	
	
	/**
     * @var double
     * @ORM\Column(name="prorrateo_sac", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $prorrateoSac;
    
    /**
     * Este campo entra en vigencia apartir del junio del 2018 y es en casos cuando se arregla paritarias y 
     * son remunerativos y retroactivos a enero
     * @var double
     * @ORM\Column(name="adicional_remunerativo_retroactivo", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    private $adicionalRemunerativoRetroactivo;
	

    public function __construct() {
        $this->liquidacionEmpleadoConceptos = new ArrayCollection();
        $this->gananciasEmpleadoResolucion = new ArrayCollection();
    }

    /**
     * Set liquidacionEmpleadoConceptos
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return Liquidacion
     */
    public function setLiquidacionEmpleadoConceptos(ArrayCollection $liquidacionEmpleadoConceptos) {
        $this->liquidacionEmpleadoConceptos = $liquidacionEmpleadoConceptos;

        return $this;
    }

    /**
     * Get liquidacionEmpleadoConceptos
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getLiquidacionEmpleadoConceptos() {
        return $this->liquidacionEmpleadoConceptos;
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
     * Set liquidacion
     *
     * @param Liquidacion $liquidacion
     * @return LiquidacionEmpleadoConcepto
     */
    public function setLiquidacion($liquidacion) {
        $this->liquidacion = $liquidacion;

        return $this;
    }

    /**
     * Get liquidacion
     *
     * @return Liquidacion 
     */
    public function getLiquidacion() {
        return $this->liquidacion;
    }

    /**
     * Set empleado
     *
     * @param Empleado $empleado
     * @return LiquidacionEmpleadoConcepto
     */
    public function setEmpleado($empleado) {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return Empleado 
     */
    public function getEmpleado() {
        return $this->empleado;
    }

    /**
     * Set bruto1
     *
     * @param double $bruto1
     * @return LiquidacionEmpleado
     */
    public function setBruto1($bruto1) {
        $this->bruto1 = $bruto1;

        return $this;
    }

    /**
     * Get bruto1
     *
     * @return double 
     */
    public function getBruto1() {
        return $this->bruto1;
    }

    /**
     * Set bruto2
     *
     * @param double $bruto2
     * @return LiquidacionEmpleado
     */
    public function setBruto2($bruto2) {
        $this->bruto2 = $bruto2;

        return $this;
    }

    /**
     * Get bruto2
     *
     * @return double 
     */
    public function getBruto2() {
        return $this->bruto2;
    }

    /**
     * Set montoRemunerativoConTope
     *
     * @param double $montoRemunerativoConTope
     * @return LiquidacionEmpleado
     */
    public function setMontoRemunerativoConTope($montoRemunerativoConTope) {
        $this->montoRemunerativoConTope = $montoRemunerativoConTope;

        return $this;
    }

    /**
     * Get montoRemunerativoConTope
     *
     * @return double 
     */
    public function getMontoRemunerativoConTope() {
        return $this->montoRemunerativoConTope;
    }

    /**
     * Set descuentos
     *
     * @param double $descuentos
     * @return LiquidacionEmpleado
     */
    public function setDescuentos($descuentos) {
        $this->descuentos = $descuentos;

        return $this;
    }

    /**
     * Get descuentos
     *
     * @return double 
     */
    public function getDescuentos() {
        return $this->descuentos;
    }

    /**
     * Set noRemunerativo
     *
     * @param double $noRemunerativo
     * @return LiquidacionEmpleado
     */
    public function setNoRemunerativo($noRemunerativo) {
        $this->noRemunerativo = $noRemunerativo;

        return $this;
    }

    /**
     * Get noRemunerativo
     *
     * @return double 
     */
    public function getNoRemunerativo() {
        return $this->noRemunerativo;
    }

    /**
     * Set neto
     *
     * @param double $neto
     * @return LiquidacionEmpleado
     */
    public function setNeto($neto) {
        $this->neto = $neto;

        return $this;
    }

    /**
     * Get neto
     *
     * @return double 
     */
    public function getNeto() {
        return $this->neto;
    }

    /**
     * Set reodondeo
     *
     * @param double $redondeo
     * @return LiquidacionEmpleado
     */
    public function setRedondeo($redondeo) {
        $this->redondeo = $redondeo;

        return $this;
    }

    /**
     * Get redondeo
     *
     * @return double 
     */
    public function getRedondeo() {
        return $this->redondeo;
    }

    /**
     * Busca un concepto con codigo $codigo
     * 
     * @return LiquidacionEmpleadoConcepto
     */
    public function getConceptoCodigo($codigo) {
        $result = $this->liquidacionEmpleadoConceptos->filter(
                function($entry) use ($codigo) {
            return in_array($entry->getConceptoVersion()->getCodigo(), array($codigo));
        }
        );
        return (!$result->isEmpty() ? $result->first() : null);
    }

    /**
     * 
     * @return type
     */
    public function getHaberNetoGanancia() {
        $lecAportes = $this->liquidacionEmpleadoConceptos->filter(
                function($entry) {
            return in_array($entry->getConceptoVersion()->getIdTipoConcepto(), array(TipoConcepto::__APORTE));
        }
        );

        $total_aportes = 0;

        foreach ($lecAportes as $lecAporte) {
            $total_aportes+= $lecAporte->getMonto();
        }

        return $this->bruto1Ganancias + $this->bruto2Ganancias + $this->noRemunerativoGanancias - $total_aportes;
    }

    /**
     * Add liquidacionEmpleadoConceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto $liquidacionEmpleadoConceptos
     * @return LiquidacionEmpleado
     */
    public function addLiquidacionEmpleadoConcepto(\ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto $liquidacionEmpleadoConceptos) {
        $this->liquidacionEmpleadoConceptos[] = $liquidacionEmpleadoConceptos;

        return $this;
    }

    /**
     * Remove liquidacionEmpleadoConceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto $liquidacionEmpleadoConceptos
     */
    public function removeLiquidacionEmpleadoConcepto(\ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto $liquidacionEmpleadoConceptos) {
        $this->liquidacionEmpleadoConceptos->removeElement($liquidacionEmpleadoConceptos);
    }

    /**
     * Set gananciaEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado $gananciaEmpleado
     * @return LiquidacionEmpleado
     */
    public function setGananciaEmpleado(\ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado $gananciaEmpleado = null) {
        $this->gananciaEmpleado = $gananciaEmpleado;

        $gananciaEmpleado->setLiquidacionEmpleado($this);

        return $this;
    }

    /**
     * Get gananciaEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado 
     */
    public function getGananciaEmpleado() {
        return $this->gananciaEmpleado;
    }

    /**
     * Set basico
     *
     * @param string $basico
     * @return LiquidacionEmpleado
     */
    public function setBasico($basico) {
        $this->basico = $basico;

        return $this;
    }

    /**
     * Get basico
     *
     * @return string 
     */
    public function getBasico() {
        return $this->basico;
    }

    /**
     * Set banco
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Banco $banco
     * @return LiquidacionEmpleado
     */
    public function setBanco(\ADIF\RecursosHumanosBundle\Entity\Banco $banco = null) {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Banco 
     */
    public function getBanco() {
        return $this->banco;
    }

    /**
     * Set cbu
     *
     * @param string $cbu
     * @return LiquidacionEmpleado
     */
    public function setCbu($cbu) {
        $this->cbu = $cbu;

        return $this;
    }

    /**
     * Get cbu
     *
     * @return string 
     */
    public function getCbu() {
        return $this->cbu;
    }

    /**
     * Set contribuciones
     *
     * @param string $contribuciones
     * @return LiquidacionEmpleado
     */
    public function setContribuciones($contribuciones) {
        $this->contribuciones = $contribuciones;

        return $this;
    }

    /**
     * Get contribuciones
     *
     * @return string 
     */
    public function getContribuciones() {
        return $this->contribuciones;
    }

    /**
     * Set bruto1Ganancias
     *
     * @param double $bruto1
     * @return LiquidacionEmpleado
     */
    public function setBruto1Ganancias($bruto1) {
        $this->bruto1Ganancias = $bruto1;
        return $this;
    }

    /**
     * Get bruto1Ganancias
     *
     * @return double 
     */
    public function getBruto1Ganancias() {
        return $this->bruto1Ganancias;
    }

    /**
     * Set bruto2Ganancias
     *
     * @param double $bruto2
     * @return LiquidacionEmpleado
     */
    public function setBruto2Ganancias($bruto2) {
        $this->bruto2Ganancias = $bruto2;

        return $this;
    }

    /**
     * Get bruto2Ganancias
     *
     * @return double 
     */
    public function getBruto2Ganancias() {
        return $this->bruto2Ganancias;
    }

    /**
     * Set noRemunerativoGanancias
     *
     * @param double $noRemunerativo
     * @return LiquidacionEmpleado
     */
    public function setNoRemunerativoGanancias($noRemunerativo) {
        $this->noRemunerativoGanancias = $noRemunerativo;

        return $this;
    }

    /**
     * Get noRemunerativoGanancias
     *
     * @return double 
     */
    public function getNoRemunerativoGanancias() {
        return $this->noRemunerativoGanancias;
    }
	
	public function setAportes($aportes) 
	{
		$this->aportes = $aportes;
		
		return $this;
	}
	
	public function getAportes()
	{
		return $this->aportes;
	}
	
	public function setMontoRemunerativoConTopeMenosAportes($montoRemunerativoConTopeMenosAportes)
	{
		$this->montoRemunerativoConTopeMenosAportes = $montoRemunerativoConTopeMenosAportes;
		
		return $this;
	}
	
	public function getMontoRemunerativoConTopeMenosAportes()
	{
		$this->montoRemunerativoConTopeMenosAportes;
	}
	
	public function setProrrateoSac($prorrateoSac)
	{
		$this->prorrateoSac = $prorrateoSac;
		
		return $this;
	}
	
	public function getProrrateoSac()
	{
		return $this->prorrateoSac;
	}
    
    public function setAdicionalRemunerativoRetroactivo($adicionalRemunerativoRetroactivo)
    {
        $this->adicionalRemunerativoRetroactivo = $adicionalRemunerativoRetroactivo;
        
        return $this;
    }
    
    public function getAdicionalRemunerativoRetroactivo()
    {
        return $this->adicionalRemunerativoRetroactivo;
    }
    

}
