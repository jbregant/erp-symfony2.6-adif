<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\ContableBundle\Entity\PolizaSeguro;
use Doctrine\ORM\Mapping as ORM;

/**
 * PolizaSeguroDocumentoFinanciero
 *
 * @author Manuel Becerra
 * created 25/09/2015
 * 
 * @ORM\Table(name="poliza_seguro_documento_financiero")
 * @ORM\Entity
 */
class PolizaSeguroDocumentoFinanciero extends PolizaSeguro {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero", inversedBy="polizasSeguro")
     * @ORM\JoinColumn(name="id_documento_financiero", referencedColumnName="id", nullable=false)
     */
    protected $documentoFinanciero;

    /**
     * Set documentoFinanciero
     *
     * @param DocumentoFinanciero $documentoFinanciero
     * @return PolizaSeguroDocumentoFinanciero
     */
    public function setDocumentoFinanciero(DocumentoFinanciero $documentoFinanciero) {
        $this->documentoFinanciero = $documentoFinanciero;

        return $this;
    }

    /**
     * Get documentoFinanciero
     *
     * @return DocumentoFinanciero 
     */
    public function getDocumentoFinanciero() {
        return $this->documentoFinanciero;
    }

}
