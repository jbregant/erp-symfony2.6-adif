<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Ejecutado;

/**
 * EjecutadoObra
 * 
 * @ORM\Table(name="ejecutado_obra")
 * @ORM\Entity
 */
class EjecutadoObra extends Ejecutado {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoObra")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     */
    protected $ordenPagoObra;

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
     * Set ordenPagoObra
     *
     * @param \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra $ordenPagoObra
     * @return EjecutadoObra
     */
    public function setOrdenPagoObra(\ADIF\ContableBundle\Entity\Obras\OrdenPagoObra $ordenPagoObra = null) {
        $this->ordenPagoObra = $ordenPagoObra;

        return $this;
    }

    /**
     * Get ordenPagoObra
     *
     * @return \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra 
     */
    public function getOrdenPagoObra() {
        return $this->ordenPagoObra;
    }

    /**
     * Set comprobanteObra
     *
     * @param \ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobanteObra
     * @return EjecutadoObra
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
     * @return EjecutadoObra
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
