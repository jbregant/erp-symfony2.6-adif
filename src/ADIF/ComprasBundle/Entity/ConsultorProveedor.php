<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClienteProveedor
 *
 * @ORM\Table(name="consultor_proveedor")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "consultor_proveedor" = "ConsultorProveedor",
 *      "consultor" = "\ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor",
 *      "proveedor" = "Proveedor"
 * })
 * )
 */
class ConsultorProveedor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_tipo_pago", type="integer", nullable=true)
     */
    protected $idTipoPago;

    /**
     * @var ADIF\ContableBundle\Entity\TipoPago
     */
    protected $tipoPago;

    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=false)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_retencion_iva", type="boolean", nullable=false)
     */
    protected $pasibleRetencionIVA;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_retencion_ganancias", type="boolean", nullable=false)
     */
    protected $pasibleRetencionGanancias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_retencion_ingresos_brutos", type="boolean", nullable=false)
     */
    protected $pasibleRetencionIngresosBrutos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_retencion_suss", type="boolean", nullable=false)
     */
    protected $pasibleRetencionSUSS;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Constructor
     */
    public function __construct() {

        $this->pasibleRetencionIVA = TRUE;
        $this->pasibleRetencionGanancias = TRUE;
        $this->pasibleRetencionIngresosBrutos = TRUE;
        $this->pasibleRetencionSUSS = TRUE;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

// CUENTA CONTABLE

    /**
     * 
     * @return type
     */
    public function getIdCuentaContable() {
        return $this->idCuentaContable;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {

        if (null != $cuentaContable) {
            $this->idCuentaContable = $cuentaContable->getId();
        } //.
        else {
            $this->idCuentaContable = null;
        }

        $this->cuentaContable = $cuentaContable;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

// TIPO PAGO  

    /**
     * 
     * @return type
     */
    public function getIdTipoPago() {
        return $this->idTipoPago;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoPago $tipoPago
     */
    public function setTipoPago($tipoPago) {

        if (null != $tipoPago) {
            $this->idTipoPago = $tipoPago->getId();
        } //.
        else {
            $this->idTipoPago = null;
        }

        $this->tipoPago = $tipoPago;
    }

    /**
     * 
     * @return type
     */
    public function getTipoPago() {
        return $this->tipoPago;
    }

// TIPO MONEDA  

    /**
     * 
     * @return type
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     */
    public function setTipoMoneda($tipoMoneda) {

        if (null != $tipoMoneda) {
            $this->idTipoMoneda = $tipoMoneda->getId();
        } //.
        else {
            $this->idTipoMoneda = null;
        }

        $this->tipoMoneda = $tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {

        $tipoMoneda = "$";

        if ($this->idTipoMoneda != null) {

            $tipoMoneda = $this->getTipoMoneda()->getSimboloTipoMoneda();
        }

        return $tipoMoneda;
    }

    /**
     * Set pasibleRetencionIVA
     *
     * @param boolean $pasibleRetencionIVA
     * @return ConsultorProveedor
     */
    public function setPasibleRetencionIVA($pasibleRetencionIVA) {
        $this->pasibleRetencionIVA = $pasibleRetencionIVA;

        return $this;
    }

    /**
     * Get pasibleRetencionIVA
     *
     * @return boolean 
     */
    public function getPasibleRetencionIVA() {
        return $this->pasibleRetencionIVA;
    }

    /**
     * Set pasibleRetencionGanancias
     *
     * @param boolean $pasibleRetencionGanancias
     * @return ConsultorProveedor
     */
    public function setPasibleRetencionGanancias($pasibleRetencionGanancias) {
        $this->pasibleRetencionGanancias = $pasibleRetencionGanancias;

        return $this;
    }

    /**
     * Get pasibleRetencionGanancias
     *
     * @return boolean 
     */
    public function getPasibleRetencionGanancias() {
        return $this->pasibleRetencionGanancias;
    }

    /**
     * Set pasibleRetencionIngresosBrutos
     *
     * @param boolean $pasibleRetencionIngresosBrutos
     * @return ConsultorProveedor
     */
    public function setPasibleRetencionIngresosBrutos($pasibleRetencionIngresosBrutos) {
        $this->pasibleRetencionIngresosBrutos = $pasibleRetencionIngresosBrutos;

        return $this;
    }

    /**
     * Get pasibleRetencionIngresosBrutos
     *
     * @return boolean 
     */
    public function getPasibleRetencionIngresosBrutos() {
        return $this->pasibleRetencionIngresosBrutos;
    }

    /**
     * Set pasibleRetencionSUSS
     *
     * @param boolean $pasibleRetencionSUSS
     * @return ConsultorProveedor
     */
    public function setPasibleRetencionSUSS($pasibleRetencionSUSS) {
        $this->pasibleRetencionSUSS = $pasibleRetencionSUSS;

        return $this;
    }

    /**
     * Get pasibleRetencionSUSS
     *
     * @return boolean 
     */
    public function getPasibleRetencionSUSS() {
        return $this->pasibleRetencionSUSS;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ConsultorProveedor
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

}
