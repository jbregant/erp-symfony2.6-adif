<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Liquidacion
 * 
 * @ORM\Table(name="liquidacion")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\LiquidacionRepository")
 * @UniqueEntity("numero")
 * 
 */
class Liquidacion extends BaseEntity {

    /**
     * @var integer
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha_cierre_novedades", type="date", nullable=false)
     */
    private $fechaCierreNovedades;

    /**
     * @var integer
     * 
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    private $idUsuario;

    /**
     * @var \DateTime
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=false) 
     */
    private $fechaAlta;

    /**
     *
     * @var LiquidacionEmpleado
     * 
     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado", mappedBy="liquidacion", cascade={"all"})
     */
    private $liquidacionEmpleados;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="lugar_pago", type="string", length=255, nullable=true)
     */
    private $lugarPago;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha_pago", type="datetime", nullable=true) 
     */
    private $fechaPago;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha_ultimo_aporte", type="datetime", nullable=true) 
     */
    private $fechaUltimoAporte;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha_deposito_aporte", type="datetime", nullable=true) 
     */
    private $fechaDepositoAporte;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Banco
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Banco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_banco_aportes", referencedColumnName="id", nullable=true)
     * })
     */
    private $bancoAporte;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_liquidacion", referencedColumnName="id", nullable=false)
     * })
     */
    private $tipoLiquidacion;

    /**
     * @ORM\Column(name="id_renglon_declaracion_jurada_sicore", type="integer", nullable=true)
     */
    protected $idRenglonDeclaracionJuradaSicore;

    /**
     * @var ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     */
    protected $renglonDeclaracionJuradaSicore;
    
    /**
     * @ORM\Column(name="id_renglon_declaracion_jurada_sicoss", type="integer", nullable=true)
     */
    protected $idRenglonDeclaracionJuradaSicoss;
    
    /**
     * @var ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     */
    protected $renglonDeclaracionJuradaSicoss;
    
    /**
     * @ORM\Column(name="id_asiento_contable_sueldo", type="integer", nullable=true)
     */
    private $idAsientoContableSueldo;
    
    /**
     * @ORM\Column(name="id_asiento_contable_cargas_sociales", type="integer", nullable=true)
     */
    private $idAsientoContableCargasSociales;

    /**
     * Constructor
     */
    public function __construct() {
        $this->liquidacionEmpleados = new ArrayCollection();
        $this->fechaAlta = new \DateTime;
    }

    /**
     * Set liquidacionEmpleados
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return Liquidacion
     */
    public function setLiquidacionEmpleados(ArrayCollection $liquidacionEmpleados) {
        $this->liquidacionEmpleados = $liquidacionEmpleados;

        return $this;
    }

    /**
     * Get liquidacionEmpleados
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getLiquidacionEmpleados() {
        return $this->liquidacionEmpleados;
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
     * Set numero
     *
     * @param integer $numero
     * @return Liquidacion
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set fechaCierreNovedades
     *
     * @param \DateTime $fechaCierreNovedades
     * @return Liquidacion
     */
    public function setFechaCierreNovedades($fechaCierreNovedades) {
        $this->fechaCierreNovedades = $fechaCierreNovedades;

        return $this;
    }

    /**
     * Get fechaCierreNovedades
     *
     * @return \DateTime 
     */
    public function getFechaCierreNovedades() {
        return $this->fechaCierreNovedades;
    }

    /**
     * Set idUsuario
     *
     * @param integer $idUsuario
     * @return Liquidacion
     */
    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return integer 
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Liquidacion
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
     * Set observacion
     *
     * @param string $observacion
     * @return Liquidacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Add liquidacionEmpleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleados
     * @return Liquidacion
     */
    public function addLiquidacionEmpleado(\ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleados) {
        $this->liquidacionEmpleados[] = $liquidacionEmpleados;

        return $this;
    }

    /**
     * Remove liquidacionEmpleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleados
     */
    public function removeLiquidacionEmpleado(\ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleados) {
        $this->liquidacionEmpleados->removeElement($liquidacionEmpleados);
    }

    public function __toString() {
        setlocale(LC_ALL,"es_AR.UTF-8");
        return ucfirst(strftime("%B %Y", $this->getFechaCierreNovedades()->getTimestamp()));
        //return $this->getNumero() . ' (' . $this->getFechaCierreNovedades()->format('m/Y') . ')';
    }

    /**
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     * @return Liquidacion
     */
    public function setFechaPago($fechaPago) {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return \DateTime 
     */
    public function getFechaPago() {
        return $this->fechaPago;
    }

    /**
     * Set fechaUltimoAporte
     *
     * @param \DateTime $fechaUltimoAporte
     * @return Liquidacion
     */
    public function setFechaUltimoAporte($fechaUltimoAporte) {
        $this->fechaUltimoAporte = $fechaUltimoAporte;

        return $this;
    }

    /**
     * Get fechaUltimoAporte
     *
     * @return \DateTime 
     */
    public function getFechaUltimoAporte() {
        return $this->fechaUltimoAporte;
    }

    /**
     * Set fechaDepositoAporte
     *
     * @param \DateTime $fechaDepositoAporte
     * @return Liquidacion
     */
    public function setFechaDepositoAporte($fechaDepositoAporte) {
        $this->fechaDepositoAporte = $fechaDepositoAporte;

        return $this;
    }

    /**
     * Get fechaDepositoAporte
     *
     * @return \DateTime 
     */
    public function getFechaDepositoAporte() {
        return $this->fechaDepositoAporte;
    }

    /**
     * Set bancoAporte
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Banco $bancoAporte
     * @return Liquidacion
     */
    public function setBancoAporte(\ADIF\RecursosHumanosBundle\Entity\Banco $bancoAporte) {
        $this->bancoAporte = $bancoAporte;

        return $this;
    }

    /**
     * Get bancoAporte
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Banco 
     */
    public function getBancoAporte() {
        return $this->bancoAporte;
    }

    /**
     * Set lugarPago
     *
     * @param string $lugarPago
     * @return Liquidacion
     */
    public function setLugarPago($lugarPago) {
        $this->lugarPago = $lugarPago;

        return $this;
    }

    /**
     * Get lugarPago
     *
     * @return string 
     */
    public function getLugarPago() {
        return $this->lugarPago;
    }

    /**
     * Set tipoLiquidacion
     *
     * @param integer $tipoLiquidacion
     * @return Liquidacion
     */
    public function setTipoLiquidacion($tipoLiquidacion) {
        $this->tipoLiquidacion = $tipoLiquidacion;

        return $this;
    }

    /**
     * Get tipoLiquidacion
     *
     * @return integer 
     */
    public function getTipoLiquidacion() {
        return $this->tipoLiquidacion;
    }

    /**
     * Set idRenglonDeclaracionJuradaSicore
     *
     * @param integer $idRenglonDeclaracionJuradaSicore
     * @return Liquidacion
     */
    public function setIdRenglonDeclaracionJuradaSicore($idRenglonDeclaracionJuradaSicore) {
        $this->idRenglonDeclaracionJuradaSicore = $idRenglonDeclaracionJuradaSicore;

        return $this;
    }

    /**
     * Get idRenglonDeclaracionJuradaSicore
     *
     * @return integer 
     */
    public function getIdRenglonDeclaracionJuradaSicore() {
        return $this->idRenglonDeclaracionJuradaSicore;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJuradaSicore
     */
    public function setRenglonDeclaracionJuradaSicore($renglonDeclaracionJuradaSicore) {
        if (null != $renglonDeclaracionJuradaSicore) {
            $this->idRenglonDeclaracionJuradaSicore = $renglonDeclaracionJuradaSicore->getId();
        } else {
            $this->idRenglonDeclaracionJuradaSicore = null;
        }

        $this->renglonDeclaracionJuradaSicore = $renglonDeclaracionJuradaSicore;
    }

    /**
     * 
     * @return type
     */
    public function getRenglonDeclaracionJuradaSicore() {
        return $this->renglonDeclaracionJuradaSicore;
    }
    
    /**
     * Set idRenglonDeclaracionJuradaSicoss
     *
     * @param integer $idRenglonDeclaracionJuradaSicoss
     * @return Liquidacion
     */
    public function setIdRenglonDeclaracionJuradaSicoss($idRenglonDeclaracionJuradaSicoss) {
        $this->idRenglonDeclaracionJuradaSicoss = $idRenglonDeclaracionJuradaSicoss;

        return $this;
    }

    /**
     * Get idRenglonDeclaracionJuradaSicore
     *
     * @return integer 
     */
    public function getIdRenglonDeclaracionJuradaSicoss() {
        return $this->idRenglonDeclaracionJuradaSicoss;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJuradaSicoss
     */
    public function setRenglonDeclaracionJuradaSicoss($renglonDeclaracionJuradaSicoss) {
        if (null != $renglonDeclaracionJuradaSicoss) {
            $this->idRenglonDeclaracionJuradaSicoss = $renglonDeclaracionJuradaSicoss->getId();
        } else {
            $this->idRenglonDeclaracionJuradaSicoss = null;
        }

        $this->renglonDeclaracionJuradaSicoss = $renglonDeclaracionJuradaSicoss;
    }

    /**
     * 
     * @return type
     */
    public function getRenglonDeclaracionJuradaSicoss() {
        return $this->renglonDeclaracionJuradaSicoss;
    }
    
    public function setIdAsientoContableSueldo($idAsientoContableSueldo)
    {
        $this->idAsientoContableSueldo = $idAsientoContableSueldo;
        
        return $this;
    }
    
    public function getIdAsientoContableSueldo()
    {
        return $this->idAsientoContableSueldo;
    }
    
    public function setIdAsientoContableCargasSociales($idAsientoContableCargasSociales)
    {
        $this->idAsientoContableCargasSociales = $idAsientoContableCargasSociales;
        
        return $this;
    }
    
    public function getIdAsientoContableCargasSociales()
    {
        return $this->idAsientoContableCargasSociales;
    }

}
