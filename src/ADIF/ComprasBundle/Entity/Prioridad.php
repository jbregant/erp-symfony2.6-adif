<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prioridad
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="prioridad")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionPrioridad", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class Prioridad extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación de la prioridad no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionPrioridad;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_dias", type="integer", nullable=false)
     */
    protected $cantidadDias;

    /**
     * @ORM\OneToMany(targetEntity="RenglonSolicitudCompra", mappedBy="prioridad")
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
        return $this->denominacionPrioridad;
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
     * Set denominacionPrioridad
     *
     * @param string $denominacionPrioridad
     * @return Prioridad
     */
    public function setDenominacionPrioridad($denominacionPrioridad) {
        $this->denominacionPrioridad = $denominacionPrioridad;

        return $this;
    }

    /**
     * Get denominacionPrioridad
     *
     * @return string 
     */
    public function getDenominacionPrioridad() {
        return $this->denominacionPrioridad;
    }

    /**
     * Set cantidadDias
     *
     * @param integer $cantidadDias
     * @return Prioridad
     */
    public function setCantidadDias($cantidadDias) {
        $this->cantidadDias = $cantidadDias;

        return $this;
    }

    /**
     * Get cantidadDias
     *
     * @return integer 
     */
    public function getCantidadDias() {
        return $this->cantidadDias;
    }

    /**
     * Add renglonesSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra
     * @return Prioridad
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
