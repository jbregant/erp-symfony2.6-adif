<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonRetencionLiquidacion
 *
 * @author Darío Rapetti
 * created 26/05/2015
 * 
 * @ORM\Table(name="renglon_retencion_liquidacion")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\RenglonRetencionLiquidacionRepository") 
 */
class RenglonRetencionLiquidacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="OrdenPagoRenglonRetencionLiquidacion", inversedBy="renglonesRetencionLiquidacion")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=true)
     * */
    protected $ordenPago;
    
    /**
     * @var \ADIF\ContableBundle\Entity\EstadoRenglonRetencionLiquidacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EstadoRenglonRetencionLiquidacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_renglon_retencion_liquidacion", referencedColumnName="id", nullable=false)
     * })
     */
    private $estadoRenglonRetencionLiquidacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="BeneficiarioLiquidacion")
     * @ORM\JoinColumn(name="id_beneficiario_liquidacion", referencedColumnName="id", nullable=false)
     */
    protected $beneficiarioLiquidacion;
    
    /**
     * @ORM\Column(name="id_liquidacion", type="integer", nullable=false)
     */
    protected $idLiquidacion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Liquidacion
     */
    protected $liquidacion;

    /**
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContable;
    
    /**
     * @ORM\Column(name="id_concepto", type="integer", nullable=false)
     */
    protected $idConceptoVersion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\ConceptoVersion
     */
    protected $conceptoVersion;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion $ordenPago
     * @return RenglonRetencionLiquidacion
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }
    
    /**
     * Set estadoRenglonRetencionLiquidacion
     *
     * @param \ADIF\ContableBundle\Entity\EstadoRenglonRetencionLiquidacion $estadoRenglonRetencionLiquidacion
     * @return RenglonRetencionLiquidacion
     */
    public function setEstadoRenglonRetencionLiquidacion(\ADIF\ContableBundle\Entity\EstadoRenglonRetencionLiquidacion $estadoRenglonRetencionLiquidacion) {
        $this->estadoRenglonRetencionLiquidacion = $estadoRenglonRetencionLiquidacion;

        return $this;
    }

    /**
     * Get estadoRenglonRetencionLiquidacion
     *
     * @return \ADIF\ContableBundle\Entity\EstadoRenglonRetencionLiquidacion
     */
    public function getEstadoRenglonRetencionLiquidacion() {
        return $this->estadoRenglonRetencionLiquidacion;
    }
    
    /**
     * Set beneficiarioLiquidacion
     *
     * @param \ADIF\ContableBundle\Entity\BeneficiarioLiquidacion $beneficiarioLiquidacion
     * @return RenglonRetencionLiquidacion
     */
    public function setBeneficiarioLiquidacion(\ADIF\ContableBundle\Entity\BeneficiarioLiquidacion $beneficiarioLiquidacion) {
        $this->beneficiarioLiquidacion = $beneficiarioLiquidacion;

        return $this;
    }

    /**
     * Get beneficiarioLiquidacion
     *
     * @return \ADIF\ContableBundle\Entity\BeneficiarioLiquidacion 
     */
    public function getBeneficiarioLiquidacion() {
        return $this->beneficiarioLiquidacion;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return RenglonRetencionLiquidacion
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto() {
        return $this->monto;
    }
    
    /**
     * Set idLiquidacion
     *
     * @param integer $idLiquidacion
     * @return RenglonRetencionLiquidacion
     */
    public function setIdLiquidacion($idLiquidacion) {
        $this->idLiquidacion = $idLiquidacion;

        return $this;
    }

    /**
     * Get idLiquidacion
     *
     * @return integer 
     */
    public function getIdLiquidacion() {
        return $this->idLiquidacion;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Liquidacion $liquidacion
     */
    public function setLiquidacion($liquidacion) {
        if (null != $liquidacion) {
            $this->idLiquidacion = $liquidacion->getId();
        } else {
            $this->idLiquidacion = null;
        }

        $this->liquidacion = $liquidacion;
    }

    /**
     * 
     * @return type
     */
    public function getLiquidacion() {
        return $this->liquidacion;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return RenglonRetencionLiquidacion
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
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
     * Set idConceptoVersion
     *
     * @param integer $idConceptoVersion
     * @return RenglonRetencionLiquidacion
     */
    public function setIdConceptoVersion($idConceptoVersion) {
        $this->idConceptoVersion = $idConceptoVersion;

        return $this;
    }

    /**
     * Get idConceptoVersion
     *
     * @return integer 
     */
    public function getIdConceptoVersion() {
        return $this->idConceptoVersion;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoVersion $conceptoVersion
     */
    public function setConceptoVersion($conceptoVersion) {
        if (null != $conceptoVersion) {
            $this->idConceptoVersion = $conceptoVersion->getId();
        } else {
            $this->idConceptoVersion = null;
        }

        $this->conceptoVersion = $conceptoVersion;
    }

    /**
     * 
     * @return type
     */
    public function getConceptoVersion() {
        return $this->conceptoVersion;
    }

}
