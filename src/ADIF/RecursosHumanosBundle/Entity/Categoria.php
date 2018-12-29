<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria", indexes={@ORM\Index(name="convenio", columns={"id_convenio"})})
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\CategoriaRepository")
 */
class Categoria extends BaseAuditoria {

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
     * @ORM\OneToMany(targetEntity="Subcategoria", mappedBy="idCategoria")
     * */
    private $subcategorias;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Convenio
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Convenio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_convenio", referencedColumnName="id")
     * })
     */
    private $idConvenio;

    /**
     * Constructor
     */
    public function __construct() {
        $this->subcategorias = new ArrayCollection();
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
     * @return Categoria
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
     * Set idConvenio
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Convenio $idConvenio
     * @return Categoria
     */
    public function setIdConvenio(\ADIF\RecursosHumanosBundle\Entity\Convenio $idConvenio = null) {
        $this->idConvenio = $idConvenio;

        return $this;
    }

    /**
     * Get idConvenio
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Convenio 
     */
    public function getIdConvenio() {
        return $this->idConvenio;
    }

    /**
     * Get Convenio
     * 
     * @return \ADIF\RecursosHumanosBundle\Entity\Convenio 
     */
    public function getConvenio() {
        return $this->getIdConvenio();
    }

    /**
     * Add subcategorias
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Subcategoria $subcategorias
     * @return Categoria
     */
    public function addSubcategoria(\ADIF\RecursosHumanosBundle\Entity\Subcategoria $subcategorias) {
        $this->subcategorias[] = $subcategorias;

        return $this;
    }

    /**
     * Remove subcategorias
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Subcategoria $subcategorias
     */
    public function removeSubcategoria(\ADIF\RecursosHumanosBundle\Entity\Subcategoria $subcategorias) {
        $this->subcategorias->removeElement($subcategorias);
    }

    /**
     * Get subcategorias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubcategorias() {
        return $this->subcategorias;
    }

}
