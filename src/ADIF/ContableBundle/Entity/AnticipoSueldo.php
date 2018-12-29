<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnticipoSueldo
 * 
 * @author Darío Rapetti
 * created 21/10/2014
 *
 * @ORM\Table(name="anticipo_sueldo")
 * @ORM\Entity
 */
class AnticipoSueldo extends Anticipo {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoAnticipoSueldo", mappedBy="anticipoSueldo")
     * */
    protected $ordenPago;

    /**
     * @ORM\Column(name="id_empleado", type="integer", nullable=false)
     */
    protected $idEmpleado;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Empleado
     */
    protected $empleado;

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo $ordenPago
     * @return AnticipoSueldo
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set idEmpleado
     *
     * @param integer $idEmpleado
     * @return AnticipoSueldo
     */
    public function setIdEmpleado($idEmpleado) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return integer 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     */
    public function setEmpleado($empleado) {

        if (null != $empleado) {
            $this->idEmpleado = $empleado->getId();
        } else {
            $this->idEmpleado = null;
        }

        $this->empleado = $empleado;
    }

    /**
     * 
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado
     */
    public function getEmpleado() {
        return $this->empleado;
    }

}
