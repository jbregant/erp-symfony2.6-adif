<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of TipoSegmentoPlanDeCuentas:
 *      Naturaleza
 *      Liquidez
 *      Centro de Costo
 *      Rubro
 *      Categoria
 *      Subcategoria
 *
 * @author Manuel Becerra
 * created 23/09/2014
 * 
 * @ORM\Table(name="tipo_segmento")
 * @ORM\Entity
 * @UniqueEntity("denominacionTipoSegmento", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoSegmentoPlanDeCuentas extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionTipoSegmento;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoSegmento;

    /**
     * 
     * @return type
     */
    public  function __toString() {
        return $this->denominacionTipoSegmento;
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
     * Set denominacionTipoSegmento
     *
     * @param string $denominacionTipoSegmento
     * @return TipoSegmentoPlanDeCuentas
     */
    public function setDenominacionTipoSegmento($denominacionTipoSegmento) {
        $this->denominacionTipoSegmento = $denominacionTipoSegmento;

        return $this;
    }

    /**
     * Get denominacionTipoSegmento
     *
     * @return string 
     */
    public function getDenominacionTipoSegmento() {
        return $this->denominacionTipoSegmento;
    }

    /**
     * Set descripcionTipoSegmento
     *
     * @param string $descripcionTipoSegmento
     * @return TipoSegmentoPlanDeCuentas
     */
    public function setDescripcionTipoSegmento($descripcionTipoSegmento) {
        $this->descripcionTipoSegmento = $descripcionTipoSegmento;

        return $this;
    }

    /**
     * Get descripcionTipoSegmento
     *
     * @return string 
     */
    public function getDescripcionTipoSegmento() {
        return $this->descripcionTipoSegmento;
    }

}
