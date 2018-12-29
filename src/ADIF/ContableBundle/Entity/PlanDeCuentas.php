<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlanDeCuentas
 *
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 * @ORM\Table(name="plan_de_cuentas")
 * @ORM\Entity
 */
class PlanDeCuentas extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas
     * 
     * @ORM\OneToMany(targetEntity="SegmentoPlanDeCuentas", mappedBy="planDeCuentas", cascade={"persist", "remove"})
     * @ORM\OrderBy({"posicion" = "ASC"})
     */
    protected $segmentos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->segmentos = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Add segmentos
     *
     * @param \ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas $segmento
     * @return PlanDeCuentas
     */
    public function addSegmento(\ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas $segmento) {

        $segmento->setPlanDeCuentas($this);

        $this->segmentos[] = $segmento;

        return $this;
    }

    /**
     * Remove segmentos
     *
     * @param \ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas $segmento
     */
    public function removeSegmento(\ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas $segmento) {
        $this->segmentos->removeElement($segmento);

        $segmento->setPlanDeCuentas(null);
    }

    /**
     * Get segmentos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSegmentos() {
        return $this->segmentos;
    }

}
