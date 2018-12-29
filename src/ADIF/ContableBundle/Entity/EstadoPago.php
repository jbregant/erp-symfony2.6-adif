<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoPago
 * 
 * Indica el estado del Pago
 *
 * @author Manuel Becerra
 * created 08/01/2015
 * 
 * @ORM\Table(name="estado_pago")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstado", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoPago extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="id_tipo_importancia", type="integer", nullable=true)
     */
    protected $idTipoImportancia;

    /**
     * @var ADIF\ComprasBundle\Entity\TipoImportancia
     */
    protected $tipoImportancia;

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
     * @return EstadoPago
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
     * @return EstadoPago
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

    /**
     * 
     * @return type
     */
    public function getIdTipoImportancia() {
        return $this->idTipoImportancia;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     */
    public function setTipoImportancia($tipoImportancia) {

        if (null != $tipoImportancia) {
            $this->idTipoImportancia = $tipoImportancia->getId();
        } //.
        else {
            $this->idTipoImportancia = null;
        }

        $this->tipoImportancia = $tipoImportancia;
    }

    /**
     * 
     * @return type
     */
    public function getTipoImportancia() {
        return $this->tipoImportancia;
    }

}
