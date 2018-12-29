<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoResponsable 
 * 
 * Indica los Tipos de Responsabilidad
 * 
 * Por ejemplo:
 *      Consumidor Final.
 *      Inscripto.
 *      Responsable Monotributo.
 *      Sujeto No Categorizado.
 * 
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="tipo_responsable")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\TipoResponsableRepository")
 * @UniqueEntity("codigoTipoResponsable", message="El código ingresado ya se encuentra en uso.")
 * @UniqueEntity("denominacionTipoResponsable", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoResponsable extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="codigo", type="string", length=3, unique=true, nullable=false)
     * @Assert\Length(
     *      max="3", 
     *      maxMessage="El código de la Condición de IVA no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoTipoResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoResponsable;

    /**
     * @ORM\ManyToMany(targetEntity="TipoImpuesto", mappedBy="tiposResponsable")
     * */
    protected $tiposImpuesto;

    /**
     * Constructor
     */
    public function __construct() {
        $this->tiposImpuesto = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionTipoResponsable;
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
     * Set codigoTipoResponsable
     *
     * @param string $codigoTipoResponsable
     * @return TipoResponsable
     */
    public function setCodigoTipoResponsable($codigoTipoResponsable) {
        $this->codigoTipoResponsable = $codigoTipoResponsable;

        return $this;
    }

    /**
     * Get codigoTipoResponsable
     *
     * @return string 
     */
    public function getCodigoTipoResponsable() {
        return $this->codigoTipoResponsable;
    }

    /**
     * Set denominacionTipoResponsable
     *
     * @param string $denominacionTipoResponsable
     * @return TipoResponsable
     */
    public function setDenominacionTipoResponsable($denominacionTipoResponsable) {
        $this->denominacionTipoResponsable = $denominacionTipoResponsable;

        return $this;
    }

    /**
     * Get denominacionTipoResponsable
     *
     * @return string 
     */
    public function getDenominacionTipoResponsable() {
        return $this->denominacionTipoResponsable;
    }

    /**
     * Set descripcionTipoResponsable
     *
     * @param string $descripcionTipoResponsable
     * @return TipoResponsable
     */
    public function setDescripcionTipoResponsable($descripcionTipoResponsable) {
        $this->descripcionTipoResponsable = $descripcionTipoResponsable;

        return $this;
    }

    /**
     * Get descripcionTipoResponsable
     *
     * @return string 
     */
    public function getDescripcionTipoResponsable() {
        return $this->descripcionTipoResponsable;
    }

    /**
     * Add tiposImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tiposImpuesto
     * @return TipoResponsable
     */
    public function addTiposImpuesto(\ADIF\ContableBundle\Entity\TipoImpuesto $tiposImpuesto) {
        $this->tiposImpuesto[] = $tiposImpuesto;

        return $this;
    }

    /**
     * Remove tiposImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tiposImpuesto
     */
    public function removeTiposImpuesto(\ADIF\ContableBundle\Entity\TipoImpuesto $tiposImpuesto) {
        $this->tiposImpuesto->removeElement($tiposImpuesto);
    }

    /**
     * Get tiposImpuesto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTiposImpuesto() {
        return $this->tiposImpuesto;
    }

}
