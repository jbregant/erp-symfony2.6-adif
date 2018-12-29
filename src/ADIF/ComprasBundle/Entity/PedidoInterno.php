<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoPedidoInterno;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PedidoInterno
 *
 * @author Carlos Sabena
 * created 10/09/2014
 * 
 * @ORM\Table(name="pedido_interno")
 * @ORM\Entity
 */
class PedidoInterno extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_pedido", type="date", nullable=false)
     */
    protected $fechaPedido;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * @ORM\Column(name="id_area", type="integer", nullable=true)
     */
    protected $idArea;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Area
     */
    protected $area;

    /**
    * @ORM\Column(name="id_centro_costo", type="integer", nullable=false)
    */
    protected $idCentroCosto;

    /**
     * @var ADIF\ContableBundle\Entity\CentroCosto
     */
    protected $centroCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=true)
     */
    protected $numeroReferencia;

    /**
     * @ORM\OneToMany(targetEntity="RenglonPedidoInterno", mappedBy="pedidoInterno", cascade={"persist", "remove"})
     */
    protected $renglonesPedidoInterno;

    /**
     * @var EstadoPedidoInterno
     *
     * @ORM\ManyToOne(targetEntity="EstadoPedidoInterno", inversedBy="pedidosInternos")
     * @ORM\JoinColumn(name="id_estado_pedido_interno", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoPedidoInterno;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\OneToOne(targetEntity="JustificacionPedidoInterno", mappedBy="pedidoInterno", cascade={"persist", "remove"})
     */
    protected $justificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaPedido = new \DateTime();
        $this->renglonesPedidoInterno = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->fechaPedido->format('d/m/Y');
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
     * Set fechaPedido
     *
     * @param \DateTime $fechaPedido
     * @return PedidoInterno
     */
    public function setFechaPedido($fechaPedido) {
        $this->fechaPedido = $fechaPedido;

        return $this;
    }

    /**
     * Get fechaPedido
     *
     * @return \DateTime 
     */
    public function getFechaPedido() {
        return $this->fechaPedido;
    }

    /**
     * 
     * @return type
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     */
    public function setUsuario($usuario) {

        if (null != $usuario) {
            $this->idUsuario = $usuario->getId();
        } //.
        else {
            $this->idUsuario = null;
        }

        $this->usuario = $usuario;
    }

    /**
     * 
     * @return type
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * 
     * @return type
     */
    public function getIdArea() {

        if ($this->idArea == null) {

            if ($this->getUsuario() != null) {
                return $this->getUsuario()->getIdArea();
            }
        }

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

        if ($this->idArea == null) {

            if ($this->getUsuario() != null) {
                return $this->getUsuario()->getArea();
            }
        }

        return $this->area;
    }

    /**
     * 
     * @return type
     */
    public function getIdCentroCosto() {
        return $this->idCentroCosto;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CentroCosto $centroCosto
     */
    public function setCentroCosto($centroCosto) {

        if (null != $centroCosto) {
            $this->idCentroCosto = $centroCosto->getId();
        } //.
        else {
            $this->idCentroCosto = null;
        }

        $this->centroCosto = $centroCosto;
    }

    /**
     * 
     * @return type
     */
    public function getCentroCosto() {
        return $this->centroCosto;
    }

    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return PedidoInterno
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return PedidoInterno
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
     * Set observacion
     *
     * @param string $observacion
     * @return PedidoInterno
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set estadoPedidoInterno
     *
     * @param EstadoPedidoInterno $estadoPedidoInterno
     * @return PedidoInterno
     */
    public function setEstadoPedidoInterno(EstadoPedidoInterno $estadoPedidoInterno) {
        $this->estadoPedidoInterno = $estadoPedidoInterno;

        return $this;
    }

    /**
     * Get estadoPedidoInterno
     *
     * @return EstadoPedidoInterno 
     */
    public function getEstadoPedidoInterno() {
        return $this->estadoPedidoInterno;
    }

    /**
     * Set justificacion
     *
     * @param JustificacionPedidoInterno $justificacion
     * @return PedidoInterno
     */
    public function setJustificacion(JustificacionPedidoInterno $justificacion = null) {
        $this->justificacion = $justificacion;

        return $this;
    }

    /**
     * Get justificacion
     *
     * @return JustificacionPedidoInterno 
     */
    public function getJustificacion() {
        return $this->justificacion;
    }

    /**
     * Add renglonesPedidoInterno
     *
     * @param RenglonPedidoInterno $renglonesPedidoInterno
     * @return PedidoInterno
     */
    public function addRenglonesPedidoInterno(RenglonPedidoInterno $renglonesPedidoInterno) {
        $this->renglonesPedidoInterno[] = $renglonesPedidoInterno;

        return $this;
    }

    /**
     * Remove renglonesPedidoInterno
     *
     * @param RenglonPedidoInterno $renglonesPedidoInterno
     */
    public function removeRenglonesPedidoInterno(RenglonPedidoInterno $renglonesPedidoInterno) {
        $this->renglonesPedidoInterno->removeElement($renglonesPedidoInterno);
    }

    /**
     * Get renglonesPedidoInterno
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesPedidoInterno() {
        return $this->renglonesPedidoInterno;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEditable() {

        $denominacionEstado = $this->getEstadoPedidoInterno()->getDenominacionEstadoPedidoInterno();

        return $denominacionEstado == ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_BORRADOR;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAnulable() {

        $estaEnSolicitudCompra = false;

        foreach ($this->renglonesPedidoInterno as $renglonPedidoInterno) {

            /* @var $renglonPedidoInterno RenglonPedidoInterno */
            if ($renglonPedidoInterno->getRenglonSolicitudCompra()[0] != null) {

                $estaEnSolicitudCompra = true;

                break;
            }
        }

        // Si NO esta dentro de una Solicitud y NO esta ANULADO
        return !$estaEnSolicitudCompra && !$this->getEstaAnulado();
    }

    /**
     * 
     * @return boolean
     */
    public function getEstaAnulado() {

        $denominacionEstado = $this->getEstadoPedidoInterno()->getDenominacionEstadoPedidoInterno();

        return $denominacionEstado == ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_ANULADO;
    }

}
