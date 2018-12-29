<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * RedeterminacionObra
 * 
 * @ORM\Table(name="redeterminacion_obra")
 * @ORM\Entity
 */
class RedeterminacionObra extends DocumentoFinanciero {

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=8, nullable=true)
     */
    protected $numero;

    /**
     * @var double
     * @ORM\Column(name="monto_fondo_reparo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoFondoReparo;

    /**
     * Set numero
     *
     * @param string $numero
     * @return RedeterminacionObra
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return 'R' . str_pad($this->numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumeroSinFormato() {
        return $this->numero;
    }

    /**
     * Set montoFondoReparo
     *
     * @param double $montoFondoReparo
     * @return RedeterminacionObra
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
    public function getEsRedeterminacionObra() {
        return true;
    }

}
