<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMoneda;

/**
 * Conciliacion
 *
 * @author Darío Rapetti
 * created 09/01/2015
 * 
 * @ORM\Table(name="conciliacion_bancaria_conciliacion")
 * @ORM\Entity
 */
class Conciliacion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
     */
    protected $fechaFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    protected $fechaCierre;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=false)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion", mappedBy="conciliacion", cascade={"all"})
     */
    protected $importacionesConciliacion;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable", mappedBy="conciliacion", cascade={"all"})
     */
    protected $movimientosConciliables;
    
    /**
     * @var double
     * @ORM\Column(name="saldo_extracto", type="decimal", precision=15, scale=2, nullable=true, options={"default": 0})
     * 
     */
    protected $saldoExtracto;    

    /**
     * @var double
     * @ORM\Column(name="saldo_mayor", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $saldoMayor;   

    /**
     * @var double
     * @ORM\Column(name="monto_partidas_conciliatorias", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $montoPartidasConciliatorias;      
      
    /**
     * @ORM\ManyToMany(targetEntity="MovimientoConciliable", inversedBy="conciliaciones")
     * @ORM\JoinTable(name="conciliacion_bancaria_conciliacion_movimiento_conciliable",
     *      joinColumns={@ORM\JoinColumn(name="id_conciliacion", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_movimiento_conciliable", referencedColumnName="id")}
     *      )
     */
    protected $partidasConciliatoriasMayor;
    
    /**
     * @ORM\ManyToMany(targetEntity="RenglonConciliacion", inversedBy="conciliaciones")
     * @ORM\JoinTable(name="conciliacion_bancaria_conciliacion_renglon_conciliacion",
     *      joinColumns={@ORM\JoinColumn(name="id_conciliacion", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_renglon_conciliacion", referencedColumnName="id")}
     *      )
     */
    protected $partidasConciliatoriasExtracto;    
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_extracto", type="datetime", nullable=false)
     */
    protected $fechaExtracto;
    
    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=15, scale=2, nullable=false, options={"default": 1})
     * 
     */    
    protected $tipoCambio;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_re_abierta", type="boolean", nullable=false)
     */
    protected $esReabierta;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->importacionesConciliacion = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partidasConciliatoriasMayor = new \Doctrine\Common\Collections\ArrayCollection();        
        $this->partidasConciliatoriasExtracto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoCambio = 1;
        $this->esReabierta = false;
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Conciliacion
     */
    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return Conciliacion
     */
    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin() {
        return $this->fechaFin;
    }

    /**
     * Set fechaCierre
     *
     * @param \DateTime $fechaCierre
     * @return Conciliacion
     */
    public function setFechaCierre($fechaCierre) {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre
     *
     * @return \DateTime 
     */
    public function getFechaCierre() {
        return $this->fechaCierre;
    }

    /**
     * Set ReAbierta
     *
     * @param \boolean
     * @return Conciliacion
     */
    public function setEsReAbierta($esReabierta) {
        $this->esReabierta = $esReabierta;

        return $this;
    }
    

    /**
     * Get ReAbierta
     *
     * @return \boolean 
     */
    public function getEsReAbierta() {
        return $this->esReabierta;
    }
    
    /**
     * 
     * @param type $idCuenta
     * @return \ADIF\ComprasBundle\Entity\Proveedor
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuenta
     */
    public function setCuenta($cuenta) {

        if (null != $cuenta) {
            $this->idCuenta = $cuenta->getId();
        } //.
        else {
            $this->idCuenta = null;
        }

        $this->cuenta = $cuenta;
    }

    /**
     * 
     * @return type
     */
    public function getCuenta() {
        return $this->cuenta;
    }

    /**
     * Add importacionesConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion $importacionesConciliacion
     * @return Conciliacion
     */
    public function addImportacionesConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion $importacionesConciliacion) {
        $this->importacionesConciliacion[] = $importacionesConciliacion;

        return $this;
    }

    /**
     * Remove importacionesConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion $importacionesConciliacion
     */
    public function removeImportacionesConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion $importacionesConciliacion) {
        $this->importacionesConciliacion->removeElement($importacionesConciliacion);
    }

    /**
     * Get importacionesConciliacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImportacionesConciliacion() {
        return $this->importacionesConciliacion;
    }

    /**
     * Add movimientosConciliables
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $movimientosConciliables
     * @return Conciliacion
     */
    public function addMovimientosConciliable(\ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $movimientosConciliables) {
        $this->movimientosConciliables[] = $movimientosConciliables;

        return $this;
    }

    /**
     * Remove movimientosConciliables
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $movimientosConciliables
     */
    public function removeMovimientosConciliable(\ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $movimientosConciliables) {
        $this->movimientosConciliables->removeElement($movimientosConciliables);
    }

    /**
     * Get movimientosConciliables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientosConciliables() {
        return $this->movimientosConciliables;
    }


    /**
     * Set saldoExtracto
     *
     * @param string $saldoExtracto
     * @return Conciliacion
     */
    public function setSaldoExtracto($saldoExtracto)
    {
        $this->saldoExtracto = $saldoExtracto;

        return $this;
    }

    /**
     * Get saldoExtracto
     * 
     * @param type $enMCL
     * @return string 
     */
    public function getSaldoExtracto()
    {
        return $this->saldoExtracto;
    }

    /**
     * Set saldoMayor
     *
     * @param string $saldoMayor
     * @return Conciliacion
     */
    public function setSaldoMayor($saldoMayor)
    {
        $this->saldoMayor = $saldoMayor;

        return $this;
    }

    /**
     * Get saldoMayor
     *
     * @return string 
     */
    public function getSaldoMayor()
    {
        return $this->saldoMayor;
    }

    /**
     * Set montoPartidasConciliatorias
     *
     * @param string $montoPartidasConciliatorias
     * @return Conciliacion
     */
    public function setMontoPartidasConciliatorias($montoPartidasConciliatorias)
    {
        $this->montoPartidasConciliatorias = $montoPartidasConciliatorias;

        return $this;
    }

    /**
     * Get montoPartidasConciliatorias
     *
     * @return string 
     */
    public function getMontoPartidasConciliatorias()
    {
        return $this->montoPartidasConciliatorias;
    }

    /**
     * Add partidasConciliatoriasMayor
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $partidasConciliatoriasMayor
     * @return Conciliacion
     */
    public function addPartidasConciliatoriasMayor(\ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $partidasConciliatoriasMayor)
    {
        $this->partidasConciliatoriasMayor[] = $partidasConciliatoriasMayor;

        return $this;
    }

    /**
     * Remove partidasConciliatoriasMayor
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $partidasConciliatoriasMayor
     */
    public function removePartidasConciliatoriasMayor(\ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable $partidasConciliatoriasMayor)
    {
        $this->partidasConciliatoriasMayor->removeElement($partidasConciliatoriasMayor);
    }

    /**
     * Get partidasConciliatoriasMayor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidasConciliatoriasMayor()
    {
        return $this->partidasConciliatoriasMayor;
    }

    /**
     * Add partidasConciliatoriasExtracto
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $partidasConciliatoriasExtracto
     * @return Conciliacion
     */
    public function addPartidasConciliatoriasExtracto(\ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $partidasConciliatoriasExtracto)
    {
        $this->partidasConciliatoriasExtracto[] = $partidasConciliatoriasExtracto;

        return $this;
    }

    /**
     * Remove partidasConciliatoriasExtracto
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $partidasConciliatoriasExtracto
     */
    public function removePartidasConciliatoriasExtracto(\ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $partidasConciliatoriasExtracto)
    {
        $this->partidasConciliatoriasExtracto->removeElement($partidasConciliatoriasExtracto);
    }

    /**
     * Get partidasConciliatoriasExtracto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidasConciliatoriasExtracto()
    {
        return $this->partidasConciliatoriasExtracto;
    }
   
    /**
     * Set fechaExtracto
     *
     * @param \DateTime $fecha_extracto
     *
     * @return Conciliacion
     */
    public function setFechaExtracto($fecha_extracto)
    {
        $this->fechaExtracto = $fecha_extracto;

        return $this;
    }

    /**
     * Get fechaExtracto
     *
     * @return \DateTime
     */
    public function getFechaExtracto()
    {
        return $this->fechaExtracto;
    }
    
    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return Conciliacion
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    }
    
    public function tieneTipoCambio() {
        return $this->getCuenta()->getTipoMoneda()->getCodigoTipoMoneda() != ConstanteTipoMoneda::PESO_ARGENTINO;
    }
    
    /**
     * Get saldoExtracto
     * 
     * @param type $enMCL
     * @return string 
     */
    public function getSaldoExtractoEnPesos()
    {
        return $this->saldoExtracto * $this->tipoCambio;
    }    
    
}
