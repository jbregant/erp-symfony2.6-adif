<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoPedidoInterno 
 * 
 * Indica el estado del Pedido Interno. 
 * 
 * Por ejemplo:
 *      Creado.
 *      Borrador.
 *      Enviado al Area Correspondiente.
 *      [...]
 * 
 *
 * @author Carlos Sabena
 * created 10/09/2014
 * 
 * @ORM\Table(name="estado_pedido_interno")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstadoPedidoInterno", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoPedidoInterno extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstadoPedidoInterno;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoPedidoInterno;

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
     * @ORM\OneToMany(targetEntity="PedidoInterno", mappedBy="estadoPedidoInterno")
     */
    protected $pedidosInternos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->pedidosInternos = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstadoPedidoInterno;
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
     * Set denominacionEstadoPedidoInterno
     *
     * @param string $denominacionEstadoPedidoInterno
     * @return EstadoPedidoInterno
     */
    public function setDenominacionEstadoPedidoInterno($denominacionEstadoPedidoInterno) {
        $this->denominacionEstadoPedidoInterno = $denominacionEstadoPedidoInterno;

        return $this;
    }

    /**
     * Get denominacionEstadoPedidoInterno
     *
     * @return string 
     */
    public function getDenominacionEstadoPedidoInterno() {
        return $this->denominacionEstadoPedidoInterno;
    }

    /**
     * Set descripcionEstadoPedidoInterno
     *
     * @param string $descripcionEstadoPedidoInterno
     * @return EstadoPedidoInterno
     */
    public function setDescripcionEstadoPedidoInterno($descripcionEstadoPedidoInterno) {
        $this->descripcionEstadoPedidoInterno = $descripcionEstadoPedidoInterno;

        return $this;
    }

    /**
     * Get descripcionEstadoPedidoInterno
     *
     * @return string 
     */
    public function getDescripcionEstadoPedidoInterno() {
        return $this->descripcionEstadoPedidoInterno;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoPedidoInterno
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
     * @return EstadoPedidoInterno
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
     * Add pedidosInternos
     *
     * @param \ADIF\ComprasBundle\Entity\PedidoInterno $pedidosInternos
     * @return EstadoPedidoInterno
     */
    public function addPedidosInterno(\ADIF\ComprasBundle\Entity\PedidoInterno $pedidosInternos) {
        $this->pedidosInternos[] = $pedidosInternos;

        return $this;
    }

    /**
     * Remove pedidosInternos
     *
     * @param \ADIF\ComprasBundle\Entity\PedidoInterno $pedidosInternos
     */
    public function removePedidosInterno(\ADIF\ComprasBundle\Entity\PedidoInterno $pedidosInternos) {
        $this->pedidosInternos->removeElement($pedidosInternos);
    }

    /**
     * Get pedidosInternos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPedidosInternos() {
        return $this->pedidosInternos;
    }

}
