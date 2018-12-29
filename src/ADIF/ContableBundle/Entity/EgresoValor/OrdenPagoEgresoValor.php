<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\ContableBundle\Entity\AdifDatos;
use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of OrdenPagoEgresoValor
 *
 * @author Manuel Becerra
 * created 05/11/2014
 * 
 * @ORM\Table(name="orden_pago_egreso_valor")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\OrdenPagoEgresoValorRepository")
 */
class OrdenPagoEgresoValor extends OrdenPago {

    /**
     * @ORM\ManyToOne(targetEntity="EgresoValor", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_egreso_valor", referencedColumnName="id", nullable=false)
     */
    protected $egresoValor;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * @ORM\OneToOne(targetEntity="ReposicionEgresoValor", cascade={"all"})
     * @ORM\JoinColumn(name="id_reposicion", referencedColumnName="id", nullable=true)
     */
    protected $reposicionEgresoValor;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();
    }

    /**
     * Set egresoValor
     *
     * @param EgresoValor $egresoValor
     * @return OrdenPagoEgresoValor
     */
    public function setEgresoValor(EgresoValor $egresoValor) {
        $this->egresoValor = $egresoValor;

        return $this;
    }

    /**
     * Get egresoValor
     *
     * @return EgresoValor 
     */
    public function getEgresoValor() {
        return $this->egresoValor;
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
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoEgresoValor
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
     * Set reposicionEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor
     * @return OrdenPagoEgresoValor
     */
    public function setReposicionEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor = null) {
        $this->reposicionEgresoValor = $reposicionEgresoValor;

        return $this;
    }

    /**
     * Get reposicionEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor 
     */
    public function getReposicionEgresoValor() {
        return $this->reposicionEgresoValor;
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
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagoegresovalor';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontableegresovalor';
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return new AdifDatos();
    }

    /**
     * 
     * @return EjecutadoEgresoValor
     */
    public function getEjecutadoEntity() {
        $ejecutado = new EjecutadoEgresoValor();
        return $ejecutado->setOrdenPagoEgresoValor($this);
    }
    
    public function getController(){
        return new \ADIF\ContableBundle\Controller\EgresoValor\OrdenPagoEgresoValorController();
    }

}
