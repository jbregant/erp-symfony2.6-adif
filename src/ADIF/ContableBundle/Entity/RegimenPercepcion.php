<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of RegimenPercepcion
 *
 * @author Darío Rapetti
 * created 22/04/2015
 * 
 * @ORM\Table(name="regimen_percepcion")
 * @ORM\Entity
 */
class RegimenPercepcion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConceptoPercepcion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_percepcion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $conceptoPercepcion;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="El código no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigo;

    /**
     * @var float
     * 
     * @ORM\Column(name="alicuota", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La alícuota debe ser de tipo numérico.")
     */
    protected $alicuota;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return RegimenPercepcion
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return RegimenPercepcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return RegimenPercepcion
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set alicuota
     *
     * @param float $alicuota
     * @return RegimenPercepcion
     */
    public function setAlicuota($alicuota) {
        $this->alicuota = $alicuota;

        return $this;
    }

    /**
     * Get alicuota
     *
     * @return float 
     */
    public function getAlicuota() {
        return $this->alicuota;
    }

    /**
     * Set conceptoPercepcion
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPercepcion $conceptoPercepcion
     * @return RegimenPercepcion
     */
    public function setConceptoPercepcion(\ADIF\ContableBundle\Entity\ConceptoPercepcion $conceptoPercepcion) {
        $this->conceptoPercepcion = $conceptoPercepcion;

        return $this;
    }

    /**
     * Get conceptoPercepcion
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoPercepcion 
     */
    public function getConceptoPercepcion() {
        return $this->conceptoPercepcion;
    }

}
