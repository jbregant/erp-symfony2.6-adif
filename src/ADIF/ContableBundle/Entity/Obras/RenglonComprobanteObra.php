<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\RenglonComprobante;

/**
 * RenglonComprobanteObra
 *
 * @author Manuel Becerra
 * created 26/12/2014
 * 
 * @ORM\Table(name="renglon_comprobante_obra")
 * @ORM\Entity 
 */
class RenglonComprobanteObra extends RenglonComprobante {

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_suss", referencedColumnName="id", nullable=true)
     * 
     */
    protected $regimenRetencionSUSS;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iva", referencedColumnName="id", nullable=true)
     * 
     */
    protected $regimenRetencionIVA;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iibb", referencedColumnName="id", nullable=true)
     * 
     */
    protected $regimenRetencionIIBB;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_ganancias", referencedColumnName="id", nullable=true)
     * 
     */
    protected $regimenRetencionGanancias;

    /**
     * Set regimenRetencionSUSS
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionSUSS
     * @return RenglonComprobanteObra
     */
    public function setRegimenRetencionSUSS(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionSUSS) {
        $this->regimenRetencionSUSS = $regimenRetencionSUSS;

        return $this;
    }

    /**
     * Get regimenRetencionSUSS
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionSUSS() {
        return $this->regimenRetencionSUSS;
    }

    /**
     * Set regimenRetencionIVA
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIVA
     * @return RenglonComprobanteObra
     */
    public function setRegimenRetencionIVA(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIVA) {
        $this->regimenRetencionIVA = $regimenRetencionIVA;

        return $this;
    }

    /**
     * Get regimenRetencionIVA
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionIVA() {
        return $this->regimenRetencionIVA;
    }

    /**
     * Set regimenRetencionIIBB
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIIBB
     * @return RenglonComprobanteObra
     */
    public function setRegimenRetencionIIBB(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIIBB) {
        $this->regimenRetencionIIBB = $regimenRetencionIIBB;

        return $this;
    }

    /**
     * Get regimenRetencionIIBB
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionIIBB() {
        return $this->regimenRetencionIIBB;
    }

    /**
     * Set regimenRetencionGanancias
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionGanancias
     * @return RenglonComprobanteObra
     */
    public function setRegimenRetencionGanancias(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionGanancias) {
        $this->regimenRetencionGanancias = $regimenRetencionGanancias;

        return $this;
    }

    /**
     * Get regimenRetencionGanancias
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionGanancias() {
        return $this->regimenRetencionGanancias;
    }

    /**
     * Get tipoDocumentoFinanciero
     *
     * @return \ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero 
     */
    public function getTipoDocumentoFinanciero() {
        return $this->comprobante->getDocumentoFinanciero()->getTipoDocumentoFinanciero();
    }

}
