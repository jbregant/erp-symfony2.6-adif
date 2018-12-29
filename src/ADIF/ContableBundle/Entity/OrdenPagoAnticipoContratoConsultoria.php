<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoAnticipoConsultor
 *
 * @author Darío Rapetti
 * created 05/05/2015
 * 
 * @ORM\Table(name="orden_pago_anticipo_contrato_consultoria")
 * @ORM\Entity
 */
class OrdenPagoAnticipoContratoConsultoria extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria
     *
     * @ORM\OneToOne(targetEntity="AnticipoContratoConsultoria", cascade={"all"}, inversedBy="ordenPago")
     * @ORM\JoinColumn(name="id_anticipo", referencedColumnName="id", nullable=false)
     * 
     */
    protected $anticipoContratoConsultoria;

    /**
     * Set anticipoContratoConsultoria
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipoContratoConsultoria
     * @return OrdenPagoAnticipoContratoConsultoria
     */
    public function setAnticipoContratoConsultoria(\ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipoContratoConsultoria = null) {
        $this->anticipoContratoConsultoria = $anticipoContratoConsultoria;

        return $this;
    }

    /**
     * Get anticipoContratoConsultoria
     *
     * @return \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria
     */
    public function getAnticipoContratoConsultoria() {
        return $this->anticipoContratoConsultoria;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagoanticipocontratoconsultoria';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontableanticipocontratoconsultoria';
    }

    /**
     * 
     * @return type
     */
    public function getConsultor() {
        return $this->anticipoContratoConsultoria->getConsultor();
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
        return $this->getAnticipoContratoConsultoria()->getMonto();
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->anticipoContratoConsultoria->getConsultor();
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->anticipoContratoConsultoria->getConsultor()->getCUIT();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->anticipoContratoConsultoria->getConsultor();
    }

    /**
     * Get contrato
     *
     * @return ContratoConsultoria 
     */
    public function getContrato() {
        return $this->anticipoContratoConsultoria->getContrato();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\Consultoria\OrdenPagoAnticipoContratoConsultoriaController();
    }
}
