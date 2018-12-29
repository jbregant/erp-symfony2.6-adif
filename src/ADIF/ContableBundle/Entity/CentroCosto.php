<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CentroCosto
 *
 * @author Darío Rapetti
 * created 10/11/2014
 * 
 * @ORM\Table(name="centro_costo")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"codigo", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="Ya existe un centro de costo con igual código."
 * )
 */
class CentroCosto extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="integer", length=2, unique=true, nullable=false)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, unique=true, nullable=false)
     * @Assert\Length(
     *      max="100", 
     *      maxMessage="La denominaci&oacute;n del centro no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->denominacion;
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
     * Set codigo
     *
     * @param integer $codigo
     * @return CentroCosto
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return CentroCosto
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

}
