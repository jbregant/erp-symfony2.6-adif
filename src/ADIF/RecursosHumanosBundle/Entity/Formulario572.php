<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulario572
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_formulario_572")
 * @ORM\Entity
 * @UniqueEntity(fields={"empleado","anio"}, ignoreNull=false, message="Ya existe un formulario 572 para el anio indicado.")
 */
class Formulario572 {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="Empleado", inversedBy="formularios572")
     * @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * 
     */
    protected $empleado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_formulario", type="date", nullable=false)
     */
    private $fechaFormulario;

    /**
     *
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572
     * 
     * @ORM\OneToMany(targetEntity="ConceptoFormulario572", mappedBy="formulario572", cascade={"all"})
     */
    protected $conceptos;

    /**
     * @var integer
     * @ORM\Column(name="anio", type="integer", nullable=false)
     */
    protected $anio;

    /**
     * Constructor
     */
    public function __construct() {
        $this->conceptos = new ArrayCollection();
        $this->fechaFormulario = new DateTime();
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
     * Set fechaFormulario
     *
     * @param \DateTime $fechaFormulario
     * @return Formulario572
     */
    public function setFechaFormulario($fechaFormulario) {
        $this->fechaFormulario = $fechaFormulario;

        return $this;
    }

    /**
     * Get fechaFormulario
     *
     * @return \DateTime 
     */
    public function getFechaFormulario() {
        return $this->fechaFormulario;
    }

    /**
     * Set empleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return Formulario572
     */
    public function setEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleado) {
        $empleado->addFormularios572($this);

        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getEmpleado() {
        return $this->empleado;
    }

    /**
     * Add conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptos
     * @return Formulario572
     */
    public function addConcepto(\ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptos) {
        $this->conceptos[] = $conceptos;

        return $this;
    }

    /**
     * Remove conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptos
     */
    public function removeConcepto(\ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptos) {
        $this->conceptos->removeElement($conceptos);
    }

    /**
     * Get conceptos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConceptos() {
        return $this->conceptos;
    }

    /**
     * Get percepciones
     *
     * @return float
     */
    public function getPercepciones() {

        $total = 0;
        $conceptosPercepcion = $this->getConceptos()->filter(
                function($entry) {
            return in_array($entry->getConceptoGanancia()->getTipoConceptoGanancia()->getId(), array(TipoConceptoGanancia::__PERCEPCIONES));
        });
        foreach ($conceptosPercepcion as $concepto) {
            $total += $concepto->getMonto() * ($concepto->getMesHasta() - $concepto->getMesDesde() + 1);
        }

        return $total;
    }

    /**
     * Set anio
     *
     * @param string $anio
     * @return Formulario572
     */
    public function setAnio($anio) {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string 
     */
    public function getAnio() {
        return $this->anio;
    }

    /**
     * Get cargas familiares
     *
     */
    public function getCargasFamiliares() {
        return $conceptos = $this->getConceptos()->filter(
                function($entry) {
            return in_array($entry->getConceptoGanancia()->getCodigo572(), array(ConceptoGanancia::__CODIGO_HIJOS, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, ConceptoGanancia::__CODIGO_CONYUGE));
        });
    }

}
