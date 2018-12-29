<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

//use ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonCobranza;

/**
 * RenglonCobranzaBanco
 *
 * @author Augusto Villa Monte
 * created 10/07/2015
 * 
 * @ORM\Table(name="renglon_cobranza_banco")
 * @ORM\Entity
 */
class RenglonCobranzaBanco extends RenglonCobranza {

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=26, nullable=true)
     */
    protected $codigo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_manual", type="boolean", nullable=false)
     */
    protected $esManual;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_transaccion", type="string", length=8, unique=false, nullable=true)
     * @Assert\Length(
     *      max="8", 
     *      maxMessage="El número de transacción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroTransaccion;
    
    /**
     * @var \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza", inversedBy="renglonesBanco", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_recibo", referencedColumnName="id", nullable=true)
     * })
     */
    protected $reciboBanco;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="es_onabe", type="boolean", nullable=true)
     */
    protected $esOnabe = false;

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return RenglonCobranza
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set esManual
     *
     * @param boolean $esManual
     * @return RenglonCobranza
     */
    public function setEsManual($esManual) {
        $this->esManual = $esManual;

        return $this;
    }

    /**
     * Get esManual
     *
     * @return boolean 
     */
    public function getEsManual() {
        return $this->esManual;
    }

    /**
     * Set numeroTransaccion
     *
     * @param string $numeroTransaccion
     * @return RenglonCobranza
     */
    public function setNumeroTransaccion($numeroTransaccion) {
        $this->numeroTransaccion = $numeroTransaccion;

        return $this;
    }

    /**
     * Get numeroTransaccion
     *
     * @return string 
     */
    public function getNumeroTransaccion() {
        return $this->numeroTransaccion;
    }   

    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        //var_dump($this->getId() );var_dump($this->getIdCuentaBancaria());
        return (($this->getIdCuentaBancaria() == $cuentaBancaria->getId()) && 
                ($this->getEstadoRenglonCobranza()->getDenominacion() != ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE) && 
                ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) && 
                ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true));
    }

    public function getConcepto() {
        $ref = $this->getReferencia();
        return $ref ? 'Cobranza: Transaccion N&ordm; ' . $ref : 'Cobranza';
    }

    public function getReferencia() {
        if (!$this->getEsManual()) {
            $referencia = substr($this->getNumeroTransaccion(), -6);
        } else {
            $referencia = $this->getNumeroTransaccion();
        }  
        return $referencia;
    }

    public function getMontoMovimiento($cuentaBancaria = null) {
        return $this->getMonto() * (-1);
    }

    public function getTipo() {
        return 'COBRANZA';
    }

    public function getCodigoConcepto() {
        return 2;
    }

    public function getEsContabilizable() {
        return false;
    }
    
    public function getFechaParaMayor() {
        return $this->getFechaRegistro();
    }       

    /**
     * Set reciboBanco
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboBanco
     *
     * @return RenglonCobranzaBanco
     */
    public function setReciboBanco(\ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboBanco = null)
    {
        $this->reciboBanco = $reciboBanco;

        return $this;
    }

    /**
     * Get reciboBanco
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     */
    public function getReciboBanco()
    {
        return $this->reciboBanco;
    }
    
    public function getRecibo() {
        return $this->getReciboBanco();
    }

    /**
     * Get tipoRenglon
     *
     * @return string 
     */
    public function getTipoRenglon() {
        $codigo = substr($this->getNumeroTransaccion(), 0, 2);
        if (!$this->getEsManual()) {
            if ($codigo == '54') {
                $tipoRenglon = 'Cheque';
            } else { //es 50
                $tipoRenglon = 'Banco';
            }
        } else {
            $tipoRenglon = '-';
        }    
        return $tipoRenglon;
    }    
    
    /**
     * Set esOnabe
     *
     * @param boolean $esOnabe
     * @return RenglonCobranza
     */
    public function setEsOnabe($esOnabe) {
        $this->esOnabe = $esOnabe;

        return $this;
    }

    /**
     * Get esOnabe
     *
     * @return boolean 
     */
    public function getEsOnabe() {
        return $this->esOnabe;
    }
}
