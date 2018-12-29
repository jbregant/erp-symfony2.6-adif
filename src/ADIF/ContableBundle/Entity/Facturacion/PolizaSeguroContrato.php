<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\ContableBundle\Entity\PolizaSeguro;
use Doctrine\ORM\Mapping as ORM;

/**
 * PolizaSeguroContrato
 *
 * @author Manuel Becerra
 * created 31/05/2015
 * 
 * @ORM\Table(name="poliza_seguro_contrato")
 * @ORM\Entity
 */
class PolizaSeguroContrato extends PolizaSeguro {

    /**
     * @var Contrato
     *
     * @ORM\ManyToOne(targetEntity="Contrato", inversedBy="polizasSeguro")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=false)
     */
    protected $contrato;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato
     * @return PolizaSeguro
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\Contrato 
     */
    public function getContrato() {
        return $this->contrato;
    }

}
