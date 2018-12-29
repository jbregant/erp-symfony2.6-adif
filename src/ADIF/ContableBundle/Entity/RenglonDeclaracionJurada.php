<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * RenglonDeclaracionJurada
 * 
 * Indica el renglón de la DeclaracionJurada
 *
 * @author Darío Rapetti
 * created 17/04/2015
 * 
 * @ORM\Table(name="renglon_declaracion_jurada")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\RenglonDeclaracionJuradaRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "liquidacion" = "RenglonDeclaracionJuradaLiquidacion",
 *      "renglon_percepcion" = "RenglonDeclaracionJuradaRenglonPercepcion",
 *      "comprobante_retencion_impuesto" = "RenglonDeclaracionJuradaComprobanteRetencionImpuesto" 
 * })
 */
abstract class RenglonDeclaracionJurada extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoRenglonDeclaracionJurada
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\TipoRenglonDeclaracionJurada")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_renglon_declaracion_jurada", referencedColumnName="id", nullable=false)
     * })
     */
    private $tipoRenglonDeclaracionJurada;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoRenglonDeclaracionJurada
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EstadoRenglonDeclaracionJurada")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_renglon_declaracion_jurada", referencedColumnName="id", nullable=false)
     * })
     */
    private $estadoRenglonDeclaracionJurada;

    /**
     * @var string
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    private $monto;
    
    /**
     * @var string
     * @ORM\Column(name="monto_original", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    private $montoOriginal;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\TipoImpuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_impuesto", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoImpuesto;

    /**
     * @ORM\ManyToMany(targetEntity="PagoACuenta", mappedBy="renglonesDeclaracionJurada")
     * */
    private $pagosACuenta;
    
    /**
     * @ORM\OneToMany(targetEntity="DevolucionRenglonDeclaracionJurada", mappedBy="renglonDeclaracionJurada", cascade={"all"})
     */
    protected $devoluciones;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return '';
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->pagosACuenta = new \Doctrine\Common\Collections\ArrayCollection();
        $this->devoluciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set monto
     *
     * @param string $monto
     * @return RenglonDeclaracionJurada
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
     * Set montoOriginal
     *
     * @param string $montoOriginal
     * @return RenglonDeclaracionJurada
     */
    public function setMontoOriginal($montoOriginal) {
        $this->montoOriginal = $montoOriginal;

        return $this;
    }

    /**
     * Get montoOriginal
     *
     * @return string 
     */
    public function getMontoOriginal() {
        return $this->montoOriginal;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RenglonDeclaracionJurada
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
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto
     * @return RenglonDeclaracionJurada
     */
    public function setTipoImpuesto(\ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto) {
        $this->tipoImpuesto = $tipoImpuesto;

        return $this;
    }

    /**
     * Get tipoImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\TipoImpuesto 
     */
    public function getTipoImpuesto() {
        return $this->tipoImpuesto;
    }

    /**
     * Set tipoRenglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\TipoRenglonDeclaracionJurada $tipoRenglonDeclaracionJurada
     * @return RenglonDeclaracionJurada
     */
    public function setTipoRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\TipoRenglonDeclaracionJurada $tipoRenglonDeclaracionJurada) {
        $this->tipoRenglonDeclaracionJurada = $tipoRenglonDeclaracionJurada;

        return $this;
    }

    /**
     * Get tipoRenglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\TipoRenglonDeclaracionJurada 
     */
    public function getTipoRenglonDeclaracionJurada() {
        return $this->tipoRenglonDeclaracionJurada;
    }

    /**
     * Set estadoRenglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\EstadoRenglonDeclaracionJurada $estadoRenglonDeclaracionJurada
     * @return RenglonDeclaracionJurada
     */
    public function setEstadoRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\EstadoRenglonDeclaracionJurada $estadoRenglonDeclaracionJurada) {
        $this->estadoRenglonDeclaracionJurada = $estadoRenglonDeclaracionJurada;

        return $this;
    }

    /**
     * Get estadoRenglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\EstadoRenglonDeclaracionJurada 
     */
    public function getEstadoRenglonDeclaracionJurada() {
        return $this->estadoRenglonDeclaracionJurada;
    }

    /**
     * Add pagosACuenta
     *
     * @param \ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta
     * @return RenglonDeclaracionJurada
     */
    public function addPagosACuentum(\ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta) {
        $this->pagosACuenta[] = $pagosACuenta;

        return $this;
    }

    /**
     * Remove pagosACuenta
     *
     * @param \ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta
     */
    public function removePagosACuentum(\ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta) {
        $this->pagosACuenta->removeElement($pagosACuenta);
    }

    /**
     * Get pagosACuenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPagosACuenta() {
        return $this->pagosACuenta;
    }

    /**
     * 
     * @return type
     */
    public function getOrdenPago() {

        return null;
    }


    /**
     * Add devoluciones
     *
     * @param \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devoluciones
     * @return RenglonDeclaracionJurada
     */
    public function addDevolucione(\ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devoluciones)
    {
        $this->devoluciones[] = $devoluciones;

        return $this;
    }

    /**
     * Remove devoluciones
     *
     * @param \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devoluciones
     */
    public function removeDevolucione(\ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada $devoluciones)
    {
        $this->devoluciones->removeElement($devoluciones);
    }

    /**
     * Get devoluciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevoluciones()
    {
        return $this->devoluciones;
    }
}
