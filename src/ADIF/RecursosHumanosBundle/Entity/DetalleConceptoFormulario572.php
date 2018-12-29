<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleConceptoFormulario572
 *
 * @ORM\Table(name="g_detalle_concepto_formulario_572")
 * @ORM\Entity
 */
class DetalleConceptoFormulario572 {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=255, nullable=true)
     */
    private $cuit;
    
    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=255, nullable=true)
     */
    private $detalle;
    
        /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572
     *
     * @ORM\OneToOne(targetEntity="ConceptoFormulario572", inversedBy="detalleConceptoFormulario572")
     * @ORM\JoinColumn(name="id_concepto_formulario_572", referencedColumnName="id")
     * 
     */
    protected $conceptoFormulario572;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return DetalleConceptoFormulario572
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return DetalleConceptoFormulario572
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set conceptoFormulario572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptoFormulario572
     * @return DetalleConceptoFormulario572
     */
    public function setConceptoFormulario572(\ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptoFormulario572 = null)
    {
        $this->conceptoFormulario572 = $conceptoFormulario572;
        $conceptoFormulario572->setDetalleConceptoFormulario572($this); 

        return $this;
    }

    /**
     * Get conceptoFormulario572
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 
     */
    public function getConceptoFormulario572()
    {
        return $this->conceptoFormulario572;
    }
}
