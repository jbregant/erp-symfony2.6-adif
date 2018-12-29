<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adicional
 * 
 * @author Manuel Becerra
 * created 10/12/2014
 * 
 * @ORM\MappedSuperclass 
 */
class Adicional extends BaseAuditoria implements BaseAuditable {

    /**
     * @ORM\Column(name="signo", type="string", length=1, nullable=false)
     * @Assert\Choice(choices = {"+", "-"}, message = "Elija un signo válido.")
     */
    protected $signo;

    /**
     * @var float
     * 
     * @ORM\Column(name="valor", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El valor debe ser de tipo numérico.")
     */
    protected $valor;

    /**
     * @ORM\Column(name="tipo_valor", type="string", length=1, nullable=false)
     * @Assert\NotBlank(message = "El tipo de valor no debe estar en blanco.")
     * @Assert\Choice(choices = {"$", "%", null}, message = "Elija un tipo de valor válido.")
     */
    protected $tipoValor;

    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=true)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;

    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=10, scale=4, nullable=false, options={"default": 1})
     */
    protected $tipoCambio;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Constructor
     */
    public function __construct() {

        $this->signo = "+";

        $this->tipoCambio = 1;
    }

    /**
     * Set signo
     *
     * @param string $signo
     * @return AdicionalCotizacion
     */
    public function setSigno($signo) {
        $this->signo = $signo;

        return $this;
    }

    /**
     * Get signo
     *
     * @return string 
     */
    public function getSigno() {
        return $this->signo;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return AdicionalCotizacion
     */
    public function setValor($valor) {
        $this->valor = $valor;

        return $this;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getValor($enMCL = true) {

        return $this->valor * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * Set tipoValor
     *
     * @param string $tipoValor
     * @return Adicional
     */
    public function setTipoValor($tipoValor) {
        $this->tipoValor = $tipoValor;

        return $this;
    }

    /**
     * Get tipoValor
     *
     * @return string 
     */
    public function getTipoValor() {
        return $this->tipoValor;
    }

    /**
     * Set idTipoMoneda
     *
     * @param integer $idTipoMoneda
     * @return Adicional
     */
    public function setIdTipoMoneda($idTipoMoneda) {
        $this->idTipoMoneda = $idTipoMoneda;

        return $this;
    }

    /**
     * Get idTipoMoneda
     *
     * @return integer 
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     */
    public function setTipoMoneda($tipoMoneda) {

        if (null != $tipoMoneda) {
            $this->idTipoMoneda = $tipoMoneda->getId();
        } //.
        else {
            $this->idTipoMoneda = null;
        }

        $this->tipoMoneda = $tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return Adicional
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return AdicionalCotizacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {

        $tipoMoneda = "$";

        if ($this->idTipoMoneda != null) {

            $tipoMoneda = $this->getTipoMoneda()->getSimboloTipoMoneda();
        }

        return $tipoMoneda;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    private function getTipoCambioCalculado($enMCL) {

        return $enMCL ? $this->tipoCambio : 1;
    }

}
