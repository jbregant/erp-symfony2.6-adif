<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * FondoReparo
 * 
 * @ORM\Table(name="fondo_reparo")
 * @ORM\Entity
 */
class FondoReparo extends DocumentoFinanciero {

    /**
     * @var double
     * @ORM\Column(name="porcentaje_abonar", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeAbonar;

    /**
     * Set porcentajeAbonar
     *
     * @param string $porcentajeAbonar
     * @return FondoReparo
     */
    public function setPorcentajeAbonar($porcentajeAbonar) {
        $this->porcentajeAbonar = $porcentajeAbonar;

        return $this;
    }

    /**
     * Get porcentajeAbonar
     *
     * @return string 
     */
    public function getPorcentajeAbonar() {
        return $this->porcentajeAbonar;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsFondoReparo() {
        return true;
    }

}
