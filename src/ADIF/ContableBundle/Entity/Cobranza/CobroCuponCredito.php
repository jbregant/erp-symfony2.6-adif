<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Facturacion\IConciliableCreditoVenta;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CobroNotaCredito
 *
 * @author Gustavo Luis
 * created 02/10/2017
 * 
 * @ORM\Table(name="cobro_cupon_credito")
 * @ORM\Entity
 */
class CobroCuponCredito extends Cobro 
{    
    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Facturacion\CuponVenta", inversedBy="cobrosCuponesCredito")
     * @ORM\JoinTable(name="cobro_cupon_credito_cupon_venta",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_cupon_credito", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_cupon_venta", referencedColumnName="id")}
     *      )
     */
    protected $cuponesCreditoVenta;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->cuponesCreditoVenta = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add notasCreditoVenta
     *
     * @param IConciliableCreditoVenta $notasCreditoVenta
     * @return CobroNotaCreditoVenta
     */
    public function addCuponesCreditoVenta(IConciliableCreditoVenta $cupones)
    {    
        $this->cuponesCreditoVenta[] = $cupones;
        
        return $this;
    }

    /**
     * Remove notasCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta $notasCreditoVenta
     */
    public function removeCuponesCreditoVenta(IConciliableCreditoVenta $cupones)
    {
        $this->cuponesCreditoVenta->removeElement($cupones);
    }

    /**
     * Get notasCreditoVenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuponesCreditoVenta()
    {
        return $this->cuponesCreditoVenta;
    }
    
    public function desimputar() {
        $cupon = $this->getCuponesCreditoVenta()[0];
        $cupon->setSaldo($cupon->getSaldo() - $this->getMonto());
        return ''; //no genera asientos contables (sólo modifica cuenta corriente del cliente
    }   
    
    public function tipo () {
        return 'CobroCuponCredito';
    }    
    
    public function __toString() {
        $notaCreditoVenta = $this->getCuponesCreditoVenta()[0];
        $letra = $notaCreditoVenta->getLetraComprobante();
        //$cantidad = sizeOf($notaCreditoVenta->getCobrosNotaCreditoVenta()) - 1;
        //$usos = ($cantidad == 0 ? 'sin usos adicionales' : 'con otros '. $cantidad . ' usos');        
        return 'Cancelación de saldo a trav&eacute;s de cup&oacute;n de cr&eacute;dito' . ($letra ? ' ('. $letra .') ' : ' ') . 'N° ' . $notaCreditoVenta->getNumeroCompleto();       
        
    }
    
    public function getStringReferencias() {
        $notaCreditoVenta = $this->getCuponesCreditoVenta()[0];
        return $notaCreditoVenta->getNumeroCompleto();
    }
    
    public function getStringTipo() {
        $notaCreditoVenta = $this->getCuponesCreditoVenta()[0];
        $letra = $notaCreditoVenta->getLetraComprobante();        
        return 'Cup&oacute;n de cr&eacute;dito' . ($letra ? ' ('. $letra .')' : '');
    }    
    
    
    public function getNumeroRecibo() {      
        return $this->getStringReferencias();
    }   
    
    public function getFechaCobranza() {
        return $this->getFecha(); //$this->getNotasCreditoVenta()->first()->getFechaComprobante();
    }  
    
    public function getFechaContable() {
        return $this->getCuponesCreditoVenta()[0]->getFechaContable();
    }    
    
    function tieneCobranzaConReferencia($referencia) {
        return (strpos($this->getStringReferencias(), $referencia) !== false);
    }        
}
