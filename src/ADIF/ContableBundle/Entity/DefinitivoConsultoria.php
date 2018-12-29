<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DefinitivoConsultoria
 * 
 * @ORM\Table(name="definitivo_consultoria")
 * @ORM\Entity
 */
class DefinitivoConsultoria extends Definitivo {

    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id")
     * */
    protected $contrato;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato
     * @return DefinitivoConsultoria
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato = null) {
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

}
