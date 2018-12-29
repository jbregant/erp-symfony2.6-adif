<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnticipoFinanciero
 * 
 * @ORM\Table(name="anticipo_financiero")
 * @ORM\Entity
 */
class AnticipoFinanciero extends DocumentoFinanciero {

    /**
     * @var double
     * @ORM\Column(name="porcentaje_anticipo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeAnticipo;

    /**
     * @var double
     * @ORM\Column(name="monto_fondo_reparo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoFondoReparo;

    /**
     * Set porcentajeAnticipo
     *
     * @param string $porcentajeAnticipo
     * @return AnticipoFinanciero
     */
    public function setPorcentajeAnticipo($porcentajeAnticipo) {
        $this->porcentajeAnticipo = $porcentajeAnticipo;

        return $this;
    }

    /**
     * Get porcentajeAnticipo
     *
     * @return string 
     */
    public function getPorcentajeAnticipo() {
        return $this->porcentajeAnticipo;
    }

    /**
     * Set montoFondoReparo
     *
     * @param double $montoFondoReparo
     * @return AnticipoFinanciero
     */
    public function setMontoFondoReparo($montoFondoReparo) {
        $this->montoFondoReparo = $montoFondoReparo;

        return $this;
    }

    /**
     * Get montoFondoReparo
     *
     * @return double 
     */
    public function getMontoFondoReparo() {
        return $this->montoFondoReparo;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAnticipoFinanciero() {
        return true;
    }

}
