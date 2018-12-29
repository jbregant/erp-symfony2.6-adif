<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AspectoEvaluacion 
 * 
 * Indica el Aspecto de Evaluacion. 
 * 
 * Por ejemplo:
 *      Funcionalidad.
 *      Usabilidad.
 *      Eficiencia.
 *      Capacitación de mantenimiento.
 *      Portabilidad.
 *      Calidad de uso.
 * 
 *
 * @author Manuel Becerra
 * created 12/07/2014
 * 
 * @ORM\Table(name="aspecto_evaluacion")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\AspectoEvaluacionRepository")
 * @UniqueEntity(
 *      fields = {"denominacionAspectoEvaluacion", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class AspectoEvaluacion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionAspectoEvaluacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionAspectoEvaluacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor_ideal", type="integer", nullable=false)
     */
    protected $valorIdeal;

    /**
     * @ORM\OneToMany(targetEntity="EvaluacionAspectoProveedor", mappedBy="aspectoEvaluacion")
     */
    protected $evaluaciones;

    /**
     * Constructor
     */
    public function __construct() {
        $this->valorIdeal = 10;
        $this->evaluaciones = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionAspectoEvaluacion;
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
     * Set denominacionAspectoEvaluacion
     *
     * @param string $denominacionAspectoEvaluacion
     * @return AspectoEvaluacion
     */
    public function setDenominacionAspectoEvaluacion($denominacionAspectoEvaluacion) {
        $this->denominacionAspectoEvaluacion = $denominacionAspectoEvaluacion;

        return $this;
    }

    /**
     * Get denominacionAspectoEvaluacion
     *
     * @return string 
     */
    public function getDenominacionAspectoEvaluacion() {
        return $this->denominacionAspectoEvaluacion;
    }

    /**
     * Set descripcionAspectoEvaluacion
     *
     * @param string $descripcionAspectoEvaluacion
     * @return AspectoEvaluacion
     */
    public function setDescripcionAspectoEvaluacion($descripcionAspectoEvaluacion) {
        $this->descripcionAspectoEvaluacion = $descripcionAspectoEvaluacion;

        return $this;
    }

    /**
     * Get descripcionAspectoEvaluacion
     *
     * @return string 
     */
    public function getDescripcionAspectoEvaluacion() {
        return $this->descripcionAspectoEvaluacion;
    }

    /**
     * Set valorIdeal
     *
     * @param integer $valorIdeal
     * @return AspectoEvaluacion
     */
    public function setValorIdeal($valorIdeal) {
        $this->valorIdeal = $valorIdeal;

        return $this;
    }

    /**
     * Get valorIdeal
     *
     * @return integer 
     */
    public function getValorIdeal() {
        return $this->valorIdeal;
    }

    /**
     * Add evaluaciones
     *
     * @param \ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluaciones
     * @return AspectoEvaluacion
     */
    public function addEvaluacione(\ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluaciones) {
        $this->evaluaciones[] = $evaluaciones;

        return $this;
    }

    /**
     * Remove evaluaciones
     *
     * @param \ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluaciones
     */
    public function removeEvaluacione(\ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor $evaluaciones) {
        $this->evaluaciones->removeElement($evaluaciones);
    }

    /**
     * Get evaluaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvaluaciones() {
        return $this->evaluaciones;
    }

}
