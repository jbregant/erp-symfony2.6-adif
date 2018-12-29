<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoAnticipoSueldo
 *
 * @author Darío Rapetti
 * created 11/04/2015
 * 
 * @ORM\Table(name="orden_pago_anticipo_sueldo")
 * @ORM\Entity
 */
class OrdenPagoAnticipoSueldo extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\AnticipoSueldo
     *
     * @ORM\OneToOne(targetEntity="AnticipoSueldo", cascade={"all"}, inversedBy="ordenPago")
     * @ORM\JoinColumn(name="id_anticipo", referencedColumnName="id", nullable=false)
     * 
     */
    protected $anticipoSueldo;

    /**
     * Set anticipoSueldo
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoSueldo $anticipoSueldo
     * @return OrdenPagoAnticipoSueldo
     */
    public function setAnticipoSueldo(\ADIF\ContableBundle\Entity\AnticipoSueldo $anticipoSueldo = null) {
        $this->anticipoSueldo = $anticipoSueldo;

        return $this;
    }

    /**
     * Get anticipoSueldo
     *
     * @return \ADIF\ContableBundle\Entity\AnticipoSueldo 
     */
    public function getAnticipoSueldo() {
        return $this->anticipoSueldo;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagoanticiposueldo';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontableanticiposueldo';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->getEmpleado();
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->getEmpleado()->getNroDocumento();
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return new ArrayCollection();
    }

    /**
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {
        return $this->getAnticipoSueldo()->getMonto();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->getEmpleado();
    }

    /**
     * Get empleado
     * 
     * @return type
     */
    public function getEmpleado() {
        return $this->anticipoSueldo->getEmpleado();
    }
    
    /**
     * 
     * @return EjecutadoEgresoValor
     */
    public function getEjecutadoEntity() {
        return new EjecutadoAnticipoSueldo();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoAnticipoSueldoController();
    }
}
