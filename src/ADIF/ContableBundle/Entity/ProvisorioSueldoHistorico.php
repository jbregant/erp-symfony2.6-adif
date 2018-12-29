<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProvisorioSueldoHistorico
 *
 * @author Manuel Becerra
 * created 27/05/2015
 * 
 * @ORM\Table(name="provisorio_sueldo_historico")
 * @ORM\Entity
 */
class ProvisorioSueldoHistorico extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ProvisorioSueldo", inversedBy="historicos")
     * @ORM\JoinColumn(name="id_provisorio", referencedColumnName="id")
     */
    protected $provisorio;

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
     * @var float
     * 
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="El detalle no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $detalle;

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
     * Set provisorio
     *
     * @param \ADIF\ContableBundle\Entity\ProvisorioSueldo $provisorio
     * @return ProvisorioSueldoHistorico
     */
    public function setProvisorio(\ADIF\ContableBundle\Entity\ProvisorioSueldo $provisorio = null) {
        $this->provisorio = $provisorio;

        return $this;
    }

    /**
     * Get provisorio
     *
     * @return \ADIF\ContableBundle\Entity\ProvisorioSueldo 
     */
    public function getProvisorio() {
        return $this->provisorio;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return ProvisorioSueldoHistorico
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
     * @return ProvisorioSueldoHistorico
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
     * Set monto
     *
     * @param float $monto
     * @return ProvisorioSueldoHistorico
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
     * Set detalle
     *
     * @param string $detalle
     * @return ProvisorioSueldoHistorico
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
        return html_entity_decode($this->detalle, ENT_QUOTES);
    }

}
