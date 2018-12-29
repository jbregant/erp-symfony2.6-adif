<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AlicuotaIva
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="alicuota_iva")
 * @ORM\Entity 
 */
class AlicuotaIva extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="valor", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    protected $valor;

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
     * 
     * @return type
     */
    public function __toString() {
        return $this->getValor() . ' %';
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
     * Set valor
     *
     * @param string $valor
     * @return AlicuotaIva
     */
    public function setValor($valor) {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor() {
        return $this->valor;
    }

    /**
     * Set cuentaContableCredito
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContableCredito
     * @return AlicuotaIva
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
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContableDebito
     * @return AlicuotaIva
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
