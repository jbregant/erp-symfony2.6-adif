<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoAsientoContable 
 * 
 * Indica el estado del Asiento Contable.
 *
 * @author Manuel Becerra
 * created 26/09/2014
 * 
 * @ORM\Table(name="estado_asiento_contable")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstado", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoAsientoContable extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstado;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstado;
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
     * Set denominacionEstado
     *
     * @param string $denominacionEstado
     * @return EstadoAsientoContable
     */
    public function setDenominacionEstado($denominacionEstado) {
        $this->denominacionEstado = $denominacionEstado;

        return $this;
    }

    /**
     * Get denominacionEstado
     *
     * @return string 
     */
    public function getDenominacionEstado() {
        return $this->denominacionEstado;
    }

    /**
     * Set descripcionEstado
     *
     * @param string $descripcionEstado
     * @return EstadoAsientoContable
     */
    public function setDescripcionEstado($descripcionEstado) {
        $this->descripcionEstado = $descripcionEstado;

        return $this;
    }

    /**
     * Get descripcionEstado
     *
     * @return string 
     */
    public function getDescripcionEstado() {
        return $this->descripcionEstado;
    }

}
