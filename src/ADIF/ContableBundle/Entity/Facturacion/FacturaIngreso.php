<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaIngreso
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="factura_ingreso")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "factura_alquiler" = "FacturaAlquiler",
 *      "factura_chatarra" = "FacturaChatarra"
 * })
 * )
 */
class FacturaIngreso extends FacturaVenta implements BaseAuditable {

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {

        if ($this->contrato != null && $this->contrato->getEsContratoServidumbreDePaso()) {
            return $this->fechaComprobante;
        } else {
            return $this->fechaInicioServicio;
        }
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {

        if ($this->contrato != null && $this->contrato->getEsContratoServidumbreDePaso()) {
            return $this->fechaComprobante;
        } else {
            return $this->fechaFinServicio;
        }
    }

}
