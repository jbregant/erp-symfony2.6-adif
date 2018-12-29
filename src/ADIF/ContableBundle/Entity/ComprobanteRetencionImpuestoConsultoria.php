<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ComprobanteRetencionImpuestoConsultoria
 *
 * @author Darío Rapetti
 * created 15/04/2015
 * 
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteRetencionImpuestoConsultoriaRepository")
 */
class ComprobanteRetencionImpuestoConsultoria extends ComprobanteRetencionImpuesto {

    /**
     * 
     * @return type
     */
    public function getPath() {
        return 'comprobanteRetencionImpuestoConsultoria';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->ordenPago->getBeneficiario();
    }

    /**
     * 
     * @return string
     */
    public function getTipoComprobanteRetencion() {
        return 'CONSULTORIA';
    }

}
