<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;

/**
 * ConceptoLiquidacionAdicional
 *
 * @ORM\Table(name="concepto_liquidacion_adicional")
 * @ORM\Entity
 */
class ConceptoLiquidacionAdicional extends BaseEntity {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Concepto
     *
     * @ORM\OneToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Concepto", inversedBy="conceptoLiquidacionAdicional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto", referencedColumnName="id", nullable=false)
     * })
     */
    private $concepto;

    /**
     * Set concepto
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Concepto $concepto
     * @return ConceptoLiquidacionAdicional
     */
    public function setConcepto(\ADIF\RecursosHumanosBundle\Entity\Concepto $concepto) {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Concepto 
     */
    public function getConcepto() {
        return $this->concepto;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
