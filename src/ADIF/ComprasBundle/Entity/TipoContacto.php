<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoContacto 
 * 
 * Indica el Tipo de Contacto. 
 * 
 * Por ejemplo:
 *      Teléfono.
 *      Celular.
 *      Email.
 *      Fax.
 *      Página Web.
 * 
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="tipo_contacto")
 * @ORM\Entity
 * @UniqueEntity("denominacionTipoContacto", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoContacto extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionTipoContacto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoContacto;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionTipoContacto;
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
     * Set denominacionTipoContacto
     *
     * @param string $denominacionTipoContacto
     * @return TipoContacto
     */
    public function setDenominacionTipoContacto($denominacionTipoContacto) {
        $this->denominacionTipoContacto = $denominacionTipoContacto;

        return $this;
    }

    /**
     * Get denominacionTipoContacto
     *
     * @return string 
     */
    public function getDenominacionTipoContacto() {
        return $this->denominacionTipoContacto;
    }

    /**
     * Set descripcionTipoContacto
     *
     * @param string $descripcionTipoContacto
     * @return TipoContacto
     */
    public function setDescripcionTipoContacto($descripcionTipoContacto) {
        $this->descripcionTipoContacto = $descripcionTipoContacto;

        return $this;
    }

    /**
     * Get descripcionTipoContacto
     *
     * @return string 
     */
    public function getDescripcionTipoContacto() {
        return $this->descripcionTipoContacto;
    }

}
