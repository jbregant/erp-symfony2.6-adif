<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of RenglonAsientoContable
 *
 * @author Manuel Becerra
 * created 24/09/2014
 * 
 * @ORM\Table(name="renglon_asiento_contable")
 * @ORM\Entity
 */
class RenglonAsientoContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     *
     * @ORM\ManyToOne(targetEntity="AsientoContable", inversedBy="renglonesAsientoContable")
     * @ORM\JoinColumn(name="id_asiento_contable", referencedColumnName="id")
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
     * @var \ADIF\ContableBundle\Entity\TipoOperacionContable
     *
     * @ORM\ManyToOne(targetEntity="TipoOperacionContable")
     * @ORM\JoinColumn(name="id_tipo_operacion_contable", referencedColumnName="id")
     * 
     */
    protected $tipoOperacionContable;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoMoneda
     *
     * @ORM\ManyToOne(targetEntity="TipoMoneda")
     * @ORM\JoinColumn(name="id_tipo_moneda", referencedColumnName="id")
     * 
     */
    protected $tipoMoneda;

    /**
     * @var float
     * 
     * @ORM\Column(name="importe_mo", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe de moneda origen debe ser de tipo numérico.")
     */
    protected $importeMO;

    /**
     * @var float
     * 
     * @ORM\Column(name="importe_mcl", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe de moneda en curso legal debe ser de tipo numérico.")
     */
    protected $importeMCL;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="El detalle no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $detalle;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="es_provision_obra", type="boolean", nullable=false)
     */
    protected $esProvisionObra;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="id_contrato_siso", type="integer", nullable=true)
     */
    protected $idContratoSiso;
    
     /**
     * @var \ADIF\ContableBundle\Entity\CuentaContable
     *
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable_provision", referencedColumnName="id")
     * 
     */
    protected $cuentaContableProvision;
    
    /**
     * @var decimal
     * 
     * @ORM\Column(name="provision_monto", type="decimal", precision=20)
     */
    protected $provisionMonto;
    
    
    public function __construct() 
    {
        $this->esProvisionObra = false;
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
     * Set importeMO
     *
     * @param float $importeMO
     * @return RenglonAsientoContable
     */
    public function setImporteMO($importeMO) {
        $this->importeMO = $importeMO;

        return $this;
    }

    /**
     * Get importeMO
     *
     * @return float 
     */
    public function getImporteMO() {
        return $this->importeMO;
    }

    /**
     * Set importeMCL
     *
     * @param float $importeMCL
     * @return RenglonAsientoContable
     */
    public function setImporteMCL($importeMCL) {
        $this->importeMCL = $importeMCL;

        return $this;
    }

    /**
     * Get importeMCL
     *
     * @return float 
     */
    public function getImporteMCL() {
        return $this->importeMCL;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return RenglonAsientoContable
     */
    public function setDetalle($detalle) {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle() {
        return $this->detalle;
    }

    /**
     * Set asientoContable
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return RenglonAsientoContable
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
     * @return RenglonAsientoContable
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
     * Set tipoOperacionContable
     *
     * @param \ADIF\ContableBundle\Entity\TipoOperacionContable $tipoOperacionContable
     * @return RenglonAsientoContable
     */
    public function setTipoOperacionContable(\ADIF\ContableBundle\Entity\TipoOperacionContable $tipoOperacionContable = null) {
        $this->tipoOperacionContable = $tipoOperacionContable;

        return $this;
    }

    /**
     * Get tipoOperacionContable
     *
     * @return \ADIF\ContableBundle\Entity\TipoOperacionContable 
     */
    public function getTipoOperacionContable() {
        return $this->tipoOperacionContable;
    }

    /**
     * Set tipoMoneda
     *
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     * @return RenglonAsientoContable
     */
    public function setTipoMoneda(\ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda = null) {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return \ADIF\ContableBundle\Entity\TipoMoneda 
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }
    
    public function setEsProvisionObra($esProvisionObra)
    {
        $this->esProvisionObra = $esProvisionObra;
        
        return $this;
    }
    
    public function getEsProvisionObra()
    {
        return $this->esProvisionObra;
    }
    
    public function setIdContratoSiso($idContratoSiso)
    {
        $this->idContratoSiso = $idContratoSiso;
        
        return $this;
    }
    
    public function getIdContratoSiso()
    {
        return $this->idContratoSiso;
    }
    
     /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return RenglonAsientoContable
     */
    public function setCuentaContableProvision(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable = null) 
    {
        $this->cuentaContableProvision = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContableProvision() 
    {
        return $this->cuentaContableProvision;
    }
    
    public function setProvisionMonto($provisionMonto)
    {
        $this->provisionMonto = $provisionMonto;
        
        return $this;
    }
    
    public function getProvisionMonto()
    {
        return $this->provisionMonto;
    }

}
