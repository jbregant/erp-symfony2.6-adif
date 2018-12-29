<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Define los distintos tipos de ConceptoEgresoValor:
 *  
 *
 * @author Manuel Becerra
 * created 14/01/2015
 * 
 * @ORM\Table(name="concepto_egreso_valor")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class ConceptoEgresoValor extends BaseAuditoria implements BaseAuditable {

    /**
     * BIEN_CONSUMO
     */
    const BIEN_CONSUMO = 'Bienes de consumo';

    /**
     * SERVICIO_NO_PERSONAL
     */
    const SERVICIO_NO_PERSONAL = 'Servicios no personales';

    /**
     * CLASE_BIEN_CONSUMO
     */
    const CLASE_BIEN_CONSUMO = 'A';

    /**
     * CLASE_SERVICIO_NO_PERSONAL
     */
    const CLASE_SERVICIO_NO_PERSONAL = 'B';

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
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_bien_consumo", type="boolean", nullable=false)
     */
    protected $esBienDeConsumo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_rendicion_caja_chica", type="boolean", nullable=false)
     */
    protected $esRendicionCajaChica;

    /**
     * Constructor
     */
    public function __construct() {

        $this->esBienDeConsumo = true;
        $this->esRendicionCajaChica = false;
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
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return ConceptoEgresoValor
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
     * @return ConceptoEgresoValor
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
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return ConceptoEgresoValor
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
     * Set esBienDeConsumo
     *
     * @param boolean $esBienDeConsumo
     * @return ConceptoEgresoValor
     */
    public function setEsBienDeConsumo($esBienDeConsumo) {
        $this->esBienDeConsumo = $esBienDeConsumo;

        return $this;
    }

    /**
     * Get esBienDeConsumo
     *
     * @return boolean 
     */
    public function getEsBienDeConsumo() {
        return $this->esBienDeConsumo;
    }

    /**
     * Set esRendicionCajaChica
     *
     * @param boolean $esRendicionCajaChica
     * @return ConceptoEgresoValor
     */
    public function setEsRendicionCajaChica($esRendicionCajaChica) {
        $this->esRendicionCajaChica = $esRendicionCajaChica;

        return $this;
    }

    /**
     * Get esRendicionCajaChica
     *
     * @return boolean 
     */
    public function getEsRendicionCajaChica() {
        return $this->esRendicionCajaChica;
    }

    /**
     * 
     * @return string
     */
    public function getClase() {

        if ($this->esBienDeConsumo) {
            return self::CLASE_BIEN_CONSUMO;
        } else {
            return self::CLASE_SERVICIO_NO_PERSONAL;
        }
    }

    /**
     * 
     * @return type
     */
    public function getRubro() {

        if ($this->esBienDeConsumo) {
            return self::BIEN_CONSUMO;
        } else {
            return self::SERVICIO_NO_PERSONAL;
        }
    }

}
