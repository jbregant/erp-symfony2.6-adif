<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoPago 
 * 
 * Indica el Tipo de Pago. 
 * 
 * Por ejemplo:
 *      Cheque.
 *      Pagaré.
 *      Transferencia.
 * 
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="tipo_pago")
 * @ORM\Entity
 * @UniqueEntity("denominacionFormaPago", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoPago extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoPago;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoPago;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionTipoPago;
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
     * Set denominacionTipoPago
     *
     * @param string $denominacionTipoPago
     * @return TipoPago
     */
    public function setDenominacionTipoPago($denominacionTipoPago) {
        $this->denominacionTipoPago = $denominacionTipoPago;

        return $this;
    }

    /**
     * Get denominacionTipoPago
     *
     * @return string 
     */
    public function getDenominacionTipoPago() {
        return $this->denominacionTipoPago;
    }

    /**
     * Set descripcionTipoPago
     *
     * @param string $descripcionTipoPago
     * @return TipoPago
     */
    public function setDescripcionTipoPago($descripcionTipoPago) {
        $this->descripcionTipoPago = $descripcionTipoPago;

        return $this;
    }

    /**
     * Get descripcionTipoPago
     *
     * @return string 
     */
    public function getDescripcionTipoPago() {
        return $this->descripcionTipoPago;
    }

}
