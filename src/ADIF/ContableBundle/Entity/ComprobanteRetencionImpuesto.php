<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ComprobanteRetencionImpuesto
 *
 * @author Manuel Becerra
 * created 04/11/2014
 * 
 * @ORM\Table(name="comprobante_retencion_impuesto")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteRetencionImpuestoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "general" = "ComprobanteRetencionImpuesto",
 *      "compras" = "ComprobanteRetencionImpuestoCompras",
 *      "obras" = "ComprobanteRetencionImpuestoObras",
 *      "consultoria" = "ComprobanteRetencionImpuestoConsultoria"
 * })
 */
class ComprobanteRetencionImpuesto extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenPago", inversedBy="retenciones")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=true)
     */
    protected $ordenPago;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoComprobanteRetencionImpuesto
     *
     * @ORM\ManyToOne(targetEntity="EstadoComprobanteRetencionImpuesto")
     * @ORM\JoinColumn(name="id_estado_comprobante_retencion_impuesto", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoComprobanteRetencionImpuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false, options={"default": 0})
     */
    protected $monto;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_regimen_retencion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $regimenRetencion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_comprobante_retencion", type="datetime", nullable=true)
     */
    protected $fechaComprobanteRetencion;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_comprobante_retencion", type="integer", length=8, nullable=true)
     */
    protected $numeroComprobanteRetencion;

    /**
     * @ORM\OneToOne(targetEntity="ADIF\ContableBundle\Entity\RenglonDeclaracionJurada", cascade={"persist"})
     * @ORM\JoinColumn(name="id_renglon_declaracion_jurada", referencedColumnName="id", nullable=true)
     * */
    protected $renglonDeclaracionJurada;

    /**
     * @var decimal
     *
     * @ORM\Column(name="base_imponible", type="decimal", precision=15, scale=2, nullable=true, options={"default": 0})
     */
    protected $baseImponible;
	
	/**
     * @var decimal
     *
     * @ORM\Column(name="base_imponible_ganancias_ute", type="decimal", precision=15, scale=2, nullable=true, options={"default": 0})
     */
    protected $baseImponibleGananciasUte;
	
	/**
     * @ORM\ManyToMany(targetEntity="OrdenPagoLog", mappedBy="comprobantes", cascade={"all"})
     */
    protected $ordenPagoLog;
	
	public function __construct()
	{
		$this->ordenPagoLog = new ArrayCollection();
	}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param type $ordenPago
     * @return \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto
     */
    public function setOrdenPago($ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return OrdenPago
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return ComprobanteRetencionImpuesto
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
     * Set estadoComprobanteRetencionImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\EstadoComprobanteRetencionImpuesto $estadoComprobanteRetencionImpuesto
     * @return ComprobanteRetencionImpuesto
     */
    public function setEstadoComprobanteRetencionImpuesto(\ADIF\ContableBundle\Entity\EstadoComprobanteRetencionImpuesto $estadoComprobanteRetencionImpuesto) {
        $this->estadoComprobanteRetencionImpuesto = $estadoComprobanteRetencionImpuesto;

        return $this;
    }

    /**
     * Get estadoComprobanteRetencionImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\EstadoComprobanteRetencionImpuesto 
     */
    public function getEstadoComprobanteRetencionImpuesto() {
        return $this->estadoComprobanteRetencionImpuesto;
    }

    /**
     * Set regimenRetencion
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion
     * @return ComprobanteRetencionImpuesto
     */
    public function setRegimenRetencion(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencion) {
        $this->regimenRetencion = $regimenRetencion;

        return $this;
    }

    /**
     * Get regimenRetencion
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencion() {
        return $this->regimenRetencion;
    }

    /**
     * Set fechaComprobanteRetencion
     *
     * @param \DateTime $fechaComprobanteRetencion
     * @return ComprobanteRetencionImpuesto
     */
    public function setFechaComprobanteRetencion($fechaComprobanteRetencion) {
        $this->fechaComprobanteRetencion = $fechaComprobanteRetencion;

        return $this;
    }

    /**
     * Get fechaComprobanteRetencion
     *
     * @return \DateTime 
     */
    public function getFechaComprobanteRetencion() {
        return $this->fechaComprobanteRetencion;
    }

    /**
     * Set numeroComprobanteRetencion
     *
     * @param \intenger $numeroComprobanteRetencion
     * @return ComprobanteRetencionImpuesto
     */
    public function setNumeroComprobanteRetencion($numeroComprobanteRetencion) {
        $this->numeroComprobanteRetencion = $numeroComprobanteRetencion;

        return $this;
    }

    /**
     * Get numeroComprobanteRetencion
     *
     * @return \intenger 
     */
    public function getNumeroComprobanteRetencion() {
        return $this->numeroComprobanteRetencion;
    }

    /**
     * Set renglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada
     * @return ComprobanteRetencionImpuesto
     */
    public function setRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada = null) {
        $this->renglonDeclaracionJurada = $renglonDeclaracionJurada;

        return $this;
    }

    /**
     * Get renglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     */
    public function getRenglonDeclaracionJurada() {
        return $this->renglonDeclaracionJurada;
    }

    /**
     * Set baseImponible
     *
     * @param string $baseImponible
     * @return ComprobanteRetencionImpuesto
     */
    public function setBaseImponible($baseImponible) {
        $this->baseImponible = $baseImponible;

        return $this;
    }

    /**
     * Get baseImponible
     *
     * @return string 
     */
    public function getBaseImponible() {
        return $this->baseImponible;
    }
	
	
	public function addOrdenPagoLog($ordenPagoLog)
	{
		$this->ordenPagoLog->add($ordenPagoLog);
		
		return $this;
	}
	
	public function removeOrdenPagoLog($ordenPagoLog)
	{
		$this->ordenPagoLog->removeElement($ordenPagoLog);
		
		return $this;
	}
	
	public function getOrdenPagoLog()
	{
		return $this->ordenPagoLog;
	}
	
	/**
     * Set baseImponible
     *
     * @param string $baseImponible
     * @return ComprobanteRetencionImpuesto
     */
    public function setBaseImponibleGananciasUte($baseImponible) {
        $this->baseImponibleGananciasUte = $baseImponible;

        return $this;
    }

    /**
     * Get baseImponible
     *
     * @return string 
     */
    public function getBaseImponibleGananciasUte() {
        return $this->baseImponibleGananciasUte;
    }

}
