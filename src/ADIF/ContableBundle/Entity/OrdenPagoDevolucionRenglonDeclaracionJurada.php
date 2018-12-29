<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoDevolucionRenglonDeclaracionJurada
 *
 * @author Darío Rapetti
 * created 13/11/2015
 * 
 * @ORM\Table(name="orden_pago_devolucion_renglon_declaracion_jurada")
 * @ORM\Entity
 */
class OrdenPagoDevolucionRenglonDeclaracionJurada extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada
     *
     * @ORM\OneToOne(targetEntity="DevolucionRenglonDeclaracionJurada", inversedBy="ordenPago", cascade={"all"})
     * @ORM\JoinColumn(name="id_devolucion_renglon_declaracion_jurada", referencedColumnName="id", nullable=false)
     * 
     */
    protected $devolucionRenglonDeclaracionJurada;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set devolucionRenglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devolucionRenglonDeclaracionJurada
     * @return OrdenPagoDevolucionRenglonDeclaracionJurada
     */
    public function setDevolucionRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devolucionRenglonDeclaracionJurada = null) {
        $this->devolucionRenglonDeclaracionJurada = $devolucionRenglonDeclaracionJurada;

        return $this;
    }

    /**
     * Get devolucionRenglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada
     */
    public function getDevolucionRenglonDeclaracionJurada() {
        return $this->devolucionRenglonDeclaracionJurada;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoDevolucionRenglonDeclaracionJurada
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string 
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagodevolucionrenglondeclaracionjurada';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontabledevolucionrenglondeclaracionjurada';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getNombreBeneficiario();
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getCUITBeneficiario();
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
        return $this->getImporte();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getBeneficiario();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoDevolucionRenglonDeclaracionJuradaController();
    }
}
