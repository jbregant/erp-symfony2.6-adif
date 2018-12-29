<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonPedidoInterno
 *
 * @author Carlos Sabena
 * created 10/09/2014
 * 
 * @ORM\Table(name="renglon_pedido_interno")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RenglonPedidoInternoRepository")
 */
class RenglonPedidoInterno extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\PedidoInterno
     *
     * @ORM\ManyToOne(targetEntity="PedidoInterno", inversedBy="renglonesPedidoInterno")
     * @ORM\JoinColumn(name="id_pedido_interno", referencedColumnName="id", nullable=false)
     * 
     */
    protected $pedidoInterno;

    /**
     * @ORM\OneToMany(targetEntity="RenglonSolicitudCompra", mappedBy="renglonPedidoInterno")
     * */
    protected $renglonSolicitudCompra;

    /**
     * @var \ADIF\ComprasBundle\Entity\Rubro
     *
     * @ORM\ManyToOne(targetEntity="Rubro")
     * @ORM\JoinColumn(name="id_rubro", referencedColumnName="id", nullable=false)
     * 
     */
    protected $rubro;

    /**
     * @var \ADIF\ComprasBundle\Entity\BienEconomico
     *
     * @ORM\ManyToOne(targetEntity="BienEconomico")
     * @ORM\JoinColumn(name="id_bien_economico", referencedColumnName="id", nullable=false)
     * 
     */
    protected $bienEconomico;

    /**
     * @var \ADIF\ComprasBundle\Entity\UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
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
     * @var \ADIF\ComprasBundle\Entity\Prioridad
     *
     * @ORM\ManyToOne(targetEntity="Prioridad")
     * @ORM\JoinColumn(name="id_prioridad", referencedColumnName="id", nullable=false)
     * 
     */
    protected $prioridad;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoRenglonPedidoInterno
     *
     * @ORM\ManyToOne(targetEntity="EstadoRenglonPedidoInterno")
     * @ORM\JoinColumn(name="id_estado_renglon_pedido_interno", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoRenglonPedidoInterno;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cantidadSolicitada = 0;
        $this->cantidadPendiente = 0;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return "renglon";
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
     * Set pedidoInterno
     *
     * @param \ADIF\ComprasBundle\Entity\PedidoInterno $pedidoInterno
     * @return RenglonPedidoInterno
     */
    public function setPedidoInterno(\ADIF\ComprasBundle\Entity\PedidoInterno $pedidoInterno) {
        $this->pedidoInterno = $pedidoInterno;

        return $this;
    }

    /**
     * Get pedidoInterno
     *
     * @return \ADIF\ComprasBundle\Entity\PedidoInterno 
     */
    public function getPedidoInterno() {
        return $this->pedidoInterno;
    }

    /**
     * Set unidadMedida
     *
     * @param \ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida
     * @return RenglonPedidoInterno
     */
    public function setUnidadMedida(\ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida) {
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
     * @return RenglonPedidoInterno
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
     * @return RenglonPedidoInterno
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
     * Set rubro
     *
     * @param \ADIF\ComprasBundle\Entity\Rubro $rubro
     * @return RenglonPedidoInterno
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
     * Set bienEconomico
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico
     * @return RenglonPedidoInterno
     */
    public function setBienEconomico(\ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico) {
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return RenglonPedidoInterno
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
     * Set prioridad
     *
     * @param \ADIF\ComprasBundle\Entity\Prioridad $prioridad
     * @return RenglonPedidoInterno
     */
    public function setPrioridad(\ADIF\ComprasBundle\Entity\Prioridad $prioridad) {
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
     * Set estadoRenglonPedidoInterno
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoRenglonPedidoInterno $estadoRenglonPedidoInterno
     * @return RenglonPedidoInterno
     */
    public function setEstadoRenglonPedidoInterno(\ADIF\ComprasBundle\Entity\EstadoRenglonPedidoInterno $estadoRenglonPedidoInterno) {
        $this->estadoRenglonPedidoInterno = $estadoRenglonPedidoInterno;

        return $this;
    }

    /**
     * Get estadoRenglonPedidoInterno
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoRenglonPedidoInterno 
     */
    public function getEstadoRenglonPedidoInterno() {
        return $this->estadoRenglonPedidoInterno;
    }

    /**
     * Set renglonSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonSolicitudCompra
     * @return RenglonPedidoInterno
     */
    public function setRenglonSolicitudCompra(\ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonSolicitudCompra = null) {
        $this->renglonSolicitudCompra = $renglonSolicitudCompra;

        return $this;
    }

    /**
     * Get renglonSolicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra 
     */
    public function getRenglonSolicitudCompra() {
        return $this->renglonSolicitudCompra;
    }

    /**
     * 
     * @return type
     */
    public function getArea() {

        return $this->getPedidoInterno()->getArea();
    }

}
