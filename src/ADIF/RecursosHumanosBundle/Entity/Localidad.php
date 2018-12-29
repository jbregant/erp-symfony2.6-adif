<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Localidad
 *
 * @ORM\Table(name="localidad", indexes={@ORM\Index(name="provincia", columns={"id_provincia"})})
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"nombre", "provincia", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La localidad ya exite en la provincia."
 * )
 */
class Localidad extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Provincia
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_provincia", referencedColumnName="id")
     * })
     */
    private $provincia;

    /**
     * To string
     * 
     * @return string
     */
    public function __toString() {
        return $this->nombre;
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
     * Set nombre
     *
     * @param string $nombre
     * @return Localidad
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }
    
    /**
     * Get nombre con provincia
     *
     * @return string 
     */
    public function getNombreConProvincia() {
        return $this->nombre . ' - ' . $this->provincia->getNombre();
    }

    /**
     * Set provincia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Provincia $provincia
     * @return Localidad
     */
    public function setProvincia(\ADIF\RecursosHumanosBundle\Entity\Provincia $provincia = null) {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Provincia 
     */
    public function getProvincia() {
        return $this->provincia;
    }

}
