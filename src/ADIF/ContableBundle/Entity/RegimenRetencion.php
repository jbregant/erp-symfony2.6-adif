<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of RegimenRetencion
 *
 * @author Manuel Becerra
 * created 12/11/2014
 * 
 * @ORM\Table(name="regimen_retencion")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\RegimenRetencionRepository")
 */
class RegimenRetencion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\TipoImpuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_impuesto", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoImpuesto;

    /**
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="El código no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigo;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="codigo_siap", type="integer", nullable=true)
     */
    private $codigoSiap;

    /**
     * @var float
     * 
     * @ORM\Column(name="minimo_exento", type="float", nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe mínimo exento debe ser de tipo numérico.")
     */
    protected $minimoExento;

    /**
     * @var float
     * 
     * @ORM\Column(name="minimo_no_imponible", type="float", nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe mínimo no imponible debe ser de tipo numérico.")
     */
    protected $minimoNoImponible;

    /**
     * @var float
     * 
     * @ORM\Column(name="alicuota", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La alícuota debe ser de tipo numérico.")
     */
    protected $alicuota;

    /**
     * @var float
     * 
     * @ORM\Column(name="minimo_retencion", type="float", nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe mínimo de retención debe ser de tipo numérico.")
     */
    protected $minimoRetencion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="usa_tabla", type="boolean", nullable=false)
     */
    protected $usaTabla;

    /**
     * @ORM\OneToMany(targetEntity="RegimenRetencionBienEconomico", mappedBy="regimenRetencion", cascade={"all"})
     * 
     */
    protected $regimenesRetencionBienEconomico;

    /**
     * @var boolean
     *
     * @ORM\Column(name="asociable_bien_economico", type="boolean", nullable=false)
     */
    protected $asociableBienEconomico;

    /*
     * @return type
     */

    public function __toString() {
        return $this->getDenominacionCompleta();
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->regimenesRetencionBienEconomico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usaTabla = false;
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
     * Set tipoImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto
     * @return RegimenRetencion
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
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return RegimenRetencion
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return RegimenRetencion
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return RegimenRetencion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return RegimenRetencion
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
     * Set codigoSiap
     *
     * @param string $codigoSiap
     * @return RegimenRetencion
     */
    public function setCodigoSiap($codigoSiap) {
        $this->codigoSiap = $codigoSiap;

        return $this;
    }

    /**
     * Get codigoSiap
     *
     * @return string 
     */
    public function getCodigoSiap() {
        return $this->codigoSiap;
    }

    /**
     * Set minimoExento
     *
     * @param float $minimoExento
     * @return RegimenRetencion
     */
    public function setMinimoExento($minimoExento) {
        $this->minimoExento = $minimoExento;

        return $this;
    }

    /**
     * Get minimoExento
     *
     * @return float 
     */
    public function getMinimoExento() {
        return $this->minimoExento;
    }

    /**
     * Set minimoNoImponible
     *
     * @param float $minimoNoImponible
     * @return RegimenRetencion
     */
    public function setMinimoNoImponible($minimoNoImponible) {
        $this->minimoNoImponible = $minimoNoImponible;

        return $this;
    }

    /**
     * Get minimoNoImponible
     *
     * @return float 
     */
    public function getMinimoNoImponible() {
        return $this->minimoNoImponible;
    }

    /**
     * Set alicuota
     *
     * @param float $alicuota
     * @return RegimenRetencion
     */
    public function setAlicuota($alicuota) {
        $this->alicuota = $alicuota;

        return $this;
    }

    /**
     * Get alicuota
     *
     * @return float 
     */
    public function getAlicuota() {
        return $this->alicuota;
    }

    /**
     * Set minimoRetencion
     *
     * @param float $minimoRetencion
     * @return RegimenRetencion
     */
    public function setMinimoRetencion($minimoRetencion) {
        $this->minimoRetencion = $minimoRetencion;

        return $this;
    }

    /**
     * Get minimoRetencion
     *
     * @return float 
     */
    public function getMinimoRetencion() {
        return $this->minimoRetencion;
    }

    /**
     * Add regimenesRetencionBienEconomico
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico $regimenesRetencionBienEconomico
     * @return RegimenRetencion
     */
    public function addRegimenesRetencionBienEconomico(\ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico $regimenesRetencionBienEconomico) {
        $this->regimenesRetencionBienEconomico[] = $regimenesRetencionBienEconomico;

        return $this;
    }

    /**
     * Remove regimenesRetencionBienEconomico
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico $regimenesRetencionBienEconomico
     */
    public function removeRegimenesRetencionBienEconomico(\ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico $regimenesRetencionBienEconomico) {
        $this->regimenesRetencionBienEconomico->removeElement($regimenesRetencionBienEconomico);
    }

    /**
     * Get regimenesRetencionBienEconomico
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegimenesRetencionBienEconomico() {
        return $this->regimenesRetencionBienEconomico;
    }

    /**
     * Set usaTabla
     *
     * @param boolean $usaTabla
     * @return RegimenRetencion
     */
    public function setUsaTabla($usaTabla) {
        $this->usaTabla = $usaTabla;

        return $this;
    }

    /**
     * Get usaTabla
     *
     * @return boolean 
     */
    public function getUsaTabla() {
        return $this->usaTabla;
    }

    /**
     * Set asociableBienEconomico
     *
     * @param boolean $asociableBienEconomico
     * @return RegimenRetencion
     */
    public function setAsociableBienEconomico($asociableBienEconomico) {
        $this->asociableBienEconomico = $asociableBienEconomico;

        return $this;
    }

    /**
     * Get asociableBienEconomico
     *
     * @return boolean 
     */
    public function getAsociableBienEconomico() {
        return $this->asociableBienEconomico;
    }

    /**
     * 
     * @return type
     */
    public function getDenominacionCompleta() {
        return $this->descripcion . ' - ' . $this->denominacion;
    }

}
