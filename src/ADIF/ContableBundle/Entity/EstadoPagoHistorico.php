<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoPagoHistorico
 *
 * @author Manuel Becerra
 * created 23/10/2015
 * 
 * @ORM\Table(name="estado_pago_historico")
 * @ORM\Entity
 */
class EstadoPagoHistorico extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumn(name="id_estado_pago", referencedColumnName="id")
     * 
     */
    protected $estadoPago;

    /**
     * @var \ADIF\ContableBundle\Entity\Cheque
     *
     * @ORM\ManyToOne(targetEntity="Cheque", cascade={"all"}, inversedBy="historicoEstados")
     * @ORM\JoinColumn(name="id_cheque", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cheque;

    /**
     * @var \ADIF\ContableBundle\Entity\TransferenciaBancaria
     *
     * @ORM\ManyToOne(targetEntity="TransferenciaBancaria", cascade={"all"}, inversedBy="historicoEstados")
     * @ORM\JoinColumn(name="id_transferencia", referencedColumnName="id", nullable=true)
     * 
     */
    protected $transferencia;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fecha = new \DateTime();
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return EstadoPagoHistorico
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set idUsuario
     *
     * @param integer $idUsuario
     * @return EstadoPagoHistorico
     */
    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return integer 
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
     * Set estadoPago
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPago $estadoPago
     * @return EstadoPagoHistorico
     */
    public function setEstadoPago(\ADIF\ContableBundle\Entity\EstadoPago $estadoPago = null) {
        $this->estadoPago = $estadoPago;

        return $this;
    }

    /**
     * Get estadoPago
     *
     * @return \ADIF\ContableBundle\Entity\EstadoPago
     */
    public function getEstadoPago() {
        return $this->estadoPago;
    }

    /**
     * Set cheque
     *
     * @param \ADIF\ContableBundle\Entity\Cheque $cheque
     * @return EstadoPagoHistorico
     */
    public function setCheque(\ADIF\ContableBundle\Entity\Cheque $cheque = null) {
        $this->cheque = $cheque;

        return $this;
    }

    /**
     * Get cheque
     *
     * @return \ADIF\ContableBundle\Entity\Cheque 
     */
    public function getCheque() {
        return $this->cheque;
    }

    /**
     * Set transferencia
     *
     * @param \ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia
     * @return EstadoPagoHistorico
     */
    public function setTransferencia(\ADIF\ContableBundle\Entity\TransferenciaBancaria $transferencia = null) {
        $this->transferencia = $transferencia;

        return $this;
    }

    /**
     * Get transferencia
     *
     * @return \ADIF\ContableBundle\Entity\TransferenciaBancaria 
     */
    public function getTransferencia() {
        return $this->transferencia;
    }

}
