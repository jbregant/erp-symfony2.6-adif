<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluacionProveedor 
 *
 * @author Carlos Sabena
 * created 12/07/2014
 * 
 * @ORM\Table(name="evaluacion_proveedor")
 * @ORM\Entity
 * @UniqueEntity("proveedor", message="El proveedor ya tiene evaluación.")
 */
class EvaluacionProveedor extends BaseAuditoria implements BaseAuditable {

    /**
     * CALIFICACION_MALA
     */
    const CALIFICACION_MALA = 'calificacion-mala';

    /**
     * CALIFICACION_REGULAR
     */
    const CALIFICACION_REGULAR = 'calificacion-regular';

    /**
     * CALIFICACION_BUENA
     */
    const CALIFICACION_BUENA = 'calificacion-buena';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\OneToOne(targetEntity="Proveedor", inversedBy="evaluacionProveedor")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     * 
     */
    protected $proveedor;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_evaluacion", type="date", nullable=false)
     */
    protected $fechaEvaluacion;

    /**
     * @ORM\OneToMany(targetEntity="EvaluacionAspectoProveedor", mappedBy="evaluacionProveedor", cascade={"persist", "remove"})
     */
    protected $evaluacionesAspectos;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaEvaluacion = new \DateTime();
        $this->evaluacionesAspectos = new ArrayCollection();
        $this->calificacionFinal = 0;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->calificacionFinal;
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
     * Set fechaEvaluacion
     *
     * @param \DateTime $fechaEvaluacion
     * @return EvaluacionProveedor
     */
    public function setFechaEvaluacion($fechaEvaluacion) {
        $this->fechaEvaluacion = $fechaEvaluacion;

        return $this;
    }

    /**
     * Get fechaEvaluacion
     *
     * @return \DateTime 
     */
    public function getFechaEvaluacion() {
        return $this->fechaEvaluacion;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return EvaluacionProveedor
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor = null) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Add evaluacionesAspectos
     *
     * @param \ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluacionesAspectos
     * @return EvaluacionProveedor
     */
    public function addEvaluacionesAspecto(\ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluacionesAspectos) {
        $this->evaluacionesAspectos[] = $evaluacionesAspectos;

        return $this;
    }

    /**
     * Remove evaluacionesAspectos
     *
     * @param \ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluacionesAspectos
     */
    public function removeEvaluacionesAspecto(\ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluacionesAspectos) {
        $this->evaluacionesAspectos->removeElement($evaluacionesAspectos);
    }

    /**
     * Get evaluacionesAspectos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvaluacionesAspectos() {
        return $this->evaluacionesAspectos;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return EvaluacionProveedor
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
     * Get calificacionFinal
     *
     * @return float 
     */
    public function getCalificacionFinal() {

        $calificacionFinal = 0;

        if (count($this->getEvaluacionesAspectos()) > 0) {
            foreach ($this->getEvaluacionesAspectos() as $evaluacionAspecto) {
                $calificacionFinal += ($evaluacionAspecto->getValorAlcanzado() * 100 /
                        $evaluacionAspecto->getAspectoEvaluacion()->getValorIdeal()) / 10;
            }

            $calificacionFinal = $calificacionFinal / count($this->getEvaluacionesAspectos());
        }

        return number_format($calificacionFinal, 1);
    }

    /**
     * Get claseCalificacionFinal
     *
     * @return string
     */
    public function getClaseCalificacionFinal() {

        $calificacionFinal = $this->getCalificacionFinal();

        if ($calificacionFinal < 4) {
            $claseCalificacionFinal = self::CALIFICACION_MALA;
        } //.
        else if ($calificacionFinal >= 4 && $calificacionFinal < 7) {

            $claseCalificacionFinal = self::CALIFICACION_REGULAR;
        } //. 
        else {
            $claseCalificacionFinal = self::CALIFICACION_BUENA;
        }

        return $claseCalificacionFinal;
    }

}
