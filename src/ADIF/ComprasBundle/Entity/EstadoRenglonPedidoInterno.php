<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoRenglonPedidoInterno 
 * 
 * Indica el estado del Renglon del Pedido Interno. 
 * 
 * Por ejemplo:
 *      Creado.
 *      Borrador.
 *      Enviado.
 *      Pendiente.
 *      Rechazado.
 *      Resuelto por Stock
 *      [...]
 * 
 *
 * @author Carlos Sabena
 * created 10/09/2014
 * 
 * @ORM\Table(name="estado_renglon_pedido_interno")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstadoRenglonPedidoInterno", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoRenglonPedidoInterno extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstadoRenglonPedidoInterno;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoRenglonPedidoInterno;

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
        return $this->denominacionEstadoRenglonPedidoInterno;
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
     * Set denominacionEstadoRenglonPedidoInterno
     *
     * @param string $denominacionEstadoRenglonPedidoInterno
     * @return EstadoRenglonPedidoInterno
     */
    public function setDenominacionEstadoRenglonPedidoInterno($denominacionEstadoRenglonPedidoInterno) {
        $this->denominacionEstadoRenglonPedidoInterno = $denominacionEstadoRenglonPedidoInterno;

        return $this;
    }

    /**
     * Get denominacionEstadoRenglonPedidoInterno
     *
     * @return string 
     */
    public function getDenominacionEstadoRenglonPedidoInterno() {
        return $this->denominacionEstadoRenglonPedidoInterno;
    }

    /**
     * Set descripcionEstadoRenglonPedidoInterno
     *
     * @param string $descripcionEstadoRenglonPedidoInterno
     * @return EstadoRenglonPedidoInterno
     */
    public function setDescripcionEstadoRenglonPedidoInterno($descripcionEstadoRenglonPedidoInterno) {
        $this->descripcionEstadoRenglonPedidoInterno = $descripcionEstadoRenglonPedidoInterno;

        return $this;
    }

    /**
     * Get descripcionEstadoRenglonPedidoInterno
     *
     * @return string 
     */
    public function getDescripcionEstadoRenglonPedidoInterno() {
        return $this->descripcionEstadoRenglonPedidoInterno;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoRenglonPedidoInterno
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
     * @return EstadoRenglonPedidoInterno
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
