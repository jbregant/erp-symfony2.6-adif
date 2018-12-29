<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Darío Rapetti
 * created 26/02/2015
 * 
 * @ORM\Table(name="punto_venta_clase_contrato")
* @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\PuntoVentaClaseContratoRepository")
 */
class PuntoVentaClaseContrato extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var PuntoVenta
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\PuntoVenta", inversedBy="puntosVentaClaseContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_punto_venta", referencedColumnName="id", nullable=false)
     * })
     */
    protected $puntoVenta;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ClaseContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_clase_contrato", referencedColumnName="id", nullable=false)
     * })
     */
    protected $claseContrato;

    /**
     * @var double
     * @ORM\Column(name="monto_minimo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoMinimo;

    /**
     * @var double
     * @ORM\Column(name="monto_maximo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoMaximo;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set montoMinimo
     *
     * @param string $montoMinimo
     * @return PuntoVentaClaseContrato
     */
    public function setMontoMinimo($montoMinimo) {
        $this->montoMinimo = $montoMinimo;

        return $this;
    }

    /**
     * Get montoMinimo
     *
     * @return string 
     */
    public function getMontoMinimo() {
        return $this->montoMinimo;
    }

    /**
     * Set montoMaximo
     *
     * @param string $montoMaximo
     * @return PuntoVentaClaseContrato
     */
    public function setMontoMaximo($montoMaximo) {
        $this->montoMaximo = $montoMaximo;

        return $this;
    }

    /**
     * Get montoMaximo
     *
     * @return string 
     */
    public function getMontoMaximo() {
        return $this->montoMaximo;
    }

    /**
     * Set puntoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta $puntoVenta
     * @return PuntoVentaClaseContrato
     */
    public function setPuntoVenta(\ADIF\ContableBundle\Entity\Facturacion\PuntoVenta $puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

    /**
     * Set claseContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ClaseContrato $claseContrato
     * @return PuntoVentaClaseContrato
     */
    public function setClaseContrato(\ADIF\ContableBundle\Entity\Facturacion\ClaseContrato $claseContrato) {
        $this->claseContrato = $claseContrato;

        return $this;
    }

    /**
     * Get claseContrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ClaseContrato 
     */
    public function getClaseContrato() {
        return $this->claseContrato;
    }

}
