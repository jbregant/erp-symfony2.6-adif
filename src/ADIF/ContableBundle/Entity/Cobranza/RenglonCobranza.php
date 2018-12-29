<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonCobranza;

/**
 * RenglonCobranza
 *
 * @author Augusto Villa Monte
 * created 11/04/2015
 * 
 * @ORM\Table(name="renglon_cobranza")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "renglon_cobranza_banco" = "ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco",
 *      "renglon_cobranza_cheque" = "ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque"
 * })
 */ 
class RenglonCobranza extends MovimientoConciliable {

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
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;
     
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="datetime", nullable=true)
     */
    protected $fechaRegistro;
    
    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $monto;

    /**
     * @var \ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza")
     * @ORM\JoinColumn(name="id_estado_renglon_cobranza", referencedColumnName="id")
     * 
     */
    protected $estadoRenglonCobranza;

    /**
     * @ORM\ManyToMany(targetEntity="CobroRenglonCobranza", mappedBy="renglonesCobranza")
     * */
    protected $cobrosRenglonCobranza;

    /**
     * @ORM\ManyToMany(targetEntity="CobroRenglonCobranza", mappedBy="cheques")
     * */
    protected $cobrosRenglonCobranzaCheque;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_recibo", type="string", length=8, nullable=true)
     * @Assert\Length(
     *      max="8", 
     *      maxMessage="El número de recibo no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroRecibo;      

    /**
     * @ORM\Column(name="id_cuenta_bancaria", type="integer", nullable=true)
     */
    protected $idCuentaBancaria;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuentaBancaria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=4000, nullable=true)
     * @Assert\Length(
     *      max="4000", 
     *      maxMessage="La observación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $observacion;
    
    /**
     * @var boolean
     * Este campo se va a usar, solo para determinar si el cobro vino por la migracion de la depuracion de las 
     * cuentas corrientes de clientes, que son cobranzas viejas, que no se tienen que mostrar como 
     * cobranzas a imputar
     * @ORM\Column(name="es_migracion", type="boolean", nullable=true)
     */
    protected $esMigracion = false;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RenglonCobranza
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return RenglonCobranza
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set estadoRenglonCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza $estadoRenglonCobranza
     * @return RenglonCobranza
     */
    public function setEstadoRenglonCobranza(\ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza $estadoRenglonCobranza = null) {
        $this->estadoRenglonCobranza = $estadoRenglonCobranza;

        return $this;
    }

    /**
     * Get estadoRenglonCobranza
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza 
     */
    public function getEstadoRenglonCobranza() {
        return $this->estadoRenglonCobranza;
    }

    /**
     * Constructor
     */
    public function __construct() {
        //parent::__construct();
        $this->cobrosRenglonCobranza = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cobrosRenglonCobranzaCheque = new \Doctrine\Common\Collections\ArrayCollection(); 
    }

    /**
     * Add cobrosRenglonCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranza
     * @return RenglonCobranza
     */
    public function addCobrosRenglonCobranza(\ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranza) {
        $this->cobrosRenglonCobranza[] = $cobrosRenglonCobranza;

        return $this;
    }

    /**
     * Remove cobrosRenglonCobranza
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranza
     */
    public function removeCobrosRenglonCobranza(\ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranza) {
        $this->cobrosRenglonCobranza->removeElement($cobrosRenglonCobranza);
    }

    /**
     * Get cobrosRenglonCobranza
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobrosRenglonCobranza() {
        return $this->cobrosRenglonCobranza;
    }

    /**
     * Get numeroRecibo
     *
     * @return string 
     */
    public function getNumeroRecibo() {
        return $this->numeroRecibo;
    } 

    /**
     * Set idCuentaBancaria
     *
     * @param integer $idCuentaBancaria
     * @return RenglonCobranza
     */
    public function setIdCuentaBancaria($idCuentaBancaria) {
        $this->idCuentaBancaria = $idCuentaBancaria;

        return $this;
    }

    /**
     * Get idCuentaBancaria
     *
     * @return integer 
     */
    public function getIdCuentaBancaria() {
        return $this->idCuentaBancaria;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuentaBancaria
     */
    public function setCuentaBancaria($cuentaBancaria) {

        if (null != $cuentaBancaria) {
            $this->idCuentaBancaria = $cuentaBancaria->getId();
        } //.
        else {
            $this->idCuentaBancaria = null;
        }

        $this->cuentaBancaria = $cuentaBancaria;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaBancaria() {
        return $this->cuentaBancaria;
    }
       
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        //var_dump($this->getId() );var_dump($this->getIdCuentaBancaria());
        //return (($this->getIdCuentaBancaria() == $cuentaBancaria->getId()) && ($this->getEstadoRenglonCobranza()->getDenominacion() != ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE) && ($fecha_inicio ? $this->getFecha() >= $fecha_inicio : true) && ($fecha_fin ? $this->getFecha() <= $fecha_fin : true));
        return false;
    }
    
    public function getConcepto() {
        $ref = $this->getReferencia();
        return $ref ? 'Cobranza: Transaccion N&ordm; ' . $ref : 'Cobranza';
    }

    public function getReferencia() {
        return $this->getNumeroTransaccion();
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
     * Add cobrosRenglonCobranzaCheque
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranzaCheque
     * @return RenglonCobranza
     */
    public function addCobrosRenglonCobranzaCheque(\ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranzaCheque) {
        $this->cobrosRenglonCobranzaCheque[] = $cobrosRenglonCobranzaCheque;

        return $this;
    }

    /**
     * Remove cobrosRenglonCobranzaCheque
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranzaCheque
     */
    public function removeCobrosRenglonCobranzaCheque(\ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza $cobrosRenglonCobranzaCheque) {
        $this->cobrosRenglonCobranzaCheque->removeElement($cobrosRenglonCobranzaCheque);
    }

    /**
     * Get cobrosRenglonCobranzaCheque
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobrosRenglonCobranzaCheque() {
        return $this->cobrosRenglonCobranzaCheque;
    }    

    /**
     * Set numeroRecibo
     *
     * @param string $numeroRecibo
     * @return RenglonCobranza
     */
    public function setNumeroRecibo($numeroRecibo) {
        $this->numeroRecibo = $numeroRecibo;

        return $this;
    }

    /**
     * Set fecha_registro
     *
     * @param \DateTime $fecha_registro
     * @return RenglonCobranzaCheque
     */
    public function setFechaRegistro($fecha_registro) {
        $this->fechaRegistro = $fecha_registro;

        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime 
     */
    public function getFechaRegistro() {
        return $this->fechaRegistro;
    } 
    
    /**
     * Set observacion
     *
     * @param string $observacion
     * @return RenglonCobranza
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }
    
    public function setEsMigracion($esMigracion) 
    {
        $this->esMigracion = $esMigracion;
        
        return $this;
    }
    
    public function getEsMigracion()
    {
        return $this->esMigracion;
    }
    
}
