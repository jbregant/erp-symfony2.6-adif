<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EscalaRetencionHonorariosGanancias
 * 
 * @ORM\Table(name="escala_retencion_honorarios_ganancias")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\EscalaRetencionHonorariosGananciasRepository")
 */
class EscalaRetencionHonorariosGanancias {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_desde", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoDesde;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_hasta", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoHasta;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_a_retener", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoARetener;

    /**
     * @var float
     * 
     * @ORM\Column(name="alicuota", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La alícuota debe ser de tipo numérico.")
     */
    protected $alicuota;

    /**
     * @var float
     * 
     * @ORM\Column(name="minimo_no_imponible", type="float", nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe mínimo no imponible debe ser de tipo numérico.")
     */
    protected $minimoNoImponible;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set montoDesde
     *
     * @param float $montoDesde
     * @return EscalaRetencionHonorariosGanancias
     */
    public function setMontoDesde($montoDesde) {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return float 
     */
    public function getMontoDesde() {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param float $montoHasta
     * @return EscalaRetencionHonorariosGanancias
     */
    public function setMontoHasta($montoHasta) {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return float 
     */
    public function getMontoHasta() {
        return $this->montoHasta;
    }

    /**
     * Set montoARetener
     *
     * @param float $montoARetener
     * @return EscalaRetencionHonorariosGanancias
     */
    public function setMontoARetener($montoARetener) {
        $this->montoARetener = $montoARetener;

        return $this;
    }

    /**
     * Get montoARetener
     *
     * @return float 
     */
    public function getMontoARetener() {
        return $this->montoARetener;
    }

    /**
     * Set minimoNoImponible
     *
     * @param float $minimoNoImponible
     * @return EscalaRetencionHonorariosGanancias
     */
    public function setMinimoNoImponible($minimoNoImponible) {
        $this->minimoNoImponible = $minimoNoImponible;

        return $this;
    }

    /**
     * Get minimoNoImponible
     *
     * @return float 
     */
    public function getMinimoNoImponible() {
        return $this->minimoNoImponible;
    }

    /**
     * Set alicuota
     *
     * @param float $alicuota
     * @return EscalaRetencionHonorariosGanancias
     */
    public function setAlicuota($alicuota) {
        $this->alicuota = $alicuota;

        return $this;
    }

    /**
     * Get alicuota
     *
     * @return float 
     */
    public function getAlicuota() {
        return $this->alicuota;
    }

}
