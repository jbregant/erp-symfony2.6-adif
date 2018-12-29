<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LicitacionObra
 *
 * @author Darío Rapetti
 * created 29/11/2014
 * 
 * @ORM\Table(name="licitacion_obra")
 * @ORM\Entity 
 */
class LicitacionObra extends Licitacion {

    /**
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Obras\Tramo", mappedBy="licitacion")
     */
    protected $tramos;

    /**
     * 
     */
    public function __construct() {

        parent::__construct();

        $this->tramos = new ArrayCollection();
    }

    /**
     * Add tramo
     *
     * @param \ADIF\ContableBundle\Entity\Obras\Tramo $tramo
     * @return LicitacionObra
     */
    public function addTramo(\ADIF\ContableBundle\Entity\Obras\Tramo $tramo) {
        $this->tramo[] = $tramo;

        return $this;
    }

    /**
     * Remove tramo
     *
     * @param \ADIF\ContableBundle\Entity\Obras\Tramo $tramo
     */
    public function removeTramo(\ADIF\ContableBundle\Entity\Obras\Tramo $tramo) {
        $this->tramos->removeElement($tramo);
    }

    /**
     * Get tramos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTramos() {
        return $this->tramos;
    }

    /**
     * Get saldo
     * 
     * @return type
     */
    public function getSaldo() {

        $saldoLicitacion = $this->importeLicitacion;

        foreach ($this->tramos as $tramo) {
            $saldoLicitacion -= $tramo->getTotalContrato();
        }
		
		$epsilon = 0.00001;
		if ($saldoLicitacion <= $epsilon) {
			$saldoLicitacion = 0;
		}
		
        return $saldoLicitacion;
    }

    /**
     * Get porcentajeAdjudicado
     * 
     * @return type
     */
    public function getPorcentajeAdjudicado() {

        $porcentajeAdjudicado = 0;

        if ($this->getImporteLicitacion() > 0) {

            $porcentajeAdjudicado = ($this->getImporteLicitacion() - $this->getSaldo()) * 100 / $this->getImporteLicitacion();
        }

        return number_format($porcentajeAdjudicado, 2, ',', '.');
    }

}
