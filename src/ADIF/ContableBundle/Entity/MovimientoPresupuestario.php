<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of MovimientoPresupuestario
 *
 * 
 * @ORM\Table(name="movimiento_presupuestario")
 * @ORM\Entity
 */
class MovimientoPresupuestario extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TipoMovimientoPresupuestario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_movimiento_presupuestario", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoMovimientoPresupuestario;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="movimientosPresupuestariosOrigen")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_origen", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaOrigen;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestaria", inversedBy="movimientosPresupuestariosDestino")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_destino", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaDestino;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="text", nullable=true)
     */
    protected $detalle;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_actual", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto actual debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
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
     * Set tipoMovimientoPresupuestario
     *
     * @param TipoMovimientoPresupuestario $tipoMovimientoPresupuestario
     * @return MovimientoPresupuestario
     */
    public function setTipoMovimientoPresupuestario(TipoMovimientoPresupuestario $tipoMovimientoPresupuestario) {
        $this->tipoMovimientoPresupuestario = $tipoMovimientoPresupuestario;

        return $this;
    }

    /**
     * Get tipoMovimientoPresupuestario
     *
     * @return TipoMovimientoPresupuestario 
     */
    public function getTipoMovimientoPresupuestario() {
        return $this->tipoMovimientoPresupuestario;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return MovimientoPresupuestario
     */
    public function setDetalle($detalle) {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle() {
        return $this->detalle;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return MovimientoPresupuestario
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set cuentaPresupuestariaOrigen
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestariaOrigen
     * @return MovimientoPresupuestario
     */
    public function setCuentaPresupuestariaOrigen(\ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestariaOrigen = null) {
        $this->cuentaPresupuestariaOrigen = $cuentaPresupuestariaOrigen;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaOrigen
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestaria 
     */
    public function getCuentaPresupuestariaOrigen() {
        return $this->cuentaPresupuestariaOrigen;
    }

    /**
     * Set cuentaPresupuestariaDestino
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestariaDestino
     * @return MovimientoPresupuestario
     */
    public function setCuentaPresupuestariaDestino(\ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentaPresupuestariaDestino = null) {
        $this->cuentaPresupuestariaDestino = $cuentaPresupuestariaDestino;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaDestino
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestaria 
     */
    public function getCuentaPresupuestariaDestino() {
        return $this->cuentaPresupuestariaDestino;
    }

}
