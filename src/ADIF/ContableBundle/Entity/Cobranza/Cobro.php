<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
//use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;

/**
 * Description of Cobro
 *
 * @author Augusto Villa Monte
 * created 13/04/2015
 * 
 * @ORM\Table(name="cobro")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "cobro_anticipo_cliente" = "CobroAnticipoCliente",
 *      "cobro_renglon_cobranza" = "CobroRenglonCobranza",
 *      "cobro_nota_credito_venta" = "CobroNotaCreditoVenta",
 *      "cobro_retencion_cliente" = "CobroRetencionCliente",
 *      "cobro_cupon_credito" = "CobroCuponCredito"
 * })
 * )
 */
abstract class Cobro extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $monto; 

    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta", mappedBy="cobros")
     * */
    protected $comprobantes;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return RenglonCobranza
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RenglonCobranza
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comprobantes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobantes
     * @return Cobro
     */
    public function addComprobante(\ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobantes)
    {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobantes
     */
    public function removeComprobante(\ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobantes)
    {
        $this->comprobantes->removeElement($comprobantes);
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes()
    {
        return $this->comprobantes;
    }
    
    public function getStringReferencias() {
        return '';
    }
    
    public function getStringTipo() {
        return '';
    }
    
    public function getEsCobroRenglonCobranza() {
        return false;
    }
    
    public function getEsCobroRetencionCliente() {
        return false;
    }
    
    public function getFechaContable() {
        return $this->getFecha();
    }
    
}
