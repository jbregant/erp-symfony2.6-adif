<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaAlquiler
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="factura_alquiler")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 */
class FacturaAlquiler extends FacturaIngreso implements BaseAuditable {

    /**
     * @var CicloFacturacion
     *
     * @ORM\ManyToOne(targetEntity="CicloFacturacion")
     * @ORM\JoinColumn(name="id_ciclo_facturacion", referencedColumnName="id", nullable=false)
     */
    protected $cicloFacturacion;

    /**
     * Set cicloFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion
     * @return FacturaAlquiler
     */
    public function setCicloFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion) {
        $this->cicloFacturacion = $cicloFacturacion;

        return $this;
    }

    /**
     * Get cicloFacturacion
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion 
     */
    public function getCicloFacturacion() {
        return $this->cicloFacturacion;
    }

}
