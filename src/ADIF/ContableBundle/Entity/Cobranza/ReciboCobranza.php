<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ReciboCobranza
 *
 * @author Augusto Villa Monte
 * created 11/08/2015
 * 
 * @ORM\Table(name="recibos_cobranza")
 * @ORM\Entity
 * @UniqueEntity(fields={"numero"}, ignoreNull=true, message="El n&uacute;mero de recibo ya se encuentra en uso.")
 */
class ReciboCobranza extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    protected $fecha;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", unique=true, nullable=false)
     */
    private $numero;    

    /**
     *
     * @var \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco", mappedBy="reciboBanco", cascade={"all"})
     */
    protected $renglonesBanco;
	
    /**
     *
     * @var \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque", mappedBy="reciboCheque", cascade={"all"})
     */
    protected $renglonesCheque;	
	
    /**
     *
     * @var \ADIF\ContableBundle\Entity\Cobranza\RetencionCliente
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\RetencionCliente", mappedBy="reciboRetencion", cascade={"all"})
     */
    protected $renglonesRetencion;   
    
    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta")
     * @ORM\JoinTable(name="recibos_cobranza_comprobantes_venta",
     *      joinColumns={@ORM\JoinColumn(name="recibo_cobranza_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comprobante_venta_id", referencedColumnName="id")}
     *      )
     **/
    private $comprobantes;
    
    /**
     * @ORM\Column(name="id_cliente", type="integer", nullable=true)
     */
    protected $idCliente;

    /**
     * @var ADIF\ComprasBundle\Entity\Cliente
     */
    protected $cliente;   
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barras", type="string", length=26, nullable=true)
     */
    protected $codigoBarras;     
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->renglonesBanco = new \Doctrine\Common\Collections\ArrayCollection();
        $this->renglonesCheque = new \Doctrine\Common\Collections\ArrayCollection();
        $this->renglonesRetencion = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comprobantes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return ReciboCobranza
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
     * Set numero
     *
     * @param string $numero
     *
     * @return ReciboCobranza
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Add renglonesBanco
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco $renglonesBanco
     *
     * @return ReciboCobranza
     */
    public function addRenglonesBanco(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco $renglonesBanco)
    {
        $this->renglonesBanco[] = $renglonesBanco;
        $renglonesBanco->setReciboBanco($this);
        return $this;
    }

    /**
     * Remove renglonesBanco
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco $renglonesBanco
     */
    public function removeRenglonesBanco(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco $renglonesBanco)
    {
        $this->renglonesBanco->removeElement($renglonesBanco);
        $renglonesBanco->setReciboBanco(null);
    }

    /**
     * Get renglonesBanco
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRenglonesBanco()
    {
        return $this->renglonesBanco;
    }

    /**
     * Add renglonesCheque
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $renglonesCheque
     *
     * @return ReciboCobranza
     */
    public function addRenglonesCheque(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $renglonesCheque)
    {
        $this->renglonesCheque[] = $renglonesCheque;
        $renglonesCheque->setReciboCheque($this);
        return $this;
    }

    /**
     * Remove renglonesCheque
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $renglonesCheque
     */
    public function removeRenglonesCheque(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $renglonesCheque)
    {
        $this->renglonesCheque->removeElement($renglonesCheque);
        $renglonesCheque->setReciboCheque(null);
    }

    /**
     * Get renglonesCheque
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRenglonesCheque()
    {
        return $this->renglonesCheque;
    }

    /**
     * Add renglonesRetencion
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $renglonesRetencion
     *
     * @return ReciboCobranza
     */
    public function addRenglonesRetencion(\ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $renglonesRetencion)
    {
        $this->renglonesRetencion[] = $renglonesRetencion;
        $renglonesRetencion->setReciboRetencion($this);
        return $this;
    }

    /**
     * Remove renglonesRetencion
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $renglonesRetencion
     */
    public function removeRenglonesRetencion(\ADIF\ContableBundle\Entity\Cobranza\RetencionCliente $renglonesRetencion)
    {
        $this->renglonesRetencion->removeElement($renglonesRetencion);
        $renglonesRetencion->setReciboRetencion(null);        
    }

    /**
     * Get renglonesRetencion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRenglonesRetencion()
    {
        return $this->renglonesRetencion;
    }

    /**
     * Add comprobante
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobante
     *
     * @return ReciboCobranza
     */
    public function addComprobante(\ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobante)
    {
        $this->comprobantes[] = $comprobante;

        return $this;
    }

    /**
     * Remove comprobante
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobante
     */
    public function removeComprobante(\ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta $comprobante)
    {
        $this->comprobantes->removeElement($comprobante);
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
    
    /**
     * 
     * @param type $idCliente
     * @return ReciboCliente
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
     * Set codigoBarras
     *
     * @param string $codigoBarras
     * @return ComprobanteVenta
     */
    public function setCodigoBarras($codigoBarras) {
        $this->codigoBarras = $codigoBarras;

        return $this;
    }

    /**
     * Get codigoBarras
     *
     * @return string 
     */
    public function getCodigoBarras() {
        return $this->codigoBarras;
    }      

    /**
     * 
     * @return type
     */    
    public function getNumeroRecibo() {
        return str_pad($this->getNumero(), 10, "0", STR_PAD_LEFT);
    }  

    /**
     * 
     * @return type
     */    
    public function getCuentaBancaria() {
        $cuenta_bancaria = null;
        if (sizeOf($this->getRenglonesBanco()) > 0 && sizeOf($this->getRenglonesCheque()) == 0 && sizeOf($this->getRenglonesRetencion()) == 0) {
            $cuenta_bancaria = $this->getRenglonesBanco()->first()->getCuentaBancaria();
        }
        return $cuenta_bancaria;
    }
    
    public function getImporteTotal() {
        $importe = 0;
        foreach ($this->getRenglonesBanco() as $banco) {
            $importe += $banco->getMonto();
        }
        foreach ($this->getRenglonesCheque() as $cheque) {
            $importe += $cheque->getMonto();
        }
        foreach ($this->getRenglonesRetencion() as $retencion) {
            $importe += $retencion->getMonto();
        }
        return $importe;   
    }

    public function getImporteSinImputar() {
        $importe = 0;
        
        $comprobantes = $this->getComprobantes();
        if ($this->getCliente() == null && sizeOf($comprobantes) == 0) {
            
            $renglones_banco = $this->getRenglonesBanco();
            if (sizeOf($renglones_banco) > 0) {
                $importe = $renglones_banco->first()->getMonto();
            } 
            
            $renglones_cheque = $this->getRenglonesCheque();
            if (sizeOf($renglones_cheque) > 0) {
                $importe = $renglones_cheque->first()->getMonto();
            }
             
        }

        return $importe;
    }
    
    public function getImporteComprobantes() {
        $importe = 0;
        foreach ($this->getComprobantes() as $comprobante) {
            $importe += $comprobante->getTotal();
        }
        return $importe;   
    }    
    
    public function getCodigoBarrasNacion() {
        return ($this->getCodigoBarras() == null ? $this->generarCodigoBarras() : $this->getCodigoBarras());
    }
    
    public function getDetalle() {
        $array_detalle = [];
        if (sizeOf($this->getRenglonesBanco())>0) $array_detalle [] = 'Cobro bancario';
        if (sizeOf($this->getRenglonesCheque())>0) $array_detalle [] = 'Valor a depositar';
        if (sizeOf($this->getRenglonesRetencion())>0) $array_detalle [] = 'Retenci&oacute;n cliente';
        return implode(' - ', $array_detalle);   
    }
    
    public function generarCodigoBarras() {
        return '4687' . $this->getNumeroRecibo() . '049999000000';
    }
    
    public function getReferenciasCobro()
    {
        $referencias = array();
        
        if (sizeOf($this->getRenglonesBanco()) > 0) {
            foreach($this->getRenglonesBanco() as $renglonCobranzaBanco)  {
                $referencias[] = $renglonCobranzaBanco->getReferencia(); 
            }
        }
        
        if (sizeOf($this->getRenglonesCheque()) > 0) {
            foreach($this->getRenglonesCheque() as $renglonCobranzaCheque) {
                $referencias[] = $renglonCobranzaCheque->getReferencia();
            }
        }
        
        return implode('<br>', $referencias);
    }
    
    public function getComprobantesAsString($separador = ', ')
    {
        $comprobantesStr = '';
        $comprobantes = array();
        if (!empty($this->comprobantes)) {
            foreach($this->comprobantes as $comprobante) {
                $comprobantesStr .= $comprobante->getTipoComprobante()->getNombre();
                $comprobantesStr .= ($comprobante->getLetraComprobante() != null)
                    ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ') '
                    : ' ';
                $comprobantesStr .= $comprobante->getNumeroCompleto();
                $comprobantes[] = $comprobantesStr;
            }
        }
        
        return !empty($comprobantes) ? implode($separador, $comprobantes) : array();
    }
    
    public function getTipoComprobantesAsString($separador = ', ')
    {
        $comprobantesStr = '';
        $comprobantes = array();
        if (!empty($this->comprobantes)) {
            foreach($this->comprobantes as $comprobante) {
                $comprobantesStr .= $comprobante->getTipoComprobante()->getNombre();
                $comprobantesStr .= ($comprobante->getLetraComprobante() != null)
                    ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ') '
                    : ' ';
                $comprobantes[] = $comprobantesStr;
            }
        }
        
        return !empty($comprobantes) ? implode($separador, $comprobantes) : array();
    }
    
    public function getNumerosComprobantesAsString($separador = ', ')
    {
        $comprobantesStr = '';
        $comprobantes = array();
        if (!empty($this->comprobantes)) {
            foreach($this->comprobantes as $comprobante) {
                $comprobantesStr .= $comprobante->getNumeroCompleto();
                $comprobantes[] = $comprobantesStr;
            }
        }
        
        return !empty($comprobantes) ? implode($separador, $comprobantes) : array();
    }
    
}
