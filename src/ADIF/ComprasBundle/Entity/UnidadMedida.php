<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unidad
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="unidad_medida")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionUnidadMedida", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class UnidadMedida extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación de la unidad no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionUnidadMedida;

    /**
     * @ORM\OneToMany(targetEntity="RenglonSolicitudCompra", mappedBy="unidadMedida")
     */
    protected $renglonesSolicitudCompra;

    /**
     * Constructor
     */
    public function __construct() {
        $this->renglonesSolicitudCompra = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionUnidadMedida;
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
     * Set denominacionUnidadMedida
     *
     * @param string $denominacionUnidadMedida
     * @return UnidadMedida
     */
    public function setDenominacionUnidadMedida($denominacionUnidadMedida) {
        $this->denominacionUnidadMedida = $denominacionUnidadMedida;

        return $this;
    }

    /**
     * Get denominacionUnidadMedida
     *
     * @return string 
     */
    public function getDenominacionUnidadMedida() {
        return $this->denominacionUnidadMedida;
    }

    /**
     * Add renglonesSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra
     * @return UnidadMedida
     */
    public function addRenglonesSolicitudCompra(\ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra) {
        $this->renglonesSolicitudCompra[] = $renglonesSolicitudCompra;

        return $this;
    }

    /**
     * Remove renglonesSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra
     */
    public function removeRenglonesSolicitudCompra(\ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra) {
        $this->renglonesSolicitudCompra->removeElement($renglonesSolicitudCompra);
    }

    /**
     * Get renglonesSolicitudCompra
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesSolicitudCompra() {
        return $this->renglonesSolicitudCompra;
    }

}
