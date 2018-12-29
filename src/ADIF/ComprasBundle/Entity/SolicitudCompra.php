<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoSolicitud;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;

/**
 * SolicitudCompra
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="solicitud_compra")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\SolicitudCompraRepository")
 */
class SolicitudCompra extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_solicitud", type="date", nullable=false)
     */
    protected $fechaSolicitud;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=true)
     */
    protected $numeroReferencia;

    /**
     * @var TipoSolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="TipoSolicitudCompra")
     * @ORM\JoinColumn(name="id_tipo_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $tipoSolicitudCompra;

    /**
     * @ORM\OneToMany(targetEntity="RenglonSolicitudCompra", mappedBy="solicitudCompra", cascade={"persist", "remove"})
     */
    protected $renglonesSolicitudCompra;

    /**
     * @var EstadoSolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="EstadoSolicitudCompra", inversedBy="solicitudes")
     * @ORM\JoinColumn(name="id_estado_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoSolicitudCompra;

    /**
     * @var EntidadAutorizante
     *
     * @ORM\ManyToOne(targetEntity="EntidadAutorizante", inversedBy="solicitudes")
     * @ORM\JoinColumn(name="id_entidad_autorizante", referencedColumnName="id", nullable=true)
     * 
     */
    protected $entidadAutorizante;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;

    /**
     * @ORM\OneToOne(targetEntity="JustificacionSolicitudCompra", mappedBy="solicitudCompra", cascade={"persist", "remove"})
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
        $this->fechaSolicitud = new \DateTime();
        $this->renglonesSolicitudCompra = new ArrayCollection();
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
     * Set fechaSolicitud
     *
     * @param \DateTime $fechaSolicitud
     * @return SolicitudCompra
     */
    public function setFechaSolicitud($fechaSolicitud) {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    /**
     * Get fechaSolicitud
     *
     * @return \DateTime 
     */
    public function getFechaSolicitud() {
        return $this->fechaSolicitud;
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
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return SolicitudCompra
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
     * Set tipoSolicitudCompra
     *
     * @param TipoSolicitudCompra $tipoSolicitudCompra
     * @return SolicitudCompra
     */
    public function setTipoSolicitudCompra(TipoSolicitudCompra $tipoSolicitudCompra = null) {
        $this->tipoSolicitudCompra = $tipoSolicitudCompra;

        return $this;
    }

    /**
     * Get tipoSolicitudCompra
     *
     * @return TipoSolicitudCompra 
     */
    public function getTipoSolicitudCompra() {
        return $this->tipoSolicitudCompra;
    }

    /**
     * Add renglonesSolicitudCompra
     *
     * @param RenglonSolicitudCompra $renglonesSolicitudCompra
     * @return SolicitudCompra
     */
    public function addRenglonesSolicitudCompra(RenglonSolicitudCompra $renglonesSolicitudCompra) {
        $this->renglonesSolicitudCompra[] = $renglonesSolicitudCompra;

        return $this;
    }

    /**
     * Remove renglonesSolicitudCompra
     *
     * @param RenglonSolicitudCompra $renglonesSolicitudCompra
     */
    public function removeRenglonesSolicitudCompra(RenglonSolicitudCompra $renglonesSolicitudCompra) {
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

    /**
     * Set estadoSolicitudCompra
     *
     * @param EstadoSolicitudCompra $estadoSolicitudCompra
     * @return SolicitudCompra
     */
    public function setEstadoSolicitudCompra(EstadoSolicitudCompra $estadoSolicitudCompra = null) {
        $this->estadoSolicitudCompra = $estadoSolicitudCompra;

        return $this;
    }

    /**
     * Get estadoSolicitudCompra
     *
     * @return EstadoSolicitudCompra 
     */
    public function getEstadoSolicitudCompra() {
        return $this->estadoSolicitudCompra;
    }

    /**
     * Set entidadAutorizante
     *
     * @param EntidadAutorizante $entidadAutorizante
     * @return SolicitudCompra
     */
    public function setEntidadAutorizante(EntidadAutorizante $entidadAutorizante = null) {
        $this->entidadAutorizante = $entidadAutorizante;

        return $this;
    }

    /**
     * Get entidadAutorizante
     *
     * @return EntidadAutorizante 
     */
    public function getEntidadAutorizante() {
        return $this->entidadAutorizante;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return SolicitudCompra
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
     * Set justificacion
     *
     * @param JustificacionSolicitudCompra $justificacion
     * @return SolicitudCompra
     */
    public function setJustificacion(JustificacionSolicitudCompra $justificacion = null) {
        $this->justificacion = $justificacion;

        return $this;
    }

    /**
     * Get justificacion
     *
     * @return JustificacionSolicitudCompra 
     */
    public function getJustificacion() {
        return $this->justificacion;
    }

    /**
     * Get justiprecio
     */
    public function getJustiprecio() {

        $justiprecio = 0;

        foreach ($this->getRenglonesSolicitudCompra() as $renglon) {
            $justiprecio += $renglon->getJustiprecioTotal();
        }

        return $justiprecio;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return SolicitudCompra
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
     * 
     * @return type
     */
    public function getNumero() {
        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEditable() {

        $denominacionEstadoSolicitud = $this->getEstadoSolicitudCompra()->getDenominacionEstadoSolicitudCompra();

        return $denominacionEstadoSolicitud == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR //
                || $denominacionEstadoSolicitud == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_A_CORREGIR //
                || $denominacionEstadoSolicitud == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_DESAPROBADA;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAnulable() {

        $estaEnRequerimientoCompra = false;

        foreach ($this->renglonesSolicitudCompra as $renglonSolicitudCommpra) {

            /* @var $renglonSolicitudCommpra RenglonSolicitudCompra */
            if (!$renglonSolicitudCommpra->getRenglonesRequerimiento()->isEmpty()) {
				
				$requerimiento = $renglonSolicitudCommpra
									->getRenglonesRequerimiento()
									->first()
									->getRequerimiento();
				
				if ($requerimiento->getEstadoRequerimiento()->getDenominacionEstadoRequerimiento() != ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ANULADO) {
					// Si esta en requerimiento de compra pero el mismo se encuentra anulado, 
					// tiene que dejar anular
					$estaEnRequerimientoCompra = true;
					break;
				}
            }
        }

        // Si NO esta dentro de un Requerimiento y NO esta ANULADA
        return !$estaEnRequerimientoCompra && !$this->getEstaAnulada();
    }

    /**
     * 
     * @return boolean
     */
    public function getEstaAnulada() {

        $denominacionEstado = $this->getEstadoSolicitudCompra()->getDenominacionEstadoSolicitudCompra();

        return $denominacionEstado == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_ANULADA;
    }

    /**
     * 
     * @return array
     */
    public function getAreasOrigenPedido() {

        $areasOridenPedido = array();

        foreach ($this->renglonesSolicitudCompra as $renglonSolicitudCommpra) {

            $areasOridenPedido[$renglonSolicitudCommpra->getArea()->getId()] = $renglonSolicitudCommpra->getArea();
        }

        return $areasOridenPedido;
    }

}
