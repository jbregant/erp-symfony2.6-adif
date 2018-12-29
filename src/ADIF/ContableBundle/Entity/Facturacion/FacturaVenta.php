<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="factura_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "factura_venta" = "FacturaVenta",
 *      "factura_pliego" = "FacturaPliego",
 *      "factura_venta_general" = "FacturaVentaGeneral",
 *      "factura_ingreso" = "FacturaIngreso",
 *      "factura_alquiler" = "FacturaAlquiler",
 *      "factura_chatarra" = "FacturaChatarra"
 * })
 */
class FacturaVenta extends ComprobanteVenta implements BaseAuditable {

    /**
     * @var \string
     *
     * @ORM\Column(name="cae_numero", type="string", length=14, nullable=true)
     */
    protected $caeNumero;

    /**
     * @var \Date
     *
     * @ORM\Column(name="cae_vencimiento", type="date", nullable=true)
     */
    protected $caeVencimiento;

    /**
     * Set caeNumero
     *
     * @param string $caeNumero
     * @return FacturaVenta
     */
    public function setCaeNumero($caeNumero) {
        $this->caeNumero = $caeNumero;

        return $this;
    }

    /**
     * Get caeNumero
     *
     * @return string 
     */
    public function getCaeNumero() {
        return $this->caeNumero;
    }

    /**
     * Set caeVencimiento
     *
     * @param \DateTime $caeVencimiento
     * @return FacturaVenta
     */
    public function setCaeVencimiento($caeVencimiento) {
        $this->caeVencimiento = $caeVencimiento;

        return $this;
    }

    /**
     * Get caeVencimiento
     *
     * @return \DateTime 
     */
    public function getCaeVencimiento() {
        return $this->caeVencimiento;
    }

    public function getTextoParaAsiento() {
        return 'F ' . $this->getLetraComprobante() . ' N° ' . $this->getNumeroCompleto() . ($this->getContrato() ? ' - C N° ' . $this->getContrato()->getNumeroContrato():'');
    }

}
