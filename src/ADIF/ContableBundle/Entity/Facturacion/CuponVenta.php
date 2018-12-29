<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Facturacion\IConciliableCreditoVenta;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CuponVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="cupon_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuponVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "cupon_venta_plazo" = "CuponVentaPlazo",
 *      "cupon_venta_general" = "CuponVentaGeneral",
 *      "cupon_pliego" = "CuponPliego"
 * })
 */
class CuponVenta extends ComprobanteVenta implements BaseAuditable, IConciliableCreditoVenta {

    /**
     * @var string
     *
     * @ORM\Column(name="numero_cupon", type="string", length=25, nullable=false)
     */
    protected $numeroCupon;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_cupon_garantia", type="boolean", nullable=false)
     */
    protected $esCuponGarantia;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia", mappedBy="cuponGarantia", cascade={"all"})
     */
    protected $devolucionesGarantia;
    
     /**
     * Estos tipos de cupones si es true, son los que se van a transferir a la AABE
     * @var boolean
     *
     * @ORM\Column(name="es_migracion_aabe", type="boolean", nullable=false)
     */
    protected $esMigracionAabe;
    
     /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\CobroCuponCredito", mappedBy="cuponesCreditoVenta")
     * */
    protected $cobrosCuponesCredito;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esCuponGarantia = false;
        $this->devolucionesGarantia = new ArrayCollection();
        $this->esMigracionAabe = false;
        $this->cobrosCuponesCredito = new ArrayCollection();
    }

    /**
     * Set numeroCupon
     *
     * @param string $numeroCupon
     * @return CuponVenta
     */
    public function setNumeroCupon($numeroCupon) {
        $this->numeroCupon = $numeroCupon;

        return $this;
    }

    /**
     * Get numeroCupon
     *
     * @return string 
     */
    public function getNumeroCupon() {
        return $this->numeroCupon;
    }

    /**
     * Get numeroCompleto
     *
     * @return string 
     */
    public function getNumeroCompleto() {
        return $this->numeroCupon;
    }

    /**
     * Set esCuponGarantia
     *
     * @param boolean $esCuponGarantia
     * @return CuponVenta
     */
    public function setEsCuponGarantia($esCuponGarantia) {
        $this->esCuponGarantia = $esCuponGarantia;

        return $this;
    }

    /**
     * Get esCuponGarantia
     *
     * @return boolean 
     */
    public function getEsCuponGarantia() {
        return $this->esCuponGarantia;
    }

    /**
     * Add devolucionesGarantia
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia $devolucionesGarantia
     * @return CuponVenta
     */
    public function addDevolucionesGarantium(\ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia $devolucionesGarantia) {
        $this->devolucionesGarantia[] = $devolucionesGarantia;

        return $this;
    }

    /**
     * Remove devolucionesGarantia
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia $devolucionesGarantia
     */
    public function removeDevolucionesGarantium(\ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia $devolucionesGarantia) {
        $this->devolucionesGarantia->removeElement($devolucionesGarantia);
    }

    /**
     * Get devolucionesGarantia
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevolucionesGarantia() {
        return $this->devolucionesGarantia;
    }

    /**
     * Get esCupon
     *
     * @return boolean 
     */
    public function getEsCupon() {
        return true;
    }

    /**
     * Get esRendicionLiquidoProducto
     *
     * @return boolean 
     */
    public function getEsRendicionLiquidoProducto() {
        return false;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getCodigoBarrasNacion() {
        return ($this->getCodigoBarras() == null ? $this->generarCodigoBarras() : $this->getCodigoBarras()); 
    }

    /**
     * 
     * @return type
     */
    public function getTextoParaAsiento() {
        return 'CP N° ' . $this->getNumeroCompleto()  . ($this->getContrato() != null ? ' - C N° ' . $this->getContrato()->getNumeroContrato() : '');
    }

    /**
     * 
     * @return type
     */
    public function getSaldoCuponGarantia() {

        $saldo = $this->total;

        foreach ($this->devolucionesGarantia as $devolucionGarantia) {

            $saldo -= $devolucionGarantia->getImporte();
        }

        return $saldo;
    }
    
    public function getEsCuponVentaPlazo() {
        return false;
    }   

    public function generarCodigoBarras() {    
        // 4 digitos cliente ADIF
        $idClienteAdif = '4687';
        
        $numeroContrato = $this->getContrato()->getNumeroContrato();
        
        $primera_letra = strtoupper(substr($numeroContrato, 0, 1));
        
        $segunda_letra = strtoupper(substr($numeroContrato, 1, 1));
        
        $numero = substr($numeroContrato, 2, 10);
        
        //                2 digitos 1º letra    2 digitos 2º letra    10 digitos numero contrato
        $codigoContrato = ord($primera_letra) . ord($segunda_letra) . str_pad($numero, 10, "0", STR_PAD_LEFT);
        
        // 6 digitos fecha vencimiento
        if ($this->getFechaVencimiento() != null) {
			$vencimientoCupon = $this->getFechaVencimiento()->format('dmY');
			return $idClienteAdif . $codigoContrato . $vencimientoCupon;    
		} else {
			$vencimiento_contrato = $this->getContrato()->getFechaFin()->format('mY');
			return $idClienteAdif . $codigoContrato . '00' . $vencimiento_contrato;   	
		}
		
    }
    
    public function setEsMigracionAabe($esMigracionAabe)
    {
        $this->esMigracionAabe = $esMigracionAabe;
        
        return $this;
    }
    
    public function getEsMigracionAabe()
    {
        return $this->esMigracionAabe;
    }
    
    public function getCobrosCuponesCredito()
    {
        return $this->cobrosCuponesCredito;
    }
    
    public function addCobroCuponCredito($cupon)
    {
        $this->cobrosCuponesCredito->add($cupon);
        
        return $this;
    }
    
    public function removeCobroCuponCredito($cupon)
    {
        $this->cobrosCuponesCredito->removeElement($cupon);
        
        return $this;
    }
}
