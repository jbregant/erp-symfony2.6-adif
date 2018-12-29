<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoRequerimiento 
 * 
 * Indica el estado del Requerimiento. 
 * 
 * Por ejemplo:
 *      Borrador.
 *      Cerrada.
 *      Resuelta.
 * 
 *
 * @author Carlos Sabena
 * created 16/07/2014
 * 
 * @ORM\Table(name="estado_requerimiento")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstadoRequerimiento", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoRequerimiento extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEstadoRequerimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoRequerimiento;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoImportancia
     *
     * @ORM\ManyToOne(targetEntity="TipoImportancia")
     * @ORM\JoinColumn(name="id_tipo_importancia", referencedColumnName="id")
     * 
     */
    protected $tipoImportancia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_editable", type="boolean", nullable=false)
     */
    private $esEditable;

    /**
     * @ORM\OneToMany(targetEntity="Requerimiento", mappedBy="estadoRequerimiento")
     */
    protected $requerimientos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->requerimientos = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstadoRequerimiento;
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
     * Set denominacionEstadoRequerimiento
     *
     * @param string $denominacionEstadoRequerimiento
     * @return EstadoRequerimiento
     */
    public function setDenominacionEstadoRequerimiento($denominacionEstadoRequerimiento) {
        $this->denominacionEstadoRequerimiento = $denominacionEstadoRequerimiento;

        return $this;
    }

    /**
     * Get denominacionEstadoRequerimiento
     *
     * @return string 
     */
    public function getDenominacionEstadoRequerimiento() {
        return $this->denominacionEstadoRequerimiento;
    }

    /**
     * Set descripcionEstadoRequerimiento
     *
     * @param string $descripcionEstadoRequerimiento
     * @return EstadoRequerimiento
     */
    public function setDescripcionEstadoRequerimiento($descripcionEstadoRequerimiento) {
        $this->descripcionEstadoRequerimiento = $descripcionEstadoRequerimiento;

        return $this;
    }

    /**
     * Get descripcionEstadoRequerimiento
     *
     * @return string 
     */
    public function getDescripcionEstadoRequerimiento() {
        return $this->descripcionEstadoRequerimiento;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoRequerimiento
     */
    public function setEsEditable($esEditable) {
        $this->esEditable = $esEditable;

        return $this;
    }

    /**
     * Get esEditable
     *
     * @return boolean 
     */
    public function getEsEditable() {
        return $this->esEditable;
    }

    /**
     * Set tipoImportancia
     *
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     * @return EstadoRequerimiento
     */
    public function setTipoImportancia(\ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia = null) {
        $this->tipoImportancia = $tipoImportancia;

        return $this;
    }

    /**
     * Get tipoImportancia
     *
     * @return \ADIF\ComprasBundle\Entity\TipoImportancia 
     */
    public function getTipoImportancia() {
        return $this->tipoImportancia;
    }

    /**
     * Add requerimientos
     *
     * @param \ADIF\ComprasBundle\Entity\Requerimiento $requerimientos
     * @return EstadoRequerimiento
     */
    public function addRequerimiento(\ADIF\ComprasBundle\Entity\Requerimiento $requerimientos) {
        $this->requerimientos[] = $requerimientos;

        return $this;
    }

    /**
     * Remove requerimientos
     *
     * @param \ADIF\ComprasBundle\Entity\Requerimiento $requerimientos
     */
    public function removeRequerimiento(\ADIF\ComprasBundle\Entity\Requerimiento $requerimientos) {
        $this->requerimientos->removeElement($requerimientos);
    }

    /**
     * Get requerimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequerimientos() {
        return $this->requerimientos;
    }

}
