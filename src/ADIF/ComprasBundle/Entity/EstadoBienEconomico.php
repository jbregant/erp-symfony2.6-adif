<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoBienEconomico 
 * 
 * Indica el estado de un Bien Económico. 
 * 
 * Por ejemplo:
 *      Activo.
 *      Inactivo.
 *      Pendiente.
 *      [...]
 * 
 *
 * @author Manuel Becerra
 * created 11/07/2014
 * 
 * @ORM\Table(name="estado_bien_economico")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionEstadoBienEconomico", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class EstadoBienEconomico extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEstadoBienEconomico;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoBienEconomico;

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
     * @ORM\OneToMany(targetEntity="BienEconomico", mappedBy="estadoBienEconomico")
     */
    protected $bienesEconomicos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->bienesEconomicos = new ArrayCollection();
        $this->esEditable = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstadoBienEconomico;
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
     * Set denominacionEstadoBienEconomico
     *
     * @param string $denominacionEstadoBienEconomico
     * @return EstadoBienEconomico
     */
    public function setDenominacionEstadoBienEconomico($denominacionEstadoBienEconomico) {
        $this->denominacionEstadoBienEconomico = $denominacionEstadoBienEconomico;

        return $this;
    }

    /**
     * Get denominacionEstadoBienEconomico
     *
     * @return string 
     */
    public function getDenominacionEstadoBienEconomico() {
        return $this->denominacionEstadoBienEconomico;
    }

    /**
     * Set descripcionEstadoBienEconomico
     *
     * @param string $descripcionEstadoBienEconomico
     * @return EstadoBienEconomico
     */
    public function setDescripcionEstadoBienEconomico($descripcionEstadoBienEconomico) {
        $this->descripcionEstadoBienEconomico = $descripcionEstadoBienEconomico;

        return $this;
    }

    /**
     * Get descripcionEstadoBienEconomico
     *
     * @return string 
     */
    public function getDescripcionEstadoBienEconomico() {
        return $this->descripcionEstadoBienEconomico;
    }

    /**
     * Add bienesEconomicos
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienesEconomicos
     * @return EstadoBienEconomico
     */
    public function addBienesEconomico(\ADIF\ComprasBundle\Entity\BienEconomico $bienesEconomicos) {
        $this->bienesEconomicos[] = $bienesEconomicos;

        return $this;
    }

    /**
     * Remove bienesEconomicos
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienesEconomicos
     */
    public function removeBienesEconomico(\ADIF\ComprasBundle\Entity\BienEconomico $bienesEconomicos) {
        $this->bienesEconomicos->removeElement($bienesEconomicos);
    }

    /**
     * Get bienesEconomicos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBienesEconomicos() {
        return $this->bienesEconomicos;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoBienEconomico
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
     * @return EstadoBienEconomico
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

}
