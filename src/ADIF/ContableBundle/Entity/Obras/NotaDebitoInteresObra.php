<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebitoInteresObra
 *
 * @author Manuel Becerra
 * created 27/02/2015
 * 
 * @ORM\Table(name="nota_debito_interes_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 */
class NotaDebitoInteresObra extends ComprobanteObra {

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
     * @return NotaDebitoInteresObra
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
     * @return NotaDebitoInteresObra
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
