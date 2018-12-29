<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonSolicitudCompra
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="renglon_solicitud_compra")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RenglonSolicitudCompraRepository")
 */
class RenglonSolicitudCompra extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\SolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="SolicitudCompra", inversedBy="renglonesSolicitudCompra")
     * @ORM\JoinColumn(name="id_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $solicitudCompra;

    /**
     * @var \ADIF\ComprasBundle\Entity\RenglonPedidoInterno
     *
     * @ORM\ManyToOne(targetEntity="RenglonPedidoInterno", inversedBy="renglonSolicitudCompra")
     * @ORM\JoinColumn(name="id_renglon_pedido_interno", referencedColumnName="id", nullable=true)
     * 
     */
    protected $renglonPedidoInterno;

    /**
     * @var \ADIF\ComprasBundle\Entity\BienEconomico
     *
     * @ORM\ManyToOne(targetEntity="BienEconomico")
     * @ORM\JoinColumn(name="id_bien_economico", referencedColumnName="id", nullable=false)
     * 
     */
    protected $bienEconomico;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;

    /**
     * @var float
     * 
     * @ORM\Column(name="justiprecio_unitario", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El justiprecio unitario debe ser de tipo numérico.")
     */
    protected $justiprecioUnitario;

    /**
     * @var \ADIF\ComprasBundle\Entity\Prioridad
     *
     * @ORM\ManyToOne(targetEntity="Prioridad", inversedBy="renglonesSolicitudCompra")
     * @ORM\JoinColumn(name="id_prioridad", referencedColumnName="id", nullable=false)
     * 
     */
    protected $prioridad;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoRenglonSolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="EstadoRenglonSolicitudCompra")
     * @ORM\JoinColumn(name="id_estado_renglon_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoRenglonSolicitudCompra;

    /**
     * @var \ADIF\ComprasBundle\Entity\UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida", inversedBy="renglonesSolicitudCompra")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=false)
     * 
     */
    protected $unidadMedida;

    /**
     * @var float
     * 
     * @ORM\Column(name="cantidad_pendiente", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad pendiente debe ser de tipo numérico.")
     */
    protected $cantidadPendiente;

    /**
     * @var float
     * 
     * @ORM\Column(name="cantidad_solicitada", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad solicitada debe ser de tipo numérico.")
     */
    protected $cantidadSolicitada;

    /**
     * @ORM\OneToOne(targetEntity="EspecificacionTecnica", mappedBy="renglonSolicitudCompra", cascade={"persist", "remove"})
     */
    protected $especificacionTecnica;

    /**
     * @ORM\Column(name="id_usuario_usandolo", type="integer", nullable=true)
     */
    protected $idUsuarioUsandolo;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuarioUsandolo;

    /**
     * @ORM\OneToMany(targetEntity="RenglonRequerimiento", mappedBy="renglonSolicitudCompra")
     */
    protected $renglonesRequerimiento;

    /**
     * @var \ADIF\ComprasBundle\Entity\Rubro
     *
     * @ORM\ManyToOne(targetEntity="Rubro", inversedBy="renglonesSolicitudCompra")
     * @ORM\JoinColumn(name="id_rubro", referencedColumnName="id", nullable=false)
     * 
     */
    protected $rubro;

    /**
     * Constructor
     */
    public function __construct() {
        $this->justiprecioUnitario = 0;
        $this->cantidadSolicitada = 0;
        $this->cantidadPendiente = 0;
        $this->renglonesRequerimiento = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNumero();
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
     * Set solicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra
     * @return RenglonSolicitudCompra
     */
    public function setSolicitudCompra(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra = null) {
        $this->solicitudCompra = $solicitudCompra;

        return $this;
    }

    /**
     * Get solicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\SolicitudCompra 
     */
    public function getSolicitudCompra() {
        return $this->solicitudCompra;
    }

    /**
     * Set bienEconomico
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico
     * @return RenglonSolicitudCompra
     */
    public function setBienEconomico(\ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico = null) {
        $this->bienEconomico = $bienEconomico;

        return $this;
    }

    /**
     * Get bienEconomico
     *
     * @return \ADIF\ComprasBundle\Entity\BienEconomico 
     */
    public function getBienEconomico() {
        return $this->bienEconomico;
    }

    /**
     * Set rubro
     *
     * @param \ADIF\ComprasBundle\Entity\Rubro $rubro
     * @return RenglonSolicitudCompra
     */
    public function setRubro(\ADIF\ComprasBundle\Entity\Rubro $rubro = null) {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * Get rubro
     *
     * @return \ADIF\ComprasBundle\Entity\Rubro 
     */
    public function getRubro() {
        return $this->rubro;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return RenglonSolicitudCompra
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
     * Set justiprecioUnitario
     *
     * @param float $justiprecioUnitario
     * @return RenglonSolicitudCompra
     */
    public function setJustiprecioUnitario($justiprecioUnitario) {
        $this->justiprecioUnitario = $justiprecioUnitario;

        return $this;
    }

    /**
     * Get justiprecioUnitario
     *
     * @return float 
     */
    public function getJustiprecioUnitario() {
        return $this->justiprecioUnitario;
    }

    /**
     * Set prioridad
     *
     * @param \ADIF\ComprasBundle\Entity\Prioridad $prioridad
     * @return RenglonSolicitudCompra
     */
    public function setPrioridad(\ADIF\ComprasBundle\Entity\Prioridad $prioridad = null) {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return \ADIF\ComprasBundle\Entity\Prioridad 
     */
    public function getPrioridad() {
        return $this->prioridad;
    }

    /**
     * Set estadoRenglonSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoRenglonSolicitudCompra $estadoRenglonSolicitudCompra
     * @return RenglonSolicitudCompra
     */
    public function setEstadoRenglonSolicitudCompra(\ADIF\ComprasBundle\Entity\EstadoRenglonSolicitudCompra $estadoRenglonSolicitudCompra = null) {
        $this->estadoRenglonSolicitudCompra = $estadoRenglonSolicitudCompra;

        return $this;
    }

    /**
     * Get estadoRenglonSolicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoRenglonSolicitudCompra 
     */
    public function getEstadoRenglonSolicitudCompra() {
        return $this->estadoRenglonSolicitudCompra;
    }

    /**
     * Set unidadMedida
     *
     * @param \ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida
     * @return RenglonSolicitudCompra
     */
    public function setUnidadMedida(\ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida = null) {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return \ADIF\ComprasBundle\Entity\UnidadMedida
     */
    public function getUnidadMedida() {
        return $this->unidadMedida;
    }

    /**
     * Set cantidadPendiente
     *
     * @param float $cantidadPendiente
     * @return RenglonSolicitudCompra
     */
    public function setCantidadPendiente($cantidadPendiente) {
        $this->cantidadPendiente = $cantidadPendiente;

        return $this;
    }

    /**
     * Get cantidadPendiente
     *
     * @return float 
     */
    public function getCantidadPendiente() {
        return $this->cantidadPendiente;
    }

    /**
     * Set cantidadSolicitada
     *
     * @param float $cantidadSolicitada
     * @return RenglonSolicitudCompra
     */
    public function setCantidadSolicitada($cantidadSolicitada) {
        $this->cantidadSolicitada = $cantidadSolicitada;

        return $this;
    }

    /**
     * Get cantidadSolicitada
     *
     * @return float 
     */
    public function getCantidadSolicitada() {
        return $this->cantidadSolicitada;
    }

    /**
     * Set especificacionTecnica
     *
     * @param \ADIF\ComprasBundle\Entity\EspecificacionTecnica $especificacionTecnica
     * @return RenglonSolicitudCompra
     */
    public function setEspecificacionTecnica(\ADIF\ComprasBundle\Entity\EspecificacionTecnica $especificacionTecnica = null) {
        $this->especificacionTecnica = $especificacionTecnica;

        return $this;
    }

    /**
     * Get especificacionTecnica
     *
     * @return \ADIF\ComprasBundle\Entity\EspecificacionTecnica 
     */
    public function getEspecificacionTecnica() {
        return $this->especificacionTecnica;
    }

    /**
     * 
     * @return type
     */
    public function getIdUsuarioUsandolo() {
        return $this->idUsuarioUsandolo;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuarioUsandolo
     */
    public function setUsuarioUsandolo($usuarioUsandolo) {

        if (null != $usuarioUsandolo) {
            $this->idUsuarioUsandolo = $usuarioUsandolo->getId();
        } //.
        else {
            $this->idUsuarioUsandolo = null;
        }

        $this->usuarioUsandolo = $usuarioUsandolo;
    }

    /**
     * 
     * @return type
     */
    public function getUsuarioUsandolo() {
        return $this->usuarioUsandolo;
    }

    /**
     * Add renglonesRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento
     * @return RenglonSolicitudCompra
     */
    public function addRenglonesRequerimiento(\ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento) {
        $this->renglonesRequerimiento[] = $renglonesRequerimiento;

        return $this;
    }

    /**
     * Remove renglonesRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento
     */
    public function removeRenglonesRequerimiento(\ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento) {
        $this->renglonesRequerimiento->removeElement($renglonesRequerimiento);
    }

    /**
     * Get renglonesRequerimiento
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesRequerimiento() {
        return $this->renglonesRequerimiento;
    }

    /**
     * Get justiprecioTotal
     *
     * @return float 
     */
    public function getJustiprecioTotal() {
        return $this->justiprecioUnitario * $this->cantidadSolicitada;
    }

    /**
     * Set renglonPedidoInterno
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonPedidoInterno $renglonPedidoInterno
     * @return RenglonSolicitudCompra
     */
    public function setRenglonPedidoInterno(\ADIF\ComprasBundle\Entity\RenglonPedidoInterno $renglonPedidoInterno = null) {
        $this->renglonPedidoInterno = $renglonPedidoInterno;

        return $this;
    }

    /**
     * Get renglonPedidoInterno
     *
     * @return \ADIF\ComprasBundle\Entity\RenglonPedidoInterno 
     */
    public function getRenglonPedidoInterno() {
        return $this->renglonPedidoInterno;
    }

    /**
     * 
     * @return type
     */
    public function getNumero() {

        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * 
     * @return type
     */
    public function getArea() {

        $area = null;

        if ($this->renglonPedidoInterno != null) {

            $area = $this->renglonPedidoInterno->getArea();
        } else {

            $area = $this->solicitudCompra->getUsuario()->getArea();
        }

        return $area;
    }

}
