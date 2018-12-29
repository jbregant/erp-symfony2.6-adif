<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoImportancia
 * 
 * @author Manuel Becerra
 * created 13/07/2014
 * 
 * @ORM\Table(name="tipo_importancia")
 * @ORM\Entity
 * @UniqueEntity("denominacionTipoImportancia", message="La denominación ingresada ya se encuentra en uso.")
 * @UniqueEntity("aliasTipoImportancia", message="El alias ingresado ya se encuentra en uso.")
 */
class TipoImportancia extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=50, unique=true, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoImportancia;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=50, unique=true, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El alias no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $aliasTipoImportancia;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=25, nullable=true)
     * @Assert\Length(
     *      max="25", 
     *      maxMessage="El color no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $colorTipoImportancia;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionTipoImportancia;
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
     * Set denominacionTipoImportancia
     *
     * @param string $denominacionTipoImportancia
     * @return TipoImportancia
     */
    public function setDenominacionTipoImportancia($denominacionTipoImportancia) {
        $this->denominacionTipoImportancia = $denominacionTipoImportancia;

        return $this;
    }

    /**
     * Get denominacionTipoImportancia
     *
     * @return string 
     */
    public function getDenominacionTipoImportancia() {
        return $this->denominacionTipoImportancia;
    }

    /**
     * Set aliasTipoImportancia
     *
     * @param string $aliasTipoImportancia
     * @return TipoImportancia
     */
    public function setAliasTipoImportancia($aliasTipoImportancia) {
        $this->aliasTipoImportancia = $aliasTipoImportancia;

        return $this;
    }

    /**
     * Get aliasTipoImportancia
     *
     * @return string 
     */
    public function getAliasTipoImportancia() {
        return $this->aliasTipoImportancia;
    }

    /**
     * Set colorTipoImportancia
     *
     * @param string $colorTipoImportancia
     * @return TipoImportancia
     */
    public function setColorTipoImportancia($colorTipoImportancia) {
        $this->colorTipoImportancia = $colorTipoImportancia;

        return $this;
    }

    /**
     * Get colorTipoImportancia
     *
     * @return string 
     */
    public function getColorTipoImportancia() {
        return $this->colorTipoImportancia;
    }

}
