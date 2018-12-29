<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RenglonDeclaracionJuradaComprobanteRetencionImpuesto
 *
 * @ORM\Table(name="renglon_declaracion_jurada_comprobante_retencion_impuesto")
 * @ORM\Entity
 */
class RenglonDeclaracionJuradaComprobanteRetencionImpuesto extends RenglonDeclaracionJurada {

    /**
     * @ORM\OneToOne(targetEntity="ComprobanteRetencionImpuesto")
     * @ORM\JoinColumn(name="id_comprobante_retencion_impuesto", referencedColumnName="id")
     * */
    private $comprobanteRetencionImpuesto;

    /**
     * Set comprobanteRetencionImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $comprobanteRetencionImpuesto
     * @return RenglonDeclaracionJuradaComprobanteRetencionImpuesto
     */
    public function setComprobanteRetencionImpuesto(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $comprobanteRetencionImpuesto = null) {
        $this->comprobanteRetencionImpuesto = $comprobanteRetencionImpuesto;

        return $this;
    }

    /**
     * Get comprobanteRetencionImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto 
     */
    public function getComprobanteRetencionImpuesto() {
        return $this->comprobanteRetencionImpuesto;
    }

    /**
     * 
     * @return type
     */
    public function getRegimen() {
        return $this->getComprobanteRetencionImpuesto()->getRegimenRetencion()->getDenominacion();
    }

    /**
     * 
     * @return type
     */
    public function getNombreBeneficiario() {

        if ($this->getComprobanteRetencionImpuesto()->getProveedor() != null) {
            return $this->getComprobanteRetencionImpuesto()->getProveedor()->getCuitAndRazonSocial();
        } else {
            return $this->getComprobanteRetencionImpuesto()->getOrdenPago()->getBeneficiario()->getCuitAndRazonSocial();
        }

        return null;
    }

    /**
     * 
     * @return type
     */
    public function getCUITBeneficiario() {
        if ($this->getComprobanteRetencionImpuesto()->getProveedor() != null) {
            return $this->getComprobanteRetencionImpuesto()->getProveedor()->getCUIT();
        } else {
            return $this->getComprobanteRetencionImpuesto()->getOrdenPago()->getBeneficiario()->getCUIT();
        }

        return null;
    }

    /**
     * 
     * @return type
     */
    public function getOrdenPago() {

        $ordenPago = null;

        if ($this->comprobanteRetencionImpuesto != null) {

            if ($this->comprobanteRetencionImpuesto->getOrdenPago() != null //
                    && !$this->comprobanteRetencionImpuesto->getOrdenPago()->getEsAutorizacionContable()) {

                $ordenPago = $this->comprobanteRetencionImpuesto->getOrdenPago();
            }
        }

        return $ordenPago;
    }
    
    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        if ($this->getComprobanteRetencionImpuesto()->getProveedor() != null) {
            return $this->getComprobanteRetencionImpuesto()->getProveedor();
        } else {
            return $this->getComprobanteRetencionImpuesto()->getOrdenPago()->getBeneficiario();
        }

        return null;
    }

}
