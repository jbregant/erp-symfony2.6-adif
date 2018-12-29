<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RegimenRetencionIVAParametrizacion
 *
 * @author Manuel Becerra
 * created 12/11/2014
 * 
 * @ORM\Table(name="regimen_retencion_iva_parametrizacion")
 * @ORM\Entity
 */
class RegimenRetencionIVAParametrizacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\AlicuotaIva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_alicuota_iva", referencedColumnName="id", nullable=false)
     * })
     */
    protected $alicuotaIva;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_regimen_retencion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $regimenRetencion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set alicuotaIva
     *
     * @param \ADIF\ContableBundle\Entity\AlicuotaIva $alicuotaIva
     * @return RegimenRetencionIVAParametrizacion
     */
    public function setAlicuotaIva(\ADIF\ContableBundle\Entity\AlicuotaIva $alicuotaIva) {
        $this->alicuotaIva = $alicuotaIva;

        return $this;
    }

    /**
     * Get alicuotaIva
     *
     * @return \ADIF\ContableBundle\Entity\AlicuotaIva 
     */
    public function getAlicuotaIva() {
        return $this->alicuotaIva;
    }

    /**
     * Set regimenRetencion
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion
     * @return RegimenRetencionIVAParametrizacion
     */
    public function setRegimenRetencion(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion) {
        $this->regimenRetencion = $regimenRetencion;

        return $this;
    }

    /**
     * Get regimenRetencion
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencion() {
        return $this->regimenRetencion;
    }

}
