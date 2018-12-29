<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoDeclaracionJurada
 *
 * @author Darío Rapetti
 * created 23/04/2015
 * 
 * @ORM\Table(name="orden_pago_declaracion_jurada")
 * @ORM\Entity
 */
class OrdenPagoDeclaracionJurada extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto
     *
     * @ORM\OneToOne(targetEntity="DeclaracionJuradaImpuesto", inversedBy="ordenPago", cascade={"all"})
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
    protected $declaracionJurada;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set declaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto $declaracionJurada
     * @return OrdenPagoDeclaracionJurada
     */
    public function setDeclaracionJurada(\ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto $declaracionJurada = null) {
        $this->declaracionJurada = $declaracionJurada;

        return $this;
    }

    /**
     * Get declaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto
     */
    public function getDeclaracionJurada() {
        return $this->declaracionJurada;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoDeclaracionJuradaImpuesto
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
        return 'ordenpagodeclaracionjurada';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontabledeclaracionjurada';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return "ADIF";
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return "30-71069599-3";
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
        return new AdifDatos();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoDeclaracionJuradaController();
    }
}
