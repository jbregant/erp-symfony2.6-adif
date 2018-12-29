<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegimenRetencionBienEconomico
 * 
 * @ORM\Table(name="regimen_retencion_bien_economico")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\RegimenRetencionBienEconomicoRepository")
 */
class RegimenRetencionBienEconomico extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="RegimenRetencion", inversedBy="regimenesRetencionBienEconomico")
     * 
     */
    protected $regimenRetencion;

    /**
     * @ORM\Column(name="id_bien_economico", type="integer", nullable=false)
     */
    protected $idBienEconomico;

    /**
     * @var ADIF\ComprasBundle\Entity\BienEconomico
     */
    protected $bienEconomico;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set idBienEconomico
     *
     * @param integer $idBienEconomico
     * @return RegimenRetencionBienEconomico
     */
    public function setIdBienEconomico($idBienEconomico) {
        $this->idBienEconomico = $idBienEconomico;

        return $this;
    }

    /**
     * Get idBienEconomico
     *
     * @return integer 
     */
    public function getIdBienEconomico() {
        return $this->idBienEconomico;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico
     */
    public function setBienEconomico($bienEconomico) {

        if (null != $bienEconomico) {
            $this->bienEconomico = $bienEconomico->getId();
        } //.
        else {
            $this->bienEconomico = null;
        }

        $this->bienEconomico = $bienEconomico;
    }

    /**
     * 
     * @return type
     */
    public function getBienEconomico() {
        return $this->bienEconomico;
    }

    /**
     * Set regimenRetencion
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion
     * @return RegimenRetencionBienEconomico
     */
    public function setRegimenRetencion(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion = null) {
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
