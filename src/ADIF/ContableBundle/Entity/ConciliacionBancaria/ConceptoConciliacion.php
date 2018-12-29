<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConceptoConciliacion
 *
 * @author Augusto Villa Monte
 * created 08/01/2015
 * 
 * @ORM\Table(name="conciliacion_bancaria_concepto_conciliacion")
 * @ORM\Entity
 */
class ConceptoConciliacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    protected $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_contabilizable", type="boolean", nullable=false)
     */
    protected $esContabilizable;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="destino_iva_compras", type="string", length=20, nullable=true)
     */
    protected $destinoIvaCompras;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", nullable=true)
     */
    protected $codigo;

    /**
     * 
     */
    public function __construct() {
        $this->esContabilizable = false;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->denominacion;
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return ConceptoConciliacion
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
     * @return ConceptoConciliacion
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
     * Set esContabilizable
     *
     * @param boolean $esContabilizable
     * @return ConceptoConciliacion
     */
    public function setEsContabilizable($esContabilizable) {
        $this->esContabilizable = $esContabilizable;

        return $this;
    }

    /**
     * Get esContabilizable
     *
     * @return boolean 
     */
    public function getEsContabilizable() {
        return $this->esContabilizable;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return ConceptoConciliacion
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
     * Set destinoIvaCompras
     *
     * @param string $destinoIvaCompras
     * @return ConceptoConciliacion
     */
    public function setDestinoIvaCompras($destinoIvaCompras) {
        $this->destinoIvaCompras = $destinoIvaCompras;

        return $this;
    }

    /**
     * Get destinoIvaCompras
     *
     * @return string 
     */
    public function getDestinoIvaCompras() {
        return $this->destinoIvaCompras;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return ConceptoConciliacion
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

}
