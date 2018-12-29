<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Facturacion\IConciliableCreditoVenta;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CobroNotaCredito
 *
 * @author Augusto Villa Monte
 * created 13/04/2015
 * 
 * @ORM\Table(name="cobro_nota_credito")
 * @ORM\Entity
 */
class CobroNotaCreditoVenta extends Cobro {
    
    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta", inversedBy="cobrosNotaCreditoVenta")
     * @ORM\JoinTable(name="cobro_nota_credito_venta_nota_credito_venta",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_nota_credito_venta", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_nota_credito_venta", referencedColumnName="id")}
     *      )
     */
    protected $notasCreditoVenta; 
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->notasCreditoVenta = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add notasCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta $notasCreditoVenta
     * @return CobroNotaCreditoVenta
     */
    public function addNotasCreditoVenta(IConciliableCreditoVenta $notasCreditoVenta)
    {
        $this->notasCreditoVenta[] = $notasCreditoVenta;
       
        return $this;
    }

    /**
     * Remove notasCreditoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta $notasCreditoVenta
     */
    public function removeNotasCreditoVenta(IConciliableCreditoVenta $notasCreditoVenta)
    {
        $this->notasCreditoVenta->removeElement($notasCreditoVenta);
    }

    /**
     * Get notasCreditoVenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotasCreditoVenta()
    {
        return $this->notasCreditoVenta;
    }
    
    public function desimputar() {
        $notaDeCreditoVenta = $this->getNotasCreditoVenta()[0];
        $notaDeCreditoVenta->setSaldo($notaDeCreditoVenta->getSaldo()+$this->getMonto());
        return ''; //no genera asientos contables (sólo modifica cuenta corriente del cliente
    }   
    
    public function tipo () {
        return 'CobroNotaCreditoVenta';
    }    
    
    public function __toString() {
        $notaCreditoVenta = $this->getNotasCreditoVenta()[0];
        $letra = $notaCreditoVenta->getLetraComprobante();
        //$cantidad = sizeOf($notaCreditoVenta->getCobrosNotaCreditoVenta()) - 1;
        //$usos = ($cantidad == 0 ? 'sin usos adicionales' : 'con otros '. $cantidad . ' usos');        
        return 'Cancelación de saldo a trav&eacute;s de nota de cr&eacute;dito' . ($letra ? ' ('. $letra .') ' : ' ') . 'N° ' . $notaCreditoVenta->getNumeroCompleto();       
        
    }
    
    public function getStringReferencias() {
        $notaCreditoVenta = $this->getNotasCreditoVenta()[0];
        return $notaCreditoVenta->getNumeroCompleto();
    }
    
    public function getStringTipo() {
        $notaCreditoVenta = $this->getNotasCreditoVenta()[0];
        $letra = $notaCreditoVenta->getLetraComprobante();        
        return 'Nota de cr&eacute;dito' . ($letra ? ' ('. $letra .')' : '');
    }    
    
    
    public function getNumeroRecibo() {      
        return $this->getStringReferencias();
    }   
    
    public function getFechaCobranza() {
        return $this->getFecha(); //$this->getNotasCreditoVenta()->first()->getFechaComprobante();
    }  
    
    public function getFechaContable() {
        return $this->getNotasCreditoVenta()[0]->getFechaContable();
    }    
    
    function tieneCobranzaConReferencia($referencia) {
        return (strpos($this->getStringReferencias(), $referencia) !== false);
    }        
    
}
