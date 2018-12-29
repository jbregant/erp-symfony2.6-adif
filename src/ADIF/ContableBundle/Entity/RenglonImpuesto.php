<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonImpuesto
 *
 * @author Darío Rapetti
 * created 22/10/2014
 * 
 * @ORM\Table(name="renglon_impuesto")
 * @ORM\Entity 
 */
class RenglonImpuesto extends BaseAuditoria implements BaseAuditable {

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
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConceptoImpuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_impuesto", referencedColumnName="id", nullable=false)
     * })
     */
    private $conceptoImpuesto;
    
    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    private $monto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=500, nullable=true)
     */
    protected $detalle;

    /**
     * @var Comprobante
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Comprobante", inversedBy="renglonesImpuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    private $comprobante;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set conceptoImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPercepcion $conceptoImpuesto
     * @return RenglonImpuesto
     */
    public function setConceptoImpuesto(\ADIF\ContableBundle\Entity\ConceptoImpuesto $conceptoImpuesto) {
        $this->conceptoImpuesto = $conceptoImpuesto;

        return $this;
    }
    
    /**
     * Get conceptoImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoImpuesto
     */
    public function getConceptoImpuesto() {
        return $this->conceptoImpuesto;
    }
    
    /**
     * Set monto
     *
     * @param string $monto
     * @return RenglonImpuesto
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }
    
    /**
     * Set comprobante
     *
     * @param Comprobante $comprobante
     * @return RenglonImpuesto
     */
    public function setComprobante($comprobante) {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return Comprobante 
     */
    public function getComprobante() {
        return $this->comprobante;
    }
    
    /**
     * Set detalle
     *
     * @param string $detalle
     * @return RenglonComprobante
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

}
