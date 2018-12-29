<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContratoChatarra
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato_chatarra")
 * @ORM\Entity
 */
class ContratoChatarra extends ContratoVenta implements BaseAuditable {

    /**
     * @var \ADIF\ContableBundle\Entity\LicitacionChatarra
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\LicitacionChatarra", cascade={"persist"})
     * @ORM\JoinColumn(name="id_licitacion", referencedColumnName="id", nullable=false)
     */
    protected $licitacion;

    /**
     * Set licitacion
     *
     * @param \ADIF\ContableBundle\Entity\LicitacionChatarra $licitacion
     * @return ContratoChatarra
     */
    public function setLicitacion(\ADIF\ContableBundle\Entity\LicitacionChatarra $licitacion) {
        $this->licitacion = $licitacion;

        return $this;
    }

    /**
     * Get licitacion
     *
     * @return \ADIF\ContableBundle\Entity\LicitacionChatarra 
     */
    public function getLicitacion() {
        return $this->licitacion;
    }

    /**
     * Get esEditable
     *
     * @return boolean
     */
    public function getEsEditableTotalidad() {
        return true;
    }
    
    /**
     * 
     * @return type
     */
    public function getImpOpEx($comprobante) {
        return 0;
    }

}
