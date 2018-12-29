<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoConsultoria;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoConsultoria
 * 
 * @ORM\Table(name="orden_pago_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\OrdenPagoConsultoriaRepository")
 */
class OrdenPagoConsultoria extends OrdenPago {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=false)
     */
    protected $contrato;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\ComprobanteConsultoria
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria", mappedBy="ordenPago")
     */
    protected $comprobantes;

    /**
     * @var AnticipoContratoConsultoria Anticipos que descuentan a esta OP
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\AnticipoContratoConsultoria", mappedBy="ordenPagoCancelada")
     * 
     */
    protected $anticipos;
    
    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", nullable=true)
     */
    protected $periodo;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->comprobantes = new ArrayCollection();
        $this->anticipos = new ArrayCollection();
    }

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato
     * @return OrdenPagoConsultoria
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Add comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria $comprobantes
     * @return OrdenPagoConsultoria
     */
    public function addComprobante(\ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria $comprobantes) {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria $comprobantes
     */
    public function removeComprobante(\ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria $comprobantes) {
        $this->comprobantes->removeElement($comprobantes);
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return $this->comprobantes;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->contrato->getConsultor();
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->contrato->getConsultor()->getCUIT();
    }    

    /**
     * 
     * @return string
     */
    public function getPath() {

        return 'ordenpagoconsultoria';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {

        return 'autorizacioncontableconsultoria';
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\Obras\ComprobanteRetencionImpuestoConsultoria
     */
    public function getComprobanteRetencion($idProveedorUTE = null) {

        return new ComprobanteRetencionImpuestoConsultoria();
    }

    /**
     * Add anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos
     * @return OrdenPagoConsultoria
     */
    public function addAnticipo(\ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos) {
        $this->anticipos[] = $anticipos;

        return $this;
    }

    /**
     * Remove anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos
     */
    public function removeAnticipo(\ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos) {
        $this->anticipos->removeElement($anticipos);
    }

    /**
     * Get anticipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticipos() {
        return $this->anticipos;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->contrato->getConsultor();
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return OrdenPagoConsultoria
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    public function __toString() {
        return $this->numeroOrdenPago;
    }
    
    public function getController(){
        return new \ADIF\ContableBundle\Controller\Consultoria\OrdenPagoConsultoriaController();
    }
}
