<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoAdicional 
 * 
 * Indica el tipo adicional. 
 * 
 * Por ejemplo:
 *      Gastos de Envìo.
 *      Gastos de Importación.
 *      Otros.
 * 
 *
 * @author Carlos Sabena
 * created 15/07/2014
 * 
 * @ORM\Table(name="tipo_adicional")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionAdicional", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message = "La denominación ingresada ya se encuentra en uso."
 * )
 */
class TipoAdicional extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del adicional no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionAdicional;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del adicional no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionAdicional;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionAdicional;
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
     * Set denominacionAdicional
     *
     * @param string $denominacionAdicional
     * @return TipoAdicional
     */
    public function setDenominacionAdicional($denominacionAdicional) {
        $this->denominacionAdicional = $denominacionAdicional;

        return $this;
    }

    /**
     * Get denominacionAdicional
     *
     * @return string 
     */
    public function getDenominacionAdicional() {
        return $this->denominacionAdicional;
    }

    /**
     * Set descripcionAdicional
     *
     * @param string $descripcionAdicional
     * @return TipoAdicional
     */
    public function setDescripcionAdicional($descripcionAdicional) {
        $this->descripcionAdicional = $descripcionAdicional;

        return $this;
    }

    /**
     * Get descripcionAdicional
     *
     * @return string 
     */
    public function getDescripcionAdicional() {
        return $this->descripcionAdicional;
    }

}
