<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProvisorioSueldo
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="provisorio_sueldo")
 * @ORM\Entity
 */
class ProvisorioSueldo extends Provisorio {

    /**
     * @ORM\OneToMany(targetEntity="ProvisorioSueldoHistorico", mappedBy="provisorio")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $historicos;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esManual = true;
    }

    /**
     * Add historicos
     *
     * @param \ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico $historicos
     * @return ProvisorioSueldo
     */
    public function addHistorico(\ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico $historicos) {
        $this->historicos[] = $historicos;

        return $this;
    }

    /**
     * Remove historicos
     *
     * @param \ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico $historicos
     */
    public function removeHistorico(\ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico $historicos) {
        $this->historicos->removeElement($historicos);
    }

    /**
     * Get historicos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistoricos() {
        return $this->historicos;
    }

}
