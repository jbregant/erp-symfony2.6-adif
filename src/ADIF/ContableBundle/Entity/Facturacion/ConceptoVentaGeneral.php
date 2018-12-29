<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConceptoVentaGeneral
 * 
 * @author Manuel Becerra
 * created 17/07/2015
 * 
 * @ORM\Table(name="concepto_venta_general")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class ConceptoVentaGeneral extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_ingreso", referencedColumnName="id", nullable=false)
     */
    protected $cuentaIngreso;
	
	/**
	* @var boolean
	* @ORM\Column(name="activo", type="boolean", nullable=false)
	*/
	protected $activo;
	
	public function __construct()
	{
		$this->activo = true;
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
     * @return ConceptoVentaGeneral
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
     * @return ConceptoVentaGeneral
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
     * @return ConceptoVentaGeneral
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
     * Set cuentaIngreso
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return ConceptoVentaGeneral
     */
    public function setCuentaIngreso(\ADIF\ContableBundle\Entity\CuentaContable $cuentaIngreso) {
        $this->cuentaIngreso = $cuentaIngreso;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaIngreso() {
        return $this->cuentaIngreso;
    }
	
	public function setActivo($activo)
	{
		$this->activo = $activo;
		
		return $this;
	}
	
	public function getActivo()
	{
		return $this->activo;
	}

}
