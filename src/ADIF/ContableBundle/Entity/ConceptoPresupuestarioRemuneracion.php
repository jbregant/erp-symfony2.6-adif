<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioRemuneracion;

/**
 * ConceptoPresupuestarioRemuneracion
 * 
 * @ORM\Table(name="concepto_presupuestario_remuneracion")
 * @ORM\Entity
 */
class ConceptoPresupuestarioRemuneracion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoConceptoPresupuestarioRemuneracion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_concepto_presupuestario_remuneracion", referencedColumnName="id", nullable=true)
     * })
     */
    protected $tipoConceptoPresupuestarioRemuneracion;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionConceptoPresupuestarioRemuneracion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionConceptoPresupuestarioRemuneracion;

    /**
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="conceptoPresupuestarioRemuneracion")
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
     * Set denominacionConceptoPresupuestarioRemuneracion
     *
     * @param string $denominacionConceptoPresupuestarioRemuneracion
     * @return ConceptoPresupuestarioRemuneracion
     */
    public function setDenominacionConceptoPresupuestarioRemuneracion($denominacionConceptoPresupuestarioRemuneracion) {
        $this->denominacionConceptoPresupuestarioRemuneracion = $denominacionConceptoPresupuestarioRemuneracion;

        return $this;
    }

    /**
     * Get denominacionConceptoPresupuestarioRemuneracion
     *
     * @return string 
     */
    public function getDenominacionConceptoPresupuestarioRemuneracion() {
        return $this->denominacionConceptoPresupuestarioRemuneracion;
    }

    /**
     * Set descripcionConceptoPresupuestarioRemuneracion
     *
     * @param string $descripcionConceptoPresupuestarioRemuneracion
     * @return ConceptoPresupuestarioRemuneracion
     */
    public function setDescripcionConceptoPresupuestarioRemuneracion($descripcionConceptoPresupuestarioRemuneracion) {
        $this->descripcionConceptoPresupuestarioRemuneracion = $descripcionConceptoPresupuestarioRemuneracion;

        return $this;
    }

    /**
     * Get descripcionConceptoPresupuestarioRemuneracion
     *
     * @return string 
     */
    public function getDescripcionConceptoPresupuestarioRemuneracion() {
        return $this->descripcionConceptoPresupuestarioRemuneracion;
    }

    /**
     * Set tipoConceptoPresupuestarioRemuneracion
     *
     * @param \ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioRemuneracion $tipoConceptoPresupuestarioRemuneracion
     * @return ConceptoPresupuestarioRemuneracion
     */
    public function setTipoConceptoPresupuestarioRemuneracion(\ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioRemuneracion $tipoConceptoPresupuestarioRemuneracion) {
        $this->tipoConceptoPresupuestarioRemuneracion = $tipoConceptoPresupuestarioRemuneracion;

        return $this;
    }

    /**
     * Get tipoConceptoPresupuestarioRemuneracion
     *
     * @return \ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioRemuneracion 
     */
    public function getTipoConceptoPresupuestarioRemuneracion() {
        return $this->tipoConceptoPresupuestarioRemuneracion;
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
     * @return ConceptoPresupuestarioRemuneracion
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
        $cuentasContables->setConceptoPresupuestarioRemuneracion($this);
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
        $cuentasContables->setConceptoPresupuestarioRemuneracion(null);
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
