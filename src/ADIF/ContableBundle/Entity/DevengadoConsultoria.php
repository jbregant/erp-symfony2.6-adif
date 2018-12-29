<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevengadoConsultoria
 * @ORM\Table(name="devengado_consultoria")
 * @ORM\Entity
 */
class DevengadoConsultoria extends Devengado {

    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id")
     * */
    protected $contrato;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato
     * @return DevengadoConsultoria
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
