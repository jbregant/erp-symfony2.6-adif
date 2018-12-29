<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonConciliacion
 *
 * @author Darío Rapetti
 * created 09/01/2015
 * 
 * @ORM\Table(name="conciliacion_bancaria_renglon_conciliacion")
 * @ORM\Entity
 */
class RenglonConciliacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_movimiento_bancario", type="datetime", nullable=false)
     */
    protected $fechaMovimientoBancario;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    protected $descripcion;

    /**
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion")
     * @ORM\JoinColumn(name="id_concepto_conciliacion", referencedColumnName="id", nullable=true)
     */
    protected $conceptoConciliacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=255, nullable=true)
     */
    protected $numeroReferencia;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $monto;

    /**
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion")
     * @ORM\JoinColumn(name="id_estado_renglon_conciliacion", referencedColumnName="id")
     * 
     */
    protected $estadoRenglonConciliacion;

    /**
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion", inversedBy="renglonesConciliacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_importacion_conciliacion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $importacionConciliacion;
    
    /**
     * @ORM\ManyToMany(targetEntity="Conciliacion", mappedBy="partidasConciliatoriasExtracto")
     * */
    protected $conciliaciones;
    
    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\AsientoContable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_asiento_contable_conciliacion", referencedColumnName="id", nullable=true)
     * })
     */
    protected $asientoContableConciliacion;
    
    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\AsientoContable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_asiento_contable_desconciliacion", referencedColumnName="id", nullable=true)
     * })
     */
    protected $asientoContableDesconciliacion;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_conciliacion", type="datetime", nullable=false)
     */
    protected $fechaConciliacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cheques = new \Doctrine\Common\Collections\ArrayCollection();
        $this->transferencias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->conciliaciones = new \Doctrine\Common\Collections\ArrayCollection();        
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
     * Set fechaMovimientoBancario
     *
     * @param \DateTime $fechaMovimientoBancario
     * @return RenglonConciliacion
     */
    public function setFechaMovimientoBancario($fechaMovimientoBancario) {
        $this->fechaMovimientoBancario = $fechaMovimientoBancario;

        return $this;
    }

    /**
     * Get fechaMovimientoBancario
     *
     * @return \DateTime 
     */
    public function getFechaMovimientoBancario() {
        return $this->fechaMovimientoBancario;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return RenglonConciliacion
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
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return RenglonConciliacion
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return RenglonConciliacion
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     * 
     * @param type $enMCL
     * @return string 
     */
    public function getMonto($enMCL = true) {
        return $this->monto * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * Set conceptoConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion $conceptoConciliacion
     * @return RenglonConciliacion
     */
    public function setConceptoConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion $conceptoConciliacion) {
        $this->conceptoConciliacion = $conceptoConciliacion;

        return $this;
    }

    /**
     * Get conceptoConciliacion
     *
     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion 
     */
    public function getConceptoConciliacion() {
        return $this->conceptoConciliacion;
    }

    /**
     * Set estadoRenglonConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion $estadoRenglonConciliacion
     * @return RenglonConciliacion
     */
    public function setEstadoRenglonConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion $estadoRenglonConciliacion = null) {
        $this->estadoRenglonConciliacion = $estadoRenglonConciliacion;

        return $this;
    }

    /**
     * Get estadoRenglonConciliacion
     *
     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion 
     */
    public function getEstadoRenglonConciliacion() {
        return $this->estadoRenglonConciliacion;
    }

    /**
     * Set importacionConciliacion
     *
     * @param ImportacionConciliacion $importacionConciliacion
     * @return RenglonConciliacion
     */
    public function setImportacionConciliacion($importacionConciliacion) {
        $this->importacionConciliacion = $importacionConciliacion;

        return $this;
    }

    /**
     * Get importacionConciliacion
     *
     * @return RenglonConciliacion
     */
    public function getImportacionConciliacion() {
        return $this->importacionConciliacion;
    }


    /**
     * Add conciliaciones
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones
     * @return RenglonConciliacion
     */
    public function addConciliacione(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones)
    {
        $this->conciliaciones[] = $conciliaciones;

        return $this;
    }

    /**
     * Remove conciliaciones
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones
     */
    public function removeConciliacione(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones)
    {
        $this->conciliaciones->removeElement($conciliaciones);
    }

    /**
     * Get conciliaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConciliaciones()
    {
        return $this->conciliaciones;
    }
    
    /**
     * 
     * @param type $enMCL
     * @return type
     */
    private function getTipoCambioCalculado($enMCL) {

        return $enMCL ? $this->getImportacionConciliacion()->getTipoCambio() : 1;
    }
    
    public function setAsientoContableConciliacion($asientoContable)
    {
        $this->asientoContableConciliacion = $asientoContable;
        
        return $this;
    }
    
    public function getAsientoContableConciliacion()
    {
        return $this->asientoContableConciliacion;
    }
    
    public function setAsientoContableDesconciliacion($asientoContable)
    {
        $this->asientoContableDesconciliacion = $asientoContable;
           
        return $this;
    }
    
    public function getAsientoContableDesconciliacion()
    {
        return $this->asientoContableDesconciliacion;
    }
    
    public function setFechaConciliacion($fecha)
    {
        $this->fechaConciliacion = $fecha;
        
        return $this;
    }
    
    public function getFechaConciliacion()
    {
        return $this->fechaConciliacion;
    }
    
}
