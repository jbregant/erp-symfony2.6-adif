<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoMoneda
 *
 * @author Manuel Becerra
 * created 25/06/2014
 * 
 * @ORM\Table(name="tipo_moneda")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"codigoTipoMoneda", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="El código ISO ingresado ya se encuentra en uso."
 * )
 */
class TipoMoneda extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="codigo", type="string", length=3, unique=true, nullable=false)
     * @Assert\Length(
     *      max="3", 
     *      maxMessage="El código ISO de la moneda no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoTipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="simbolo", type="string", length=4, nullable=false)
     * @Assert\Length(
     *      max="4", 
     *      maxMessage="El símbolo de la moneda no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $simboloTipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación de la moneda no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción de la moneda no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionTipoMoneda;

    /**
     * Indica si es la moneda de curso legal.
     * 
     * @var boolean
     *
     * @ORM\Column(name="es_mcl", type="boolean", nullable=false)
     */
    protected $esMCL;

    /**
     * @var float
     * 
     * @ORM\Column(name="tipo_cambio", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El tipo de cambio debe ser de tipo numérico.")
     */
    protected $tipoCambio;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esMCL = false;
        $this->tipoCambio = 1;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return '(' . $this->codigoTipoMoneda . ') ' . $this->denominacionTipoMoneda;
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
     * Set codigoTipoMoneda
     *
     * @param string $codigoTipoMoneda
     * @return TipoMoneda
     */
    public function setCodigoTipoMoneda($codigoTipoMoneda) {
        $this->codigoTipoMoneda = $codigoTipoMoneda;

        return $this;
    }

    /**
     * Get codigoTipoMoneda
     *
     * @return string 
     */
    public function getCodigoTipoMoneda() {
        return $this->codigoTipoMoneda;
    }

    /**
     * Set simboloTipoMoneda
     *
     * @param string $simboloTipoMoneda
     * @return TipoMoneda
     */
    public function setSimboloTipoMoneda($simboloTipoMoneda) {
        $this->simboloTipoMoneda = $simboloTipoMoneda;

        return $this;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {
        return $this->simboloTipoMoneda;
    }

    /**
     * Set denominacionTipoMoneda
     *
     * @param string $denominacionTipoMoneda
     * @return TipoMoneda
     */
    public function setDenominacionTipoMoneda($denominacionTipoMoneda) {
        $this->denominacionTipoMoneda = $denominacionTipoMoneda;

        return $this;
    }

    /**
     * Get denominacionTipoMoneda
     *
     * @return string 
     */
    public function getDenominacionTipoMoneda() {
        return $this->denominacionTipoMoneda;
    }

    /**
     * Set descripcionTipoMoneda
     *
     * @param string $descripcionTipoMoneda
     * @return TipoMoneda
     */
    public function setDescripcionTipoMoneda($descripcionTipoMoneda) {
        $this->descripcionTipoMoneda = $descripcionTipoMoneda;

        return $this;
    }

    /**
     * Get descripcionTipoMoneda
     *
     * @return string 
     */
    public function getDescripcionTipoMoneda() {
        return $this->descripcionTipoMoneda;
    }

    /**
     * Set esMCL
     *
     * @param boolean $esMCL
     * @return TipoMoneda
     */
    public function setEsMCL($esMCL) {
        $this->esMCL = $esMCL;

        return $this;
    }

    /**
     * Get esMCL
     *
     * @return boolean 
     */
    public function getEsMCL() {
        return $this->esMCL;
    }

    /**
     * Set tipoCambio
     *
     * @param float $tipoCambio
     * @return TipoMoneda
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return float 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    }

}
