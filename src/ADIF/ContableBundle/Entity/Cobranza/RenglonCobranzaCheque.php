<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

//use ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonCobranza;

/**
 * RenglonCobranzaCheque
 *
 * @author Augusto Villa Monte
 * created 10/07/2015
 * 
 * @ORM\Table(name="renglon_cobranza_cheque")
 * @ORM\Entity
 */
class RenglonCobranzaCheque extends RenglonCobranza {

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=15, unique=false, nullable=false)
     * @Assert\Length(
     *      max="8", 
     *      maxMessage="El número de cheque no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numero;     

    /**
     * @ORM\Column(name="id_banco", type="integer", nullable=false)
     */
    protected $idBanco;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Banco
     */
    protected $banco; //emisor    

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta; 

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_deposito", type="datetime", nullable=true)
     */
    protected $fechaDeposito; 
    
    /**
     * @var \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza", inversedBy="renglonesCheque", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_recibo", referencedColumnName="id", nullable=true)
     * })
     */
    protected $reciboCheque;        
    
    /**
     * Set numero
     *
     * @param string $numero
     * @return RenglonCobranza
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }
    
    /**
     * Set idBanco
     *
     * @param integer $idBanco
     * @return RenglonCobranzaCheque
     */
    public function setIdBanco($idBanco) {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get idBanco
     *
     * @return integer 
     */
    public function getIdBanco() {
        return $this->idBanco;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Banco $banco
     */
    public function setBanco($banco) {

        if (null != $banco) {
            $this->idBanco = $banco->getId();
        } //.
        else {
            $this->idBanco = null;
        }

        $this->banco = $banco;
    }

    /**
     * 
     * @return type
     */
    public function getBanco() {
        return $this->banco;
    }   
    
    /**
     * 
     * @param type $idCuenta
     * @return \ADIF\ComprasBundle\Entity\Proveedor
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuenta
     */
    public function setCuenta($cuenta) {

        if (null != $cuenta) {
            $this->idCuenta = $cuenta->getId();
        } //.
        else {
            $this->idCuenta = null;
        }

        $this->cuenta = $cuenta;
    }

    /**
     * 
     * @return type
     */
    public function getCuenta() {
        return $this->cuenta;
    }  

    /**
     * Set fecha_deposito
     *
     * @param \DateTime $fecha_deposito
     * @return RenglonCobranzaCheque
     */
    public function setFechaDeposito($fecha_deposito) {
        $this->fechaDeposito = $fecha_deposito;

        return $this;
    }

    /**
     * Get fechaDeposito
     *
     * @return \DateTime 
     */
    public function getFechaDeposito() {
        return $this->fechaDeposito;
    }
    
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        //var_dump($this->getId() );var_dump($this->getIdCuentaBancaria());
        return (($this->getIdCuenta() != null) && //
                ($this->getIdCuenta() == $cuentaBancaria->getId()) && //
                ($this->getEstadoRenglonCobranza()->getDenominacion() != ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE) && //
                ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) && //
                ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true));
        //return false;
    }

    public function getConcepto() {
        $ref = $this->getReferencia();
        return $ref ? 'Cobranza: Cheque N&ordm; ' . $ref : 'Cobranza';
    }

    public function getReferencia() {
        return $this->getNumero();
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

    /**
     * Constructor
     */
    public function __construct() {
        //parent::__construct();

    }
    public function getFechaParaMayor() {
        return $this->getFechaDeposito();
    }    


    /**
     * Set reciboCheque
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboCheque
     *
     * @return RenglonCobranzaCheque
     */
    public function setReciboCheque(\ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboCheque = null)
    {
        $this->reciboCheque = $reciboCheque;

        return $this;
    }

    /**
     * Get reciboCheque
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     */
    public function getReciboCheque()
    {
        return $this->reciboCheque;
    }
    
    public function getRecibo() {
        return $this->getReciboCheque();
    }
    
}
