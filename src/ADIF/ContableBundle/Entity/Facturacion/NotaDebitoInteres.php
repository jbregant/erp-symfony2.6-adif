<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebitoInteres
 *
 * @author Manuel Becerra
 * created 27/02/2015
 * 
 * @ORM\Table(name="nota_debito_interes")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 */
class NotaDebitoInteres extends NotaDebitoVenta implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_atraso", type="integer", nullable=false)
     */
    protected $diasAtraso;

    /**
     * @var double
     * @ORM\Column(name="monto_interes", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoInteres;

    /**
     * Set diasAtraso
     *
     * @param integer $diasAtraso
     * @return NotaDebitoInteres
     */
    public function setDiasAtraso($diasAtraso) {
        $this->diasAtraso = $diasAtraso;

        return $this;
    }

    /**
     * Get diasAtraso
     *
     * @return integer 
     */
    public function getDiasAtraso() {
        return $this->diasAtraso;
    }

    /**
     * Set montoInteres
     *
     * @param string $montoInteres
     * @return NotaDebitoInteres
     */
    public function setMontoInteres($montoInteres) {
        $this->montoInteres = $montoInteres;

        return $this;
    }

    /**
     * Get montoInteres
     *
     * @return string 
     */
    public function getMontoInteres() {
        return $this->montoInteres;
    }

}
