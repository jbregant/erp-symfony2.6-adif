<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Rubro 
 *
 * @author Manuel Becerra
 * created 11/07/2014
 * 
 * @ORM\Table(name="rubro")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionRubro", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class Rubro extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionRubro;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionRubro;

    /**
     * @ORM\Column(name="id_area", type="integer", nullable=true)
     */
    protected $idArea;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Area
     */
    protected $area;

    /**
     * @ORM\OneToMany(targetEntity="BienEconomico", mappedBy="rubro")
     */
    protected $bienesEconomicos;

    /**
     * @ORM\ManyToMany(targetEntity="Proveedor", mappedBy="rubros")
     */
    protected $proveedores;

    /**
     * @ORM\OneToMany(targetEntity="RenglonSolicitudCompra", mappedBy="rubro")
     */
    protected $renglonesSolicitudCompra;

    /**
     * Constructor
     */
    public function __construct() {
        $this->bienesEconomicos = new ArrayCollection();
        $this->proveedores = new ArrayCollection();
        $this->renglonesSolicitudCompra = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        //return $this->denominacionRubro;
        return (isset($this) && $this->denominacionRubro !== '')
        ? (string)$this->denominacionRubro
        : '';
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
     * Set denominacionRubro
     *
     * @param string $denominacionRubro
     * @return Rubro
     */
    public function setDenominacionRubro($denominacionRubro) {
        $this->denominacionRubro = $denominacionRubro;

        return $this;
    }

    /**
     * Get denominacionRubro
     *
     * @return string 
     */
    public function getDenominacionRubro() {
        return $this->denominacionRubro;
    }

    /**
     * Set descripcionRubro
     *
     * @param string $descripcionRubro
     * @return Rubro
     */
    public function setDescripcionRubro($descripcionRubro) {
        $this->descripcionRubro = $descripcionRubro;

        return $this;
    }

    /**
     * Get descripcionRubro
     *
     * @return string 
     */
    public function getDescripcionRubro() {
        return $this->descripcionRubro;
    }

    /**
     * 
     * @return type
     */
    public function getIdArea() {
        return $this->idArea;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Area $area
     */
    public function setArea($area) {

        if (null != $area) {
            $this->idArea = $area->getId();
        } //.
        else {
            $this->idArea = null;
        }

        $this->area = $area;
    }

    /**
     * 
     * @return type
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * Add bienesEconomicos
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienesEconomicos
     * @return Rubro
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
     * Add renglonesSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonesSolicitudCompra
     * @return Rubro
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
