<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoSolicitudCompra 
 * 
 * Indica el Tipo de Solicitud de Compra. 
 * 
 * Por ejemplo:
 *      Compra Anual.
 *      Compra Particular.
 * 
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="tipo_solicitud_compra")
 * @ORM\Entity
 * @UniqueEntity("denominacionTipoSolicitudCompra", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoSolicitudCompra extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoSolicitudCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoSolicitudCompra;

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
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionTipoSolicitudCompra;
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
     * Set denominacionTipoSolicitudCompra
     *
     * @param string $denominacionTipoSolicitudCompra
     * @return TipoSolicitudCompra
     */
    public function setDenominacionTipoSolicitudCompra($denominacionTipoSolicitudCompra) {
        $this->denominacionTipoSolicitudCompra = $denominacionTipoSolicitudCompra;

        return $this;
    }

    /**
     * Get denominacionTipoSolicitudCompra
     *
     * @return string 
     */
    public function getDenominacionTipoSolicitudCompra() {
        return $this->denominacionTipoSolicitudCompra;
    }

    /**
     * Set descripcionTipoSolicitudCompra
     *
     * @param string $descripcionTipoSolicitudCompra
     * @return TipoSolicitudCompra
     */
    public function setDescripcionTipoSolicitudCompra($descripcionTipoSolicitudCompra) {
        $this->descripcionTipoSolicitudCompra = $descripcionTipoSolicitudCompra;

        return $this;
    }

    /**
     * Get descripcionTipoSolicitudCompra
     *
     * @return string 
     */
    public function getDescripcionTipoSolicitudCompra() {
        return $this->descripcionTipoSolicitudCompra;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return TipoSolicitudCompra
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
     * @return TipoSolicitudCompra
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
