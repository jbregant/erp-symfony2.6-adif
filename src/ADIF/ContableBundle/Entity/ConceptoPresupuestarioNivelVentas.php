<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioNivelVentas;

/**
 * ConceptoPresupuestarioNivelVentas
 * 
 * @ORM\Table(name="concepto_presupuestario_nivel_ventas")
 * @ORM\Entity
 */
class ConceptoPresupuestarioNivelVentas extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="conceptoPresupuestarioNivelVentas")
     */
    protected $cuentasContables;

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
     * @return ConceptoPresupuestarioNivelVentas
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
     * @return ConceptoPresupuestarioNivelVentas
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
     * Constructor
     */
    public function __construct() {
        $this->cuentasContables = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentasContables
     * @return ConceptoPresupuestarioNivelVentas
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $cuentasContables->setConceptoPresupuestarioNivelVentas($this);
        $this->cuentasContables[] = $cuentasContables;
        return $this;
    }

    /**
     * Remove cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentasContables
     */
    public function removeCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $this->cuentasContables->removeElement($cuentasContables);
        $cuentasContables->setConceptoPresupuestarioNivelVentas(null);
    }

    /**
     * Get cuentasContables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasContables() {
        return $this->cuentasContables;
    }

}
