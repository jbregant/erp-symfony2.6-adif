<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoReconocimientoEgresoValor
 * 
 * @ORM\Table(name="orden_pago_reconocimiento_egreso_valor")
 * @ORM\Entity
 */
class OrdenPagoReconocimientoEgresoValor extends OrdenPago {

    /**
     * @ORM\ManyToOne(targetEntity="ReconocimientoEgresoValor", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_reconocimiento", referencedColumnName="id", nullable=false)
     */
    protected $reconocimientoEgresoValor;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoReconocimientoEgresoValor
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
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {
        return $this->getImporte();
    }

    /**
     * Set reconocimientoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientoEgresoValor
     * @return OrdenPagoReconocimientoEgresoValor
     */
    public function setReconocimientoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientoEgresoValor) {
        $this->reconocimientoEgresoValor = $reconocimientoEgresoValor;

        return $this;
    }

    /**
     * Get reconocimientoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor 
     */
    public function getReconocimientoEgresoValor() {
        return $this->reconocimientoEgresoValor;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagoreconocimiento';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablereconocimiento';
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
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->reconocimientoEgresoValor
                        ->getEgresoValor()->getResponsableEgresoValor();
    }

    /**
     * Get egresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor 
     */
    public function getEgresoValor() {
        return $this->reconocimientoEgresoValor->getEgresoValor();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\EgresoValor\OrdenPagoReconocimientoController();
    }
}
