<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ConceptoPresupuestarioDisponibilidades
 * 
 * @ORM\Table(name="concepto_presupuestario_disponibilidades")
 * @ORM\Entity
 */
class ConceptoPresupuestarioDisponibilidades extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="conceptoPresupuestarioDisponibilidades")
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
     * @return ConceptoPresupuestarioDisponibilidades
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
     * @return ConceptoPresupuestarioDisponibilidades
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
     * @return ConceptoPresupuestarioDisponibilidades
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $cuentasContables->setConceptoPresupuestarioDisponibilidades($this);
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
        $cuentasContables->setConceptoPresupuestarioDisponibilidades(null);
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
