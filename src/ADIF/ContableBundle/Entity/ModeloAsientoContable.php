<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ModeloAsientoContable
 *
 * @author Manuel Becerra
 * created 01/10/2014
 * 
 * @ORM\Table(name="modelo_asiento_contable")
 * @ORM\Entity
 */
class ModeloAsientoContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=1024, nullable=false)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="La denominacion no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionModeloAsientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="TipoAsientoContable")
     * @ORM\JoinColumn(name="id_tipo_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $tipoAsientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\ConceptoAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="ConceptoAsientoContable")
     * @ORM\JoinColumn(name="id_concepto_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $conceptoAsientoContable;

    /**
     *
     * @ORM\OneToMany(targetEntity="RenglonModeloAsientoContable", mappedBy="modeloAsientoContable", cascade={"persist", "remove"})
     */
    private $renglonesModeloAsientoContable;

    /**
     * Constructor
     */
    public function __construct() {
        $this->renglonesModeloAsientoContable = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionModeloAsientoContable;
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
     * Set denominacionModeloAsientoContable
     *
     * @param string $denominacionModeloAsientoContable
     * @return ModeloAsientoContable
     */
    public function setDenominacionModeloAsientoContable($denominacionModeloAsientoContable) {
        $this->denominacionModeloAsientoContable = $denominacionModeloAsientoContable;

        return $this;
    }

    /**
     * Get denominacionModeloAsientoContable
     *
     * @return string 
     */
    public function getDenominacionModeloAsientoContable() {
        return $this->denominacionModeloAsientoContable;
    }

    /**
     * Set tipoAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\TipoAsientoContable $tipoAsientoContable
     * @return ModeloAsientoContable
     */
    public function setTipoAsientoContable(\ADIF\ContableBundle\Entity\TipoAsientoContable $tipoAsientoContable = null) {
        $this->tipoAsientoContable = $tipoAsientoContable;

        return $this;
    }

    /**
     * Get tipoAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\TipoAsientoContable 
     */
    public function getTipoAsientoContable() {
        return $this->tipoAsientoContable;
    }

    /**
     * Set conceptoAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoAsientoContable $conceptoAsientoContable
     * @return AsientoContable
     */
    public function setConceptoAsientoContable(\ADIF\ContableBundle\Entity\ConceptoAsientoContable $conceptoAsientoContable = null) {
        $this->conceptoAsientoContable = $conceptoAsientoContable;

        return $this;
    }

    /**
     * Get conceptoAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoAsientoContable 
     */
    public function getConceptoAsientoContable() {
        return $this->conceptoAsientoContable;
    }

    /**
     * Add renglonesModeloAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\RenglonModeloAsientoContable $renglonModeloAsientoContable
     * @return ModeloAsientoContable
     */
    public function addRenglonesModeloAsientoContable(\ADIF\ContableBundle\Entity\RenglonModeloAsientoContable $renglonModeloAsientoContable) {

        $renglonModeloAsientoContable->setModeloAsientoContable($this);

        $this->renglonesModeloAsientoContable[] = $renglonModeloAsientoContable;

        return $this;
    }

    /**
     * Remove renglonesModeloAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\RenglonModeloAsientoContable $renglonesModeloAsientoContable
     */
    public function removeRenglonesModeloAsientoContable(\ADIF\ContableBundle\Entity\RenglonModeloAsientoContable $renglonesModeloAsientoContable) {
        $this->renglonesModeloAsientoContable->removeElement($renglonesModeloAsientoContable);
    }

    /**
     * Get renglonesModeloAsientoContable
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesModeloAsientoContable() {
        return $this->renglonesModeloAsientoContable;
    }

}
