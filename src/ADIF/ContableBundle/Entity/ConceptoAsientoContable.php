<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ConceptoAsientoContable:
 *      CP - Asiento de compras
 *      ING - Asiento de devengamiento de Ingresos
 *      SU - Asiento devengamiento de sueldos
 *      EGR - Asiento pago a proveedores
 *      TS - Asiento de tesoreria (pagos distintos a proveedores)
 *      CC - Asiento de cobranzas
 *
 * @author Manuel Becerra
 * created 15/10/2014
 * 
 * @ORM\Table(name="concepto_asiento_contable")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class ConceptoAsientoContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\SubdiarioAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="SubdiarioAsientoContable")
     * @ORM\JoinColumn(name="id_subdiario_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $subdiarioAsientoContable;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=5, nullable=false)
     * @Assert\Length(
     *      max="5", 
     *      maxMessage="el alias no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="El c&oacute;digo no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigo;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
    }

    /**
     * Set subdiarioAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\SubdiarioAsientoContable $subdiarioAsientoContable
     * @return ConceptoAsientoContable
     */
    public function setSubdiarioAsientoContable(\ADIF\ContableBundle\Entity\SubdiarioAsientoContable $subdiarioAsientoContable = null) {
        $this->subdiarioAsientoContable = $subdiarioAsientoContable;

        return $this;
    }

    /**
     * Get subdiarioAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\SubdiarioAsientoContable 
     */
    public function getSubdiarioAsientoContable() {
        return $this->subdiarioAsientoContable;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return ConceptoAsientoContable
     */
    public function setAlias($alias) {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return ConceptoAsientoContable
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
     * @return ConceptoAsientoContable
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
     * @return ConceptoAsientoContable
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
