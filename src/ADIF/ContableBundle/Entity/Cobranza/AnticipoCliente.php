<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;

/**
 * AnticipoCliente
 *
 * @author Augusto Villa Monte
 * created 11/04/2015
 * 
 * @ORM\Table(name="anticipo_cliente")
 * @ORM\Entity
 */
class AnticipoCliente extends BaseAuditoria implements BaseAuditable {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $monto;   
    
    /**
     * @ORM\Column(name="id_cliente", type="integer", nullable=false)
     */
    protected $idCliente;

    /**
     * @var ADIF\ComprasBundle\Entity\Cliente
     */
    protected $cliente;
    
    /**
     * @ORM\ManyToMany(targetEntity="CobroAnticipoCliente", mappedBy="anticiposCliente")
     * */
    protected $cobrosAnticipoCliente;    
    
    /**
     * 
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza", cascade={"all"}, inversedBy="anticipoCliente")
     * @ORM\JoinColumn(name="id_cobro_renglon_cobranza", referencedColumnName="id", nullable=false)
     * 
     */    
    protected $cobroRenglonCobranza;
    
    /**
     * @var double
     * @ORM\Column(name="saldo", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $saldo;     

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
     * @return AnticipoCliente
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
     * 
     * @param type $idCliente
     * @return AnticipoCliente
     */
    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
        return $this;
    }

    /**
     * Get idCliente
     *
     * @return integer 
     */
    public function getIdCliente() {
        return $this->idCliente;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Cliente $cliente
     */
    public function setCliente($cliente) {
        if (null != $cliente) {
            $this->idCliente = $cliente->getId();
        } else {
            $this->idCliente = null;
        }

        $this->cliente = $cliente;
    }

    /**
     * 
     * @return type
     */
    public function getCliente() {
        return $this->cliente;
    }
        
    /**
     * Constructor
     */
    public function __construct()
    {
        //parent::__construct();
        $this->cobrosAnticipoCliente = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cobrosAnticipoCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroAnticipoCliente $cobrosAnticipoCliente
     * @return AnticipoCliente
     */
    public function addCobrosAnticipoCliente(\ADIF\ContableBundle\Entity\Cobranza\CobroAnticipoCliente $cobrosAnticipoCliente)
    {
        $this->cobrosAnticipoCliente[] = $cobrosAnticipoCliente;

        return $this;
    }

    /**
     * Remove cobrosAnticipoCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroAnticipoCliente $cobrosAnticipoCliente
     */
    public function removeCobrosAnticipoCliente(\ADIF\ContableBundle\Entity\Cobranza\CobroAnticipoCliente $cobrosAnticipoCliente)
    {
        $this->cobrosAnticipoCliente->removeElement($cobrosAnticipoCliente);
    }

    /**
     * Get cobrosAnticipoCliente
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobrosAnticipoCliente()
    {
        return $this->cobrosAnticipoCliente;
    }
    
    /**
     * Set cobroRenglonCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobroRenglonCobranza
     * @return AnticipoCliente
     */
    public function setCobroRenglonCobranza($cobroRenglonCobranza) {
        $this->cobroRenglonCobranza = $cobroRenglonCobranza;

        return $this;
    }

    /**
     * Get cobroRenglonCobranza
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza
     */
    public function getCobroRenglonCobranza() {
        return $this->cobroRenglonCobranza;
    }
    
    /**
     * Set saldo
     *
     * @param string $saldo
     * @return Comprobante
     */
    public function setSaldo($saldo) {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string 
     */
    public function getSaldo() {
        return $this->saldo;
    }  
    
    public function getFecha() {
        return $this->getCobroRenglonCobranza()->getFechaCobranza();
    }
    
    public function getReferencias() {
        return $this->getCobroRenglonCobranza()->getStringReferencias();
    }    
    
}
