<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
//use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonCobranza;

/**
 * Description of CobroRenglonCobranza
 *
 * @author Augusto Villa Monte
 * created 13/04/2015
 * 
 * @ORM\Table(name="cobro_renglon_cobranza")
 * @ORM\Entity
 */
class CobroRenglonCobranza extends Cobro {

    /**
     * @ORM\ManyToMany(targetEntity="RenglonCobranza", inversedBy="cobrosRenglonCobranza")
     * @ORM\JoinTable(name="cobro_renglon_cobranza_renglon_cobranza",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_renglon_cobranza", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_renglon_cobranza", referencedColumnName="id")}
     *      )
     */
    protected $renglonesCobranza;  

    /**
     * @ORM\ManyToMany(targetEntity="RenglonCobranzaCheque", inversedBy="cobrosRenglonCobranzaCheque")
     * @ORM\JoinTable(name="cobro_renglon_cobranza_renglon_cobranza_cheque",
     *      joinColumns={@ORM\JoinColumn(name="id_cobro_renglon_cobranza", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_renglon_cobranza_cheque", referencedColumnName="id")}
     *      )
     */
    protected $cheques; 
    
    /**
     * 
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente", mappedBy="cobroRenglonCobranza")
     * 
     */
    protected $anticipoCliente;  
    
    /**
     * @var double
     * @ORM\Column(name="monto_cheques", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $montoCheques; 
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->renglonesCobranza = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cheques = new \Doctrine\Common\Collections\ArrayCollection();
        $this->montoCheques = 0;
    }

    /**
     * Add renglonesCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza $renglonesCobranza
     * @return CobroRenglonCobranza
     */
    public function addRenglonesCobranza(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza $renglonesCobranza)
    {
        $this->renglonesCobranza[] = $renglonesCobranza;

        return $this;
    }

    /**
     * Remove renglonesCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza $renglonesCobranza
     */
    public function removeRenglonesCobranza(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza $renglonesCobranza)
    {
        $this->renglonesCobranza->removeElement($renglonesCobranza);
    }

    /**
     * Get renglonesCobranza
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesCobranza()
    {
        return $this->renglonesCobranza;
    }
    
    /**
     * Set anticipo_cliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente $anticipo_cliente
     * @return CobroRenglonCobranza
     */
    public function setAnticipoCliente($anticipo_cliente) {
        $this->anticipoCliente = $anticipo_cliente;

        return $this;
    }

    /**
     * Get anticipo_cliente
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente 
     */
    public function getAnticipoCliente() {
        return $this->anticipoCliente;
    }    
    
    public function desimputar(){
        $ok = true;
        $anticipo = $this->getAnticipoCliente();
        if ($anticipo){
            $cobrosAnticipo = $this->getAnticipoCliente()->getCobrosAnticipoCliente();
            //var_dump(empty($cobrosAnticipo));die();
            if ($cobrosAnticipo != null) $ok = (sizeOf($cobrosAnticipo) == 0);
        }
        if (!$ok) {            
            $detalle = '<ul>';
            foreach ($cobrosAnticipo as $cobro) {
                //var_dump($cobro->getId());
                $comprobante = $cobro->getComprobantes()[0];
                $letra = $comprobante->getLetraComprobante();
                $detalle_aux = $comprobante->getTipoComprobante() . ($letra ? ' ('. $letra .')' : '') . ' N° ' . $comprobante->getNumeroCompleto();       
                $detalle .= '<li>'.$detalle_aux.'</li>';
            }
            $detalle .= '</ul>';
            //die();
            return "No se puede desimputar el cobro porque el anticipo generado est&aacute; usado para cancelar otros comprobantes: <br/>" . $detalle;
        } else return '';
    }     

    public function tipo () {
        return 'CobroRenglonCobranza';
    }   
    
    public function __toString() {
        $string = "Cobranza ".$this->getNumerosReferenciaParaDesimputar(); //$cantidad == 1 ? 'Un cobro del banco' : $cantidad . ' cobros del banco';
        if ($this->getAnticipoCliente() == null) {
            $string .= ' sin anticipo generado';
            return $string;
        } else {
            $string .= ' con anticipo generado ';
            $cobros = $this->getAnticipoCliente()->getCobrosAnticipoCliente();
            if (sizeOf($cobros) == 0) $string .= ' sin aplicaci&oacute;n';
            else {
                $cantidad = sizeOf($cobros);
                $string .= ' con aplicaci&oacute;n ('. $cantidad .')';
            }
        }
        return $string; // .= ' - ' . ;
    }
    
    public function getNumerosReferenciaParaDesimputar() {
  
        return 'N° ' . $this->getStringReferencias();        
    }
    
    public function getStringReferencias() {
        
        $str = '';        
	$bancos = $this->getRenglonesCobranza();
        $referenciasBanco = array();
	foreach ($bancos  as $renglon) {
	    $referenciasBanco[] = $renglon->getNumeroTransaccion();            
        }
        $str_banco = implode(', ', $referenciasBanco);

	$cheques = $this->getCheques();
	$referenciasCheque = array();
        foreach ($cheques as $renglon) {
	    $referenciasBanco[] = $renglon->getNumero();
        }
        $str_cheque = implode(', ', $referenciasCheque);

	if (sizeof($bancos) > 0 && sizeOf($cheques) > 0) {
	    $str = 'Ref banco ' . $str_banco . ' y ref cheque '. $str_cheque ;
	} else {
            $str = 'Ref '. (sizeof($bancos) > 0 ? 'banco ' : 'cheque ') . (sizeof($bancos) > 0 ? $str_banco : $str_cheque);
        }
        return $str;
        
    }
    
    public function getStringTipo() {
        $str = '';
        if (sizeof($this->getRenglonesCobranza()) > 0) {
            $str = 'Cobranza de banco';
            if (sizeOf($this->getCheques()) > 0) $str .= ' y valores a depositar';
        } else 
            if ((sizeOf($this->getCheques()) > 0)) $str .= 'Valores a depositar';

        return $str;
    }
    
    public function getEsCobroRenglonCobranza() {
        return true;
    }
    
    public function getNumeroRecibo() {
//        $renglonCobranza = sizeOf($this->getRenglonesCobranza()) > 0 ? $this->getRenglonesCobranza()->first() : $this->getCheques()->first();
//        return str_pad($renglonCobranza->getNumeroRecibo(), 8, "0", STR_PAD_LEFT);
        $numeros_recibo = array();
        foreach ($this->getRenglonesCobranza() as $renglon) {
            $reciboBanco = $renglon->getReciboBanco();
            if ($reciboBanco) {
                $numero = $reciboBanco->getNumero();
                if (!in_array($numero, $numeros_recibo)) {
                   $numeros_recibo[] = $numero;
                }
            }
        }
        foreach ($this->getCheques() as $renglon) {
            $reciboCheque = $renglon->getReciboCheque();
            if ($reciboCheque) {
                $numero = $reciboCheque->getNumero();
                if (!in_array($numero, $numeros_recibo)) {
                   $numeros_recibo[] = $numero;
                }
            }
        }
        if (empty($numeros_recibo)) {
            return '-';
        } else {
            return implode(' , ', $numeros_recibo);
        }        
    }

    /**
     * Set montoCheques
     *
     * @param string $monto_cheques
     * @return RenglonCobranzaCheque
     */
    public function setMontoCheques($monto_cheques)
    {
        $this->montoCheques = $monto_cheques;

        return $this;
    }

    /**
     * Get montoCheques
     *
     * @return string 
     */
    public function getMontoCheques()
    {
        return $this->montoCheques;
    }    
    
    /**
     * Add cheques
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $cheques
     * @return CobroRenglonCobranzaCheque
     */
    public function addCheques(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $cheques)
    {
        $this->cheques[] = $cheques;

        return $this;
    }

    /**
     * Remove cheques
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $cheques
     */
    public function removeCheques(\ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque $cheques)
    {
        $this->cheques->removeElement($cheques);
    }

    /**
     * Get cheques
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCheques()
    {
        return $this->cheques;
    }
    
    public function getFechaCobranza() {
//        if (sizeOf($this->getRenglonesCobranza()) > 0) {
//            $renglones = $this->getRenglonesCobranza();
//            $fecha_aux = $renglones->first()->getFecha();
//            foreach ($renglones as $renglon) {
//                if ($fecha_aux != $renglon->getFecha()) {
//                    return null;
//                } else {
//                    $fecha_aux = $renglon->getFecha();
//                }
//            }
//        } else {
//            $fecha_aux = $this->getCheques()->first()->getFecha();
//        }
//        return $fecha_aux;
        $renglonesBanco = $this->getRenglonesCobranza();
        $renglonesCheque = $this->getCheques();
        if (sizeOf($renglonesBanco)>0) {
            $fecha = $renglonesBanco[0]->getFechaRegistro();
        }
        if (sizeOf($renglonesCheque)>0) {
            $fecha = $renglonesCheque[0]->getFechaRegistro();
        }        
        return $fecha;        
    }
    
    function tieneCobranzaConReferencia($referencia) {
        foreach ($this->getRenglonesCobranza() as $renglon) {
            if (strpos('-'.$renglon->getNumeroTransaccion(), $referencia, 1) !== false) {
                return true;
            }
        }
        foreach ($this->getCheques() as $renglon) {
            if (strpos('-'.$renglon->getNumero(), $referencia, 1) != false) {
                return true;
            }
        }
        return false;
    }
    
}
