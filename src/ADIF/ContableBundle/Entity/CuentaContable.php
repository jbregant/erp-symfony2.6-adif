<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioDisponibilidades;

/**
 * CuentaContable
 *
 * @author Manuel Becerra
 * created 25/06/2014
 * 
 * @ORM\Table(name="cuenta_contable")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuentaContableRepository")
 * @UniqueEntity(fields={"codigoCuentaContable"}, message="El código de cuenta contable ya se encuentra en uso.")
 */
class CuentaContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoCuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionCuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionCuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_imputable", type="boolean", nullable=false)
     */
    protected $esImputable;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaContable
     *
     * @ORM\ManyToOne(targetEntity="CuentaContable", inversedBy="cuentasContablesHijas" )
     * @ORM\JoinColumn(name="id_cuenta_contable_padre", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"codigoCuentaContable" = "ASC"})
     */
    protected $cuentaContablePadre;

    /**
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="cuentaContablePadre")
     */
    protected $cuentasContablesHijas;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaObjetoGasto")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_objeto_gasto", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cuentaPresupuestariaObjetoGasto;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoMoneda
     *
     * @ORM\ManyToOne(targetEntity="TipoMoneda")
     * @ORM\JoinColumn(name="id_tipo_moneda", referencedColumnName="id")
     * 
     */
    protected $tipoMoneda;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoCuentaContable
     *
     * @ORM\ManyToOne(targetEntity="EstadoCuentaContable", inversedBy="cuentasContables")
     * @ORM\JoinColumn(name="id_estado_cuenta_contable", referencedColumnName="id")
     * 
     */
    protected $estadoCuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_cuenta_financiamiento", type="boolean", nullable=false)
     */
    protected $esCuentaFinanciamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_interno", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código interno no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoInterno;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ConceptoPresupuestarioRemuneracion", inversedBy="cuentasContables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_presupuestario_remuneracion", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoPresupuestarioRemuneracion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ConceptoPresupuestarioServiciosNoPersonales", inversedBy="cuentasContables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_presupuestario_servicios_no_personales", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoPresupuestarioServiciosNoPersonales;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ConceptoPresupuestarioNivelVentas", inversedBy="cuentasContables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_presupuestario_nivel_ventas", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoPresupuestarioNivelVentas;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ConceptoPresupuestarioDisponibilidades", inversedBy="cuentasContables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_presupuestario_disponibilidades", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conceptoPresupuestarioDisponibilidades;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean", nullable=false, options={"default": 1})
     */
    protected $activa;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esImputable = false;
        $this->esCuentaFinanciamiento = false;
        $this->cuentasContablesHijas = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->codigoCuentaContable . " - " . $this->denominacionCuentaContable;
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
     * Set codigoCuentaContable
     *
     * @param string $codigoCuentaContable
     * @return CuentaContable
     */
    public function setCodigoCuentaContable($codigoCuentaContable) {
        $this->codigoCuentaContable = $codigoCuentaContable;

        return $this;
    }

    /**
     * Get codigoCuentaContable
     *
     * @return string 
     */
    public function getCodigoCuentaContable() {
        return $this->codigoCuentaContable;
    }

    /**
     * Set denominacionCuentaContable
     *
     * @param string $denominacionCuentaContable
     * @return CuentaContable
     */
    public function setDenominacionCuentaContable($denominacionCuentaContable) {
        $this->denominacionCuentaContable = $denominacionCuentaContable;

        return $this;
    }

    /**
     * Get denominacionCuentaContable
     *
     * @return string 
     */
    public function getDenominacionCuentaContable() {
        return $this->denominacionCuentaContable;
    }

    /**
     * Set descripcionCuentaContable
     *
     * @param string $descripcionCuentaContable
     * @return CuentaContable
     */
    public function setDescripcionCuentaContable($descripcionCuentaContable) {
        $this->descripcionCuentaContable = $descripcionCuentaContable;

        return $this;
    }

    /**
     * Get descripcionCuentaContable
     *
     * @return string 
     */
    public function getDescripcionCuentaContable() {
        return $this->descripcionCuentaContable;
    }

    /**
     * Set esImputable
     *
     * @param boolean $esImputable
     * @return CuentaContable
     */
    public function setEsImputable($esImputable) {
        $this->esImputable = $esImputable;

        return $this;
    }

    /**
     * Get esImputable
     *
     * @return boolean 
     */
    public function getEsImputable() {
        return $this->esImputable;
    }

    /**
     * Set tipoMoneda
     *
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     * @return CuentaContable
     */
    public function setTipoMoneda(\ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda = null) {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return \ADIF\ContableBundle\Entity\TipoMoneda 
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Set estadoCuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\EstadoCuentaContable $estadoCuentaContable
     * @return CuentaContable
     */
    public function setEstadoCuentaContable(\ADIF\ContableBundle\Entity\EstadoCuentaContable $estadoCuentaContable = null) {
        $this->estadoCuentaContable = $estadoCuentaContable;

        return $this;
    }

    /**
     * Get estadoCuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\EstadoCuentaContable 
     */
    public function getEstadoCuentaContable() {
        return $this->estadoCuentaContable;
    }

    /**
     * Set cuentaContablePadre
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return CuentaContable
     */
    public function setCuentaContablePadre(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable = null) {
        $this->cuentaContablePadre = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContablePadre
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContablePadre() {
        return $this->cuentaContablePadre;
    }

    /**
     * Add cuentasContablesHijas
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return CuentaContable
     */
    public function addCuentasContablesHija(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentasContablesHijas[] = $cuentaContable;

        return $this;
    }

    /**
     * Remove cuentasContablesHijas
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function removeCuentasContablesHija(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentasContablesHijas->removeElement($cuentaContable);
    }

    /**
     * Get cuentasContablesHijas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasContablesHijas() {
        return $this->cuentasContablesHijas;
    }

    /**
     * Retorna true si la CuentaContable es raiz. Caso contrario
     * retorna false.
     * 
     * @return type boolean
     */
    public function getEsRaiz() {
        return null == $this->cuentaContablePadre;
    }

    /**
     * Retorna el nivel de la Cuenta Contable.
     * 
     * @return int
     */
    public function getNivel() {

        if (null != $this->cuentaContablePadre) {
            return $this->cuentaContablePadre->getNivel() + 1;
        } //.
        else {
            return 1;
        }
    }

    /**
     * Retorna el código inicial de la Cuenta. Por ejemplo:
     * 
     * Si la Cuenta Contable es de nivel 3 y su código es 1.1.2.0.0.0
     * El método retorna 1.1.2
     * 
     * @return string
     */
    public function getCodigoInicial() {

        $codigoInicial = "";

        $index = 0;

        $segmentoArray = explode('.', $this->getCodigoCuentaContable());

        foreach ($segmentoArray as $caracter) {
            $index++;
            if (intval($caracter) != 0) {
                $nivel = $index;
            }
        }

        for ($i = 0; $i < $nivel; $i++) {
            $codigoInicial .= $segmentoArray[$i] . '.';
        }

        return $codigoInicial;
    }

    /**
     * Retorna en un array, todos los id de las CuentaContable padres, llegando 
     * hasta la respectiva CuentaContable raiz.
     * 
     * @return array
     */
    public function getCuentaContablePadreIds() {

        $cuentasPadresIds = array();

        if (!$this->getEsRaiz()) {
            $cuentasPadresIds = array_merge_recursive(
                    $this->cuentaContablePadre->getCuentaContablePadreIds(), //
                    array($this->cuentaContablePadre->getId()));
        }

        return $cuentasPadresIds;
    }

    /**
     * Set cuentaPresupuestariaEconomica
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return CuentaContable
     */
    public function setCuentaPresupuestariaEconomica(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica = null) {
        $this->cuentaPresupuestariaEconomica = $cuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaEconomica
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica 
     */
    public function getCuentaPresupuestariaEconomica() {
        return $this->cuentaPresupuestariaEconomica;
    }

    /**
     * Set cuentaPresupuestariaObjetoGasto
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentaPresupuestariaObjetoGasto
     * @return CuentaContable
     */
    public function setCuentaPresupuestariaObjetoGasto(\ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentaPresupuestariaObjetoGasto = null) {
        $this->cuentaPresupuestariaObjetoGasto = $cuentaPresupuestariaObjetoGasto;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaObjetoGasto
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto 
     */
    public function getCuentaPresupuestariaObjetoGasto() {
        return $this->cuentaPresupuestariaObjetoGasto;
    }

    /**
     * Set esCuentaFinanciamiento
     *
     * @param boolean $esCuentaFinanciamiento
     * @return CuentaContable
     */
    public function setEsCuentaFinanciamiento($esCuentaFinanciamiento) {
        $this->esCuentaFinanciamiento = $esCuentaFinanciamiento;

        return $this;
    }

    /**
     * Get esCuentaFinanciamiento
     *
     * @return boolean 
     */
    public function getEsCuentaFinanciamiento() {
        return $this->esCuentaFinanciamiento;
    }

    /**
     * Set codigoInterno
     *
     * @param string $codigoInterno
     * @return CuentaContable
     */
    public function setCodigoInterno($codigoInterno) {
        $this->codigoInterno = $codigoInterno;

        return $this;
    }

    /**
     * Get codigoInterno
     *
     * @return string 
     */
    public function getCodigoInterno() {
        return $this->codigoInterno;
    }

    /**
     * Get segmentoOrden
     * @param integer $ordenSegmento
     * @return integer
     */
    public function getSegmentoOrden($ordenSegmento) {

        $segmentoArray = explode('.', $this->getCodigoCuentaContable());

        return $segmentoArray[$ordenSegmento - 1];
    }

    /**
     * Set segmentoOrden
     * @param integer $ordenSegmento
     * @param integer $valor
     * @return CuentaContable
     */
    public function setSegmentoOrden($ordenSegmento, $valor) {

        $segmentoArray = explode('.', $this->getCodigoCuentaContable());
        $segmentoArray[$ordenSegmento - 1] = $valor;

        $this->setCodigoCuentaContable(implode('.', $segmentoArray));

        return $this;
    }

    /**
     * Set conceptoPresupuestarioRemuneracion
     *
     * @param $conceptoPresupuestarioRemuneracion
     * @return CuentaContable
     */
    public function setConceptoPresupuestarioRemuneracion($conceptoPresupuestarioRemuneracion) {
        $this->conceptoPresupuestarioRemuneracion = $conceptoPresupuestarioRemuneracion;

        return $this;
    }

    /**
     * Get conceptoPresupuestarioRemuneracion
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion 
     */
    public function getConceptoPresupuestarioRemuneracion() {
        return $this->conceptoPresupuestarioRemuneracion;
    }

    /**
     * Set conceptoPresupuestarioServiciosNoPersonales
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales $conceptoPresupuestarioServiciosNoPersonales
     * @return CuentaContable
     */
    public function setConceptoPresupuestarioServiciosNoPersonales($conceptoPresupuestarioServiciosNoPersonales) {
        $this->conceptoPresupuestarioServiciosNoPersonales = $conceptoPresupuestarioServiciosNoPersonales;

        return $this;
    }

    /**
     * Get conceptoPresupuestarioServiciosNoPersonales
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales 
     */
    public function getConceptoPresupuestarioServiciosNoPersonales() {
        return $this->conceptoPresupuestarioServiciosNoPersonales;
    }

    /**
     * Set conceptoPresupuestarioNivelVentas
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas $conceptoPresupuestarioNivelVentas
     * @return CuentaContable
     */
    public function setConceptoPresupuestarioNivelVentas($conceptoPresupuestarioNivelVentas) {
        $this->conceptoPresupuestarioNivelVentas = $conceptoPresupuestarioNivelVentas;

        return $this;
    }

    /**
     * Get conceptoPresupuestarioNivelVentas
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas 
     */
    public function getConceptoPresupuestarioNivelVentas() {
        return $this->conceptoPresupuestarioNivelVentas;
    }

    public function getCodigoCuentaContableOrden() {
        $segmentoArray = explode('.', $this->codigoCuentaContable);
        $segmentoArray[2] = '00';
        return implode('.', $segmentoArray);
    }

    /**
     * Set conceptoPresupuestarioDisponibilidades
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDisponibilidades $conceptoPresupuestarioDisponibilidades
     * @return CuentaContable
     */
    public function setConceptoPresupuestarioDisponibilidades($conceptoPresupuestarioDisponibilidades) {
        $this->conceptoPresupuestarioDisponibilidades = $conceptoPresupuestarioDisponibilidades;

        return $this;
    }

    /**
     * Get conceptoPresupuestarioDisponibilidades
     *
     * @return \ADIF\ContableBundle\Entity\conceptoPresupuestarioDisponibilidades 
     */
    public function getConceptoPresupuestarioDisponibilidades() {
        return $this->conceptoPresupuestarioDisponibilidades;
    }
    
    /**
     * Set activa
     *
     * @param boolean $activa
     * @return CuentaContable
     */
    public function setActiva($activa) {
        $this->activa = $activa;

        return $this;
    }

    /**
     * Get activa
     *
     * @return boolean 
     */
    public function getActiva() {
        return $this->activa;
    }

}
