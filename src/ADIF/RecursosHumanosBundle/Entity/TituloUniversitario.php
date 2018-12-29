<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseAuditoria;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * TituloUniversitario
 *
 * @ORM\Table(name="titulo_universitario")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre","fechaBaja"}, ignoreNull=false, message="El título ya se encuentra en uso.")
 */
class TituloUniversitario extends BaseAuditoria {

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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="EstudioEmpleado", mappedBy="titulo")
     * */
    private $estudios;

    /**
     * Constructor
     */
    public function __construct() {
        $this->estudios = new ArrayCollection();
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNombre();
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
     * @return Banco
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
     * Add estudios
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado $estudios
     * @return TituloUniversitario
     */
    public function addEstudio(\ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado $estudios) {
        $this->estudios[] = $estudios;

        return $this;
    }

    /**
     * Remove estudios
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado $estudios
     */
    public function removeEstudio(\ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado $estudios) {
        $this->estudios->removeElement($estudios);
    }

    /**
     * Get estudios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEstudios() {
        return $this->estudios;
    }

}
