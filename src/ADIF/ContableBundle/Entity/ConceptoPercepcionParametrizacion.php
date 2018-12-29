<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConceptoPercepcionParametrizacion
 * 
 * @author Manuel Becerra
 * created 23/10/2014
 * 
 * @ORM\Table(name="concepto_percepcion_parametrizacion")
 * @ORM\Entity 
 */
class ConceptoPercepcionParametrizacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ConceptoPercepcion")
     * @ORM\JoinColumn(name="id_concepto_percepcion", referencedColumnName="id", nullable=false)
     */
    protected $conceptoPercepcion;

    /**
     * @ORM\ManyToOne(targetEntity="Jurisdiccion")
     * @ORM\JoinColumn(name="id_jurisdiccion", referencedColumnName="id", nullable=true)
     */
    protected $jurisdiccion;

    /**
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable_credito", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContableCredito;
    
    /**
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable_debito", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContableDebito;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set conceptoPercepcion
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoPercepcion $conceptoPercepcion
     * @return ConceptoPercepcionParametrizacion
     */
    public function setConceptoPercepcion(\ADIF\ContableBundle\Entity\ConceptoPercepcion $conceptoPercepcion) {
        $this->conceptoPercepcion = $conceptoPercepcion;

        return $this;
    }

    /**
     * Get conceptoPercepcion
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoPercepcion 
     */
    public function getConceptoPercepcion() {
        return $this->conceptoPercepcion;
    }

    /**
     * Set jurisdiccion
     *
     * @param \ADIF\ContableBundle\Entity\Jurisdiccion $jurisdiccion
     * @return ConceptoPercepcionParametrizacion
     */
    public function setJurisdiccion(\ADIF\ContableBundle\Entity\Jurisdiccion $jurisdiccion) {
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return \ADIF\ContableBundle\Entity\Jurisdiccion 
     */
    public function getJurisdiccion() {
        return $this->jurisdiccion;
    }

    /**
     * Set cuentaContableCredito
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContableCredito
     * @return ConceptoPercepcionParametrizacion
     */
    public function setCuentaContableCredito(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContableCredito) {
        $this->cuentaContableCredito = $cuentaContableCredito;

        return $this;
    }

    /**
     * Get cuentaContableCredito
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContableCredito() {
        return $this->cuentaContableCredito;
    }

    /**
     * Set cuentaContableDebito
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContableDebito
     * @return ConceptoPercepcionParametrizacion
     */
    public function setCuentaContableDebito(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContableDebito) {
        $this->cuentaContableDebito = $cuentaContableDebito;

        return $this;
    }

    /**
     * Get cuentaContableDebito
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContableDebito() {
        return $this->cuentaContableDebito;
    }
}
