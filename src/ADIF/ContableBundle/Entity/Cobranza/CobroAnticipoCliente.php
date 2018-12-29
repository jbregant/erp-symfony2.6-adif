<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CobroAnticipoCliente
 *
 * @author Augusto Villa Monte
 * created 13/04/2015
 * 
 * @ORM\Table(name="cobro_anticipo_cliente")
 * @ORM\Entity
 */
class CobroAnticipoCliente extends Cobro{
    
    /**
     * @ORM\ManyToMany(targetEntity="AnticipoCliente", inversedBy="cobrosAnticipoCliente")
     * @ORM\JoinTable(name="cobro_anticipo_cliente_anticipo_cliente",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_anticipo_cliente", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_anticipo_cliente", referencedColumnName="id")}
     *      )
     */
    protected $anticiposCliente;  
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->anticiposCliente = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add anticiposCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente $anticiposCliente
     * @return CobroAnticipoCliente
     */
    public function addAnticiposCliente(\ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente $anticiposCliente)
    {
        $this->anticiposCliente[] = $anticiposCliente;

        return $this;
    }

    /**
     * Remove anticiposCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente $anticiposCliente
     */
    public function removeAnticiposCliente(\ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente $anticiposCliente)
    {
        $this->anticiposCliente->removeElement($anticiposCliente);
    }

    /**
     * Get anticiposCliente
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticiposCliente()
    {
        return $this->anticiposCliente;
    }
    
    public function desimputar() {
        $anticipoCliente = $this->getAnticiposCliente()[0];
        $anticipoCliente->setSaldo($anticipoCliente->getSaldo()+$this->getMonto());
        return '';
    }
    
    public function tipo () {
        return 'CobroAnticipoCliente';
    }  
    
    public function __toString() {
        $cobro = $this->getAnticiposCliente()[0]->getCobroRenglonCobranza();
        //$comprobante = $cobro->getComprobantes()[0];
        //$letra = $comprobante->getLetraComprobante();
        //$cobro_str = $cobro->getFecha()->format('d/m/Y').' por '. $cobro->getMonto();
        //$comprobante_str = $comprobante->getTipoComprobante() . ($letra ? ' ('. $letra .')' : '') . ' N° ' . $comprobante->getNumeroCompleto();
        
        //$cantidad = sizeOf($this->getAnticiposCliente()[0]->getCobrosAnticipoCliente())-1;
        //$usos = 'Anticipo de cliente ' . ($cantidad == 0 ? 'sin usos adicionales' : 'con otros '. $cantidad . ' usos');
        $string = 'Cancelaci&oacute;n de saldo con anticipo de cliente generado por cobranza con '; //del '. $cobro_str . ' para '. $comprobante_str;
        
        return $string .= $cobro->getNumerosReferenciaParaDesimputar();
        
    }
    
    public function getStringReferencias() {
        $cobro = $this->getAnticiposCliente()[0]->getCobroRenglonCobranza();
        return $cobro->getStringReferencias();
    }
    
    public function getStringTipo() {
        return 'Anticipo de cliente';
    }    
    
    public function getNumeroRecibo() {      
        //return $this->getStringReferencias();
        return $this->getAnticiposCliente()->first()->getCobroRenglonCobranza()->getNumeroRecibo();
    }    
    
    public function getFechaCobranza() {
        return $this->getAnticiposCliente()->first()->getFecha();
    }
    
    public function getFechaContable() {
        return $this->getAnticiposCliente()->first()->getCobroRenglonCobranza()->getFechaContable();
    } 
    
    function tieneCobranzaConReferencia($referencia) {
        $cobro = $this->getAnticiposCliente()[0]->getCobroRenglonCobranza();
        return $cobro->tieneCobranzaConReferencia($referencia);
    }      
    
}
