<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CobroRetencionCliente
 *
 * @author Augusto Villa Monte
 * created 11/07/2015
 * 
 * @ORM\Table(name="cobro_retencion_cliente")
 * @ORM\Entity
 */
class CobroRetencionCliente extends Cobro{
    
    /**
     * @ORM\ManyToMany(targetEntity="RetencionCliente", inversedBy="cobrosRetencionCliente")
     * @ORM\JoinTable(name="cobro_retencion_cliente_retencion_cliente",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_retencion_cliente", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_retencion_cliente", referencedColumnName="id")}
     *      )
     */
    protected $retencionesCliente;     
    
    /**
     * Add retencionesCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $retencionesCliente
     * @return CobroRetencionCliente
     */
    public function addRetencionesCliente(\ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $retencionesCliente)
    {
        $this->retencionesCliente[] = $retencionesCliente;

        return $this;
    }

    /**
     * Remove retencionesCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $retencionesCliente
     */
    public function removeRetencionesCliente(\ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $retencionesCliente)
    {
        $this->retencionesCliente->removeElement($retencionesCliente);
    }

    /**
     * Get retencionesCliente
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetencionesCliente()
    {
        return $this->retencionesCliente;
    }   
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->retencionesCliente = new \Doctrine\Common\Collections\ArrayCollection();        
    }
    
    public function desimputar() {
        //$anticipoCliente = $this->getAnticiposCliente()[0];
        //$anticipoCliente->setSaldo($anticipoCliente->getSaldo()+$this->getMonto());
        return '';
    }
    
    public function tipo () {
        return 'CobroRetencionCliente';
    }  
    
    public function __toString() {

        return 'Aplicaci&oacute;n de retenci&oacute;n - ' . $this->getRetencionesCliente()->first()->getTipoImpuesto();
        
    }
    
    public function getStringReferencias() {
        return 'N° '.$this->getRetencionesCliente()->first()->getNumero();
    }
    
    public function getStringTipo() {
        return 'Retenci&oacute;n de cliente';
    }    
    
    public function getNumeroRecibo() {      
        return $this->getStringReferencias();
    }   
    
    public function getEsCobroRetencionCliente() {
        true;
    }    
    
    public function getFechaCobranza() {
        return $this->getRetencionesCliente()->first()->getFecha();
    }
    
    function tieneCobranzaConReferencia($referencia) {
        return (strpos($this->getStringReferencias(), $referencia) !== false);
    }    
    
}
