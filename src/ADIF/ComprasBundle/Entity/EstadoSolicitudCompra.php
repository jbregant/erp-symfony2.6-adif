<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoSolicitudCompra 
 * 
 * Indica el estado de la Solicitud de Compra. 
 * 
 * Por ejemplo:
 *      Creada.
 *      Borrador.
 *      Enviada a Aprobacion.
 *      [...]
 * 
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="estado_solicitud_compra")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstadoSolicitudCompra", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoSolicitudCompra extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstadoSolicitudCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoSolicitudCompra;

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
    protected $esEditable;

    /**
     * @ORM\OneToMany(targetEntity="SolicitudCompra", mappedBy="estadoSolicitudCompra")
     */
    protected $solicitudes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->solicitudes = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstadoSolicitudCompra;
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
     * Set denominacionEstadoSolicitudCompra
     *
     * @param string $denominacionEstadoSolicitudCompra
     * @return EstadoSolicitudCompra
     */
    public function setDenominacionEstadoSolicitudCompra($denominacionEstadoSolicitudCompra) {
        $this->denominacionEstadoSolicitudCompra = $denominacionEstadoSolicitudCompra;

        return $this;
    }

    /**
     * Get denominacionEstadoSolicitudCompra
     *
     * @return string 
     */
    public function getDenominacionEstadoSolicitudCompra() {
        return $this->denominacionEstadoSolicitudCompra;
    }

    /**
     * Set descripcionEstadoSolicitudCompra
     *
     * @param string $descripcionEstadoSolicitudCompra
     * @return EstadoSolicitudCompra
     */
    public function setDescripcionEstadoSolicitudCompra($descripcionEstadoSolicitudCompra) {
        $this->descripcionEstadoSolicitudCompra = $descripcionEstadoSolicitudCompra;

        return $this;
    }

    /**
     * Get descripcionEstadoSolicitudCompra
     *
     * @return string 
     */
    public function getDescripcionEstadoSolicitudCompra() {
        return $this->descripcionEstadoSolicitudCompra;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoSolicitudCompra
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
     * @return EstadoSolicitudCompra
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
     * Add solicitudes
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes
     * @return EstadoSolicitudCompra
     */
    public function addSolicitud(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes) {
        $this->solicitudes[] = $solicitudes;

        return $this;
    }

    /**
     * Remove solicitudes
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes
     */
    public function removeSolicitud(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes) {
        $this->solicitudes->removeElement($solicitudes);
    }

    /**
     * Get solicitudes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSolicitudes() {
        return $this->solicitudes;
    }

}
