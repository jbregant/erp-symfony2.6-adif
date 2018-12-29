<?php

namespace ADIF\ContableBundle\Entity\Cobranza;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;

/**
 * RetencionCliente
 *
 * @author Augusto Villa Monte
 * created 11/10/2015
 * 
 * @ORM\Table(name="retencion_cliente")
 * @ORM\Entity
 */
class RetencionCliente extends BaseAuditoria implements BaseAuditable {
    
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
     * @ORM\ManyToMany(targetEntity="CobroRetencionCliente", mappedBy="retencionesCliente")
     */
    protected $cobrosRetencionCliente;    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RetencionClienteParametrizacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_impuesto", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoImpuesto;    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="datetime", nullable=true)
     */
    protected $fechaRegistro;

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
     * @var \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza", inversedBy="renglonesRetencion", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_recibo", referencedColumnName="id", nullable=true)
     * })
     */
    protected $reciboRetencion;        
    
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
     * @return RetencionCliente
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
     * Constructor
     */
    public function __construct()
    {
        $this->cobrosRetencionCliente = new \Doctrine\Common\Collections\ArrayCollection();
        
    }   
    
    /**
     * Set numero
     *
     * @param string $numero
     * @return RetencionCliente
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RetencionCliente
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
     * Set tipoImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\RetencionClienteParametrizacion $tipoImpuesto
     * @return RetencionCliente
     */
    public function setTipoImpuesto(\ADIF\ContableBundle\Entity\RetencionClienteParametrizacion $tipoImpuesto) {
        $this->tipoImpuesto = $tipoImpuesto;

        return $this;
    }

    /**
     * Get tipoImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\RetencionClienteParametrizacion
     */
    public function getTipoImpuesto() {
        return $this->tipoImpuesto;
    }
    

    /**
     * Add cobrosRetencionCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente $cobrosRetencionCliente
     * @return RenglonCobranza
     */
    public function addCobrosRetencionCliente(\ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente $cobrosRetencionCliente) {
        $this->cobrosRetencionCliente[] = $cobrosRetencionCliente;

        return $this;
    }

    /**
     * Remove cobrosRetencionCliente
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente $cobrosRetencionCliente
     */
    public function removeCobrosRetencionCliente(\ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente $cobrosRetencionCliente) {
        $this->cobrosRetencionCliente->removeElement($cobrosRetencionCliente);
    }

    /**
     * Get cobrosRetencionCliente
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobrosRetencionCliente() {
        return $this->cobrosRetencionCliente;
    }
    
    public function getImputada() {
        return sizeOf($this->cobrosRetencionCliente) > 0;
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
     * Get numeroRecibo
     *
     * @return string 
     */
    public function getNumeroRecibo() {
        return $this->numeroRecibo;
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
     * Set reciboRetencion
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboRetencion
     *
     * @return RetencionCliente
     */
    public function setReciboRetencion(\ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza $reciboRetencion = null)
    {
        $this->reciboRetencion = $reciboRetencion;

        return $this;
    }

    /**
     * Get reciboRetencion
     *
     * @return \ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza
     */
    public function getReciboRetencion()
    {
        return $this->reciboRetencion;
    }
}
