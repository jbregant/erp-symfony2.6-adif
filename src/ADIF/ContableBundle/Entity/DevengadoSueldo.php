<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevengadoSueldo
 *
 * @author Darío Rapetti
 * created 07/04/2015
 * 
 * @ORM\Table(name="devengado_sueldo")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DevengadoSueldoRepository")
 */
class DevengadoSueldo extends Devengado {

    /**
     * @ORM\Column(name="id_liquidacion", type="integer", nullable=false)
     */
    protected $idLiquidacion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Liquidacion
     */
    protected $liquidacion;

    /**
     * Set idLiquidacion
     *
     * @param integer $idLiquidacion
     * @return DevengadoSueldo
     */
    public function setIdLiquidacion($idLiquidacion) {
        $this->idLiquidacion = $idLiquidacion;

        return $this;
    }

    /**
     * Get idLiquidacion
     *
     * @return integer 
     */
    public function getIdLiquidacion() {
        return $this->idLiquidacion;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Liquidacion $liquidacion
     */
    public function setLiquidacion($liquidacion) {

        if (null != $liquidacion) {
            $this->idLiquidacion = $liquidacion->getId();
        } else {
            $this->idLiquidacion = null;
        }

        $this->liquidacion = $liquidacion;
    }

    /**
     * 
     * @return type
     */
    public function getLiquidacion() {
        return $this->liquidacion;
    }

}
