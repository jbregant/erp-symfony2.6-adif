<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of TipoImpuesto
 *
 * @author Manuel Becerra
 * created 06/11/2014
 *
 * @ORM\Table(name="tipo_impuesto")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"},
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class TipoImpuesto extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToMany(targetEntity="TipoResponsable", inversedBy="tiposImpuesto")
     * @ORM\JoinTable(name="tipo_impuesto_tipo_responsable",
     *      joinColumns={@ORM\JoinColumn(name="id_tipo_impuesto", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tipo_responsable", referencedColumnName="id")}
     *      )
     */
    protected $tiposResponsable;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo_siap", type="integer", nullable=true)
     */
    private $codigoSiap;

    /**
     * Constructor
     */
    public function __construct() {
        $this->tiposResponsable = new ArrayCollection();
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
     * Campo a mostrar
     *
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoImpuesto
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
     * @return TipoImpuesto
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
     * Add tiposResponsable
     *
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tiposResponsable
     * @return TipoImpuesto
     */
    public function addTiposResponsable(\ADIF\ContableBundle\Entity\TipoResponsable $tiposResponsable) {
        $this->tiposResponsable[] = $tiposResponsable;

        return $this;
    }

    /**
     * Remove tiposResponsable
     *
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tiposResponsable
     */
    public function removeTiposResponsable(\ADIF\ContableBundle\Entity\TipoResponsable $tiposResponsable) {
        $this->tiposResponsable->removeElement($tiposResponsable);
    }

    /**
     * Get tiposResponsable
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTiposResponsable() {
        return $this->tiposResponsable;
    }

    /**
     * Set codigoSiap
     *
     * @param string $codigoSiap
     * @return RegimenRetencion
     */
    public function setCodigoSiap($codigoSiap) {
        $this->codigoSiap = $codigoSiap;

        return $this;
    }

    /**
     * Get codigoSiap
     *
     * @return string
     */
    public function getCodigoSiap() {
        return $this->codigoSiap;
    }

}
