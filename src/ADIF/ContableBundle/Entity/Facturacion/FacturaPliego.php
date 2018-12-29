<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaPliego
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="factura_pliego")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 */
class FacturaPliego extends FacturaVenta implements BaseAuditable {

    /**
     * @var \ADIF\ContableBundle\Entity\Licitacion
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Licitacion")
     * @ORM\JoinColumn(name="id_licitacion", referencedColumnName="id", nullable=false)
     */
    protected $licitacion;

    /**
     * Set licitacion
     *
     * @param \ADIF\ContableBundle\Entity\Obras\Licitacion $licitacion
     * @return FacturaPliego
     */
    public function setLicitacion(\ADIF\ContableBundle\Entity\Licitacion $licitacion) {
        $this->licitacion = $licitacion;

        return $this;
    }

    /**
     * Get licitacion
     *
     * @return \ADIF\ContableBundle\Entity\Licitacion
     */
    public function getLicitacion() {
        return $this->licitacion;
    }

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {
        return $this->getFechaComprobante();
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {
        return $this->getFechaComprobante();
    }

    /**
     * 
     * @return type
     */
    public function getImpTotConc() { // NETO NO GRAVADO
        return $this->getImporteNetoNoGravado();
    }

    /**
     * 
     * @return type
     */
    public function getImpOpEx() { //IMPORTE EXENTO
        return 0;
    }

    /**
     * 
     * @return type
     */
    public function getCodigoClaseContrato() {

        return ConstanteClaseContrato::PLIEGO;
    }

}
