<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoRenglonSolicitudCompra 
 * 
 * Indica el estado del Renglon de la Solicitud de Compra. 
 * 
 * Por ejemplo:
 *      Creada.
 *      Borrador.
 *      Enviada a Aprobacion.
 *      [...]
 * 
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="estado_renglon_solicitud_compra")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstadoRenglonSolicitudCompra", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoRenglonSolicitudCompra extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstadoRenglonSolicitudCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoRenglonSolicitudCompra;

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
        return $this->denominacionEstadoRenglonSolicitudCompra;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacionEstadoRenglonSolicitudCompra
     *
     * @param string $denominacionEstadoRenglonSolicitudCompra
     * @return EstadoRenglonSolicitudCompra
     */
    public function setDenominacionEstadoRenglonSolicitudCompra($denominacionEstadoRenglonSolicitudCompra)
    {
        $this->denominacionEstadoRenglonSolicitudCompra = $denominacionEstadoRenglonSolicitudCompra;

        return $this;
    }

    /**
     * Get denominacionEstadoRenglonSolicitudCompra
     *
     * @return string 
     */
    public function getDenominacionEstadoRenglonSolicitudCompra()
    {
        return $this->denominacionEstadoRenglonSolicitudCompra;
    }

    /**
     * Set descripcionEstadoRenglonSolicitudCompra
     *
     * @param string $descripcionEstadoRenglonSolicitudCompra
     * @return EstadoRenglonSolicitudCompra
     */
    public function setDescripcionEstadoRenglonSolicitudCompra($descripcionEstadoRenglonSolicitudCompra)
    {
        $this->descripcionEstadoRenglonSolicitudCompra = $descripcionEstadoRenglonSolicitudCompra;

        return $this;
    }

    /**
     * Get descripcionEstadoRenglonSolicitudCompra
     *
     * @return string 
     */
    public function getDescripcionEstadoRenglonSolicitudCompra()
    {
        return $this->descripcionEstadoRenglonSolicitudCompra;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoRenglonSolicitudCompra
     */
    public function setEsEditable($esEditable)
    {
        $this->esEditable = $esEditable;

        return $this;
    }

    /**
     * Get esEditable
     *
     * @return boolean 
     */
    public function getEsEditable()
    {
        return $this->esEditable;
    }

    /**
     * Set tipoImportancia
     *
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     * @return EstadoRenglonSolicitudCompra
     */
    public function setTipoImportancia(\ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia = null)
    {
        $this->tipoImportancia = $tipoImportancia;

        return $this;
    }

    /**
     * Get tipoImportancia
     *
     * @return \ADIF\ComprasBundle\Entity\TipoImportancia 
     */
    public function getTipoImportancia()
    {
        return $this->tipoImportancia;
    }
}
