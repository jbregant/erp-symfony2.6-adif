<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ConceptoPresupuestarioServiciosNoPersonales
 * 
 * @ORM\Table(name="concepto_presupuestario_servicios_no_personales")
 * @ORM\Entity
 */
class ConceptoPresupuestarioServiciosNoPersonales extends BaseAuditoria implements BaseAuditable {

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
     *      maxMessage="La denominación de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionConceptoPresupuestarioServiciosNoPersonales;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionConceptoPresupuestarioServiciosNoPersonales;

    /**
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="conceptoPresupuestarioServiciosNoPersonales", cascade={"all"})
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
     * Set denominacionConceptoPresupuestarioServiciosNoPersonales
     *
     * @param string $denominacionConceptoPresupuestarioServiciosNoPersonales
     * @return ConceptoPresupuestarioServiciosNoPersonales
     */
    public function setDenominacionConceptoPresupuestarioServiciosNoPersonales($denominacionConceptoPresupuestarioServiciosNoPersonales) {
        $this->denominacionConceptoPresupuestarioServiciosNoPersonales = $denominacionConceptoPresupuestarioServiciosNoPersonales;

        return $this;
    }

    /**
     * Get denominacionConceptoPresupuestarioServiciosNoPersonales
     *
     * @return string 
     */
    public function getDenominacionConceptoPresupuestarioServiciosNoPersonales() {
        return $this->denominacionConceptoPresupuestarioServiciosNoPersonales;
    }

    /**
     * Set descripcionConceptoPresupuestarioServiciosNoPersonales
     *
     * @param string $descripcionConceptoPresupuestarioServiciosNoPersonales
     * @return ConceptoPresupuestarioServiciosNoPersonales
     */
    public function setDescripcionConceptoPresupuestarioServiciosNoPersonales($descripcionConceptoPresupuestarioServiciosNoPersonales) {
        $this->descripcionConceptoPresupuestarioServiciosNoPersonales = $descripcionConceptoPresupuestarioServiciosNoPersonales;

        return $this;
    }

    /**
     * Get descripcionConceptoPresupuestarioServiciosNoPersonales
     *
     * @return string 
     */
    public function getDescripcionConceptoPresupuestarioServiciosNoPersonales() {
        return $this->descripcionConceptoPresupuestarioServiciosNoPersonales;
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
     * @return ConceptoPresupuestarioServiciosNoPersonales
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $this->cuentasContables[] = $cuentasContables;

        $cuentasContables->setConceptoPresupuestarioServiciosNoPersonales($this);

        return $this;
    }

    /**
     * Remove cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentasContables
     */
    public function removeCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $this->cuentasContables->removeElement($cuentasContables);
        $cuentasContables->setConceptoPresupuestarioServiciosNoPersonales(null);
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
