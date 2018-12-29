<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Devengado;

/**
 * DevengadoObra
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="devengado_obra")
 * @ORM\Entity
 */
class DevengadoObra extends Devengado {

    /**
     * @ORM\OneToOne(targetEntity="ComprobanteObra")
     * @ORM\JoinColumn(name="id_comprobante_obra", referencedColumnName="id")
     * */
    protected $comprobanteObra;

    /**
     * @var FuenteFinanciamiento
     *
     * @ORM\ManyToOne(targetEntity="FuenteFinanciamiento")
     * @ORM\JoinColumn(name="id_fuente_financiamiento", referencedColumnName="id")
     * 
     */
    protected $fuenteFinanciamiento;

    /**
     * Set comprobanteObra
     *
     * @param \ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobanteObra
     * @return DevengadoObra
     */
    public function setComprobanteObra(\ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobanteObra = null) {
        $this->comprobanteObra = $comprobanteObra;

        return $this;
    }

    /**
     * Get comprobanteObra
     *
     * @return \ADIF\ContableBundle\Entity\Obras\ComprobanteObra 
     */
    public function getComprobanteObra() {
        return $this->comprobanteObra;
    }

    /**
     * Set fuenteFinanciamiento
     *
     * @param \ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento $fuenteFinanciamiento
     *
     * @return DevengadoObra
     */
    public function setFuenteFinanciamiento(\ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento $fuenteFinanciamiento) {
        $this->fuenteFinanciamiento = $fuenteFinanciamiento;

        return $this;
    }

    /**
     * Get fuenteFinanciamiento
     *
     * @return \ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento
     */
    public function getFuenteFinanciamiento() {
        return $this->fuenteFinanciamiento;
    }

}
