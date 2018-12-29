<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioDotacionPersonal;

/**
 * ConceptoPresupuestarioDotacionPersonal
 * 
 * @ORM\Table(name="concepto_presupuestario_dotacion_personal")
 * @ORM\Entity
 */
class ConceptoPresupuestarioDotacionPersonal extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="categorias", type="text", nullable=true)
     */
    private $categorias;

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
     * @return ConceptoPresupuestarioDotacionPersonal
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
     * @return ConceptoPresupuestarioDotacionPersonal
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
     * Set categorias
     *
     * @param string $categorias
     * @return ConceptoPresupuestarioDotacionPersonal
     */
    public function setCategorias($categorias) {
        $this->categorias = $categorias;

        return $this;
    }

    /**
     * Get categorias
     *
     * @return string 
     */
    public function getCategorias() {
        return $this->categorias;
    }

}
