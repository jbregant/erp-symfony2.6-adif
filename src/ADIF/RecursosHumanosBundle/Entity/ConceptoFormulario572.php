<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConceptoFormulario572
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_concepto_formulario_572")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\ConceptoFormulario572Repository")
 */
class ConceptoFormulario572 {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Formulario572
     *
     * @ORM\ManyToOne(targetEntity="Formulario572", inversedBy="conceptos")
     * @ORM\JoinColumn(name="id_formulario_572", referencedColumnName="id", nullable=false)
     * 
     */
    protected $formulario572;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia
     *
     * @ORM\ManyToOne(targetEntity="ConceptoGanancia")
     * @ORM\JoinColumn(name="id_concepto_ganancia", referencedColumnName="id", nullable=false)
     * 
     */
    protected $conceptoGanancia;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * @var integer
     * @ORM\Column(name="mes_desde", type="decimal", precision=2, nullable=false)
     */
    protected $mesDesde;

    /**
     * @var integer
     * @ORM\Column(name="mes_hasta", type="decimal", precision=2, nullable=false)
     */
    protected $mesHasta;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572
     *
     * @ORM\OneToOne(targetEntity="DetalleConceptoFormulario572", mappedBy="conceptoFormulario572", cascade={"all"})
     * 
     */
    protected $detalleConceptoFormulario572;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572
     *
     * @ORM\OneToOne(targetEntity="DetalleConceptoFormulario572Aplicado", mappedBy="conceptoFormulario572", cascade={"all"})
     * 
     */
    protected $detalleConceptoFormulario572Aplicado;

    /**
     * Constructor
     */
    public function __construct() {
        $this->mesDesde = 1;
        $this->mesHasta = 12;
    }

    /**
     * Campo a mostrar
     */
    public function __toString() {
        return $this->conceptoGanancia->getDenominacion();
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
     * Set monto
     *
     * @param float $monto
     * @return ConceptoFormulario572
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
     * Set formulario572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Formulario572 $formulario572
     * @return ConceptoFormulario572
     */
    public function setFormulario572(\ADIF\RecursosHumanosBundle\Entity\Formulario572 $formulario572) {
        $this->formulario572 = $formulario572;

        return $this;
    }

    /**
     * Get formulario572
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Formulario572 
     */
    public function getFormulario572() {
        return $this->formulario572;
    }

    /**
     * Set conceptoGanancia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia $conceptoGanancia
     * @return ConceptoFormulario572
     */
    public function setConceptoGanancia(\ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia $conceptoGanancia) {
        $this->conceptoGanancia = $conceptoGanancia;

        return $this;
    }

    /**
     * Get conceptoGanancia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia 
     */
    public function getConceptoGanancia() {
        return $this->conceptoGanancia;
    }

    /**
     * Set mesDesde
     *
     * @param string $mesDesde
     * @return ConceptoFormulario572
     */
    public function setMesDesde($mesDesde) {
        $this->mesDesde = $mesDesde;

        return $this;
    }

    /**
     * Get mesDesde
     *
     * @return string 
     */
    public function getMesDesde() {
        return $this->mesDesde;
    }

    /**
     * Set mesHasta
     *
     * @param string $mesHasta
     * @return ConceptoFormulario572
     */
    public function setMesHasta($mesHasta) {
        $this->mesHasta = $mesHasta;

        return $this;
    }

    /**
     * Get mesHasta
     *
     * @return string 
     */
    public function getMesHasta() {
        return $this->mesHasta;
    }

    /**
     * Set detalleConceptoFormulario572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572 $detalleConceptoFormulario572
     * @return ConceptoFormulario572
     */
    public function setDetalleConceptoFormulario572(\ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572 $detalleConceptoFormulario572 = null) {
        $this->detalleConceptoFormulario572 = $detalleConceptoFormulario572;
        //$detalleConceptoFormulario572->setConceptoFormulario572($this);
        return $this;
    }

    /**
     * Get detalleConceptoFormulario572
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572 
     */
    public function getDetalleConceptoFormulario572() {
        return $this->detalleConceptoFormulario572;
    }

    /**
     * Set detalleConceptoFormulario572Aplicado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572Aplicado $detalleConceptoFormulario572Aplicado
     * @return ConceptoFormulario572
     */
    public function setDetalleConceptoFormulario572Aplicado(\ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572Aplicado $detalleConceptoFormulario572Aplicado = null) {
        $this->detalleConceptoFormulario572Aplicado = $detalleConceptoFormulario572Aplicado;
        return $this;
    }

    /**
     * Get detalleConceptoFormulario572Aplicado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572Aplicado 
     */
    public function getDetalleConceptoFormulario572Aplicado() {
        return $this->detalleConceptoFormulario572Aplicado;
    }

    public function getEsBorrable() {
        return (($this->getDetalleConceptoFormulario572Aplicado() == null) ||
                (($this->getDetalleConceptoFormulario572Aplicado() != null) && (!$this->getDetalleConceptoFormulario572Aplicado()->getAplicado()) && ($this->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado() == 0))
                );
    }

    public function getEsEditableSoloMonto() {
        return (($this->getDetalleConceptoFormulario572Aplicado() == null) || (($this->getDetalleConceptoFormulario572Aplicado() != null) && (!$this->getDetalleConceptoFormulario572Aplicado()->getAplicado()) && ($this->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado() != 0)));
    }

    public function getEsEditable() {
        return (($this->getDetalleConceptoFormulario572Aplicado() == null) || (($this->getDetalleConceptoFormulario572Aplicado() != null) && (!$this->getDetalleConceptoFormulario572Aplicado()->getAplicado()) && ($this->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado() == 0)));
    }

}
