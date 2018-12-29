<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RenglonDeclaracionJuradaRenglonPercepcion
 *
 * @ORM\Table(name="renglon_declaracion_jurada_renglon_percepcion")
 * @ORM\Entity
 */
class RenglonDeclaracionJuradaRenglonPercepcion extends RenglonDeclaracionJurada {

    /**
     * @ORM\OneToOne(targetEntity="RenglonPercepcion")
     * @ORM\JoinColumn(name="id_renglon_percepcion", referencedColumnName="id")
     * */
    private $renglonPercepcion;
    
    /**
     * @ORM\OneToOne(targetEntity="RegimenPercepcion")
     * @ORM\JoinColumn(name="id_regimen_percepcion", referencedColumnName="id")
     * */
    private $regimenPercepcion;

    /**
     * Set renglonPercepcion
     *
     * @param \ADIF\ContableBundle\Entity\RenglonPercepcion $renglonPercepcion
     * @return RenglonDeclaracionJuradaRenglonPercepcion
     */
    public function setRenglonPercepcion(\ADIF\ContableBundle\Entity\RenglonPercepcion $renglonPercepcion = null) {
        $this->renglonPercepcion = $renglonPercepcion;

        return $this;
    }

    /**
     * Get renglonPercepcion
     *
     * @return \ADIF\ContableBundle\Entity\RenglonPercepcion 
     */
    public function getRenglonPercepcion() {
        return $this->renglonPercepcion;
    }
    
    /**
     * Set regimenPercepcion
     *
     * @param \ADIF\ContableBundle\Entity\RegimenPercepcion $regimenPercepcion
     * @return RenglonDeclaracionJuradaRegimenPercepcion
     */
    public function setRegimenPercepcion(\ADIF\ContableBundle\Entity\RegimenPercepcion $regimenPercepcion = null) {
        $this->regimenPercepcion = $regimenPercepcion;

        return $this;
    }
    
    /**
     * Get regimenPercepcion
     *
     * @return \ADIF\ContableBundle\Entity\RegimenPercepcion 
     */
    public function getRegimenPercepcion() {
        return $this->regimenPercepcion;
    }
    
    public function getRegimen(){
        return $this->getRegimenPercepcion()->getDenominacion();
    }
    
    public function getNombreBeneficiario(){
        return $this->getRenglonPercepcion()->getComprobante()->getCliente()->getCuitAndRazonSocial();
    }
    
    public function getCUITBeneficiario(){
        return $this->getRenglonPercepcion()->getComprobante()->getCliente()->getCUIT();
    }
    
    public function getBeneficiario(){
        return $this->getRenglonPercepcion()->getComprobante()->getCliente();
    }

}
