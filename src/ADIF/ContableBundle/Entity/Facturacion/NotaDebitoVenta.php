<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebitoVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="nota_debito_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "nota_debito_venta" = "NotaDebitoVenta",
 *      "nota_debito_venta_general" = "NotaDebitoVentaGeneral",
 *      "nota_debito_pliego" = "NotaDebitoPliego",
 *      "nota_debito_interes" = "NotaDebitoInteres"
 * })
 */
class NotaDebitoVenta extends ComprobanteVenta implements BaseAuditable {

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
     * @return NotaDebitoVenta
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

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {
        return $this->getContrato()->getFechaInicio();
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {
        return $this->getContrato()->getFechaFin();
    }

    /**
     * 
     * @return type
     */
    public function getTextoParaAsiento() {

        return 'ND ' . $this->getLetraComprobante()
                . ' N° ' . $this->getNumeroCompleto()
                . ($this->getContrato() != null ? ' - C N° ' . $this->getContrato()->getNumeroContrato() : '');
    }

}
