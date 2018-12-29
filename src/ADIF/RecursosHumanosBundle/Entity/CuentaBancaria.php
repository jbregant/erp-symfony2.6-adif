<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * Cuenta
 *
 * @ORM\Table(name="cuenta", indexes={
 *      @ORM\Index(name="banco", columns={"id_banco"})
 * })
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "todas" = "CuentaBancaria",
 *      "persona" = "CuentaBancariaPersona",
 *      "adif" = "CuentaBancariaADIF"
 * })
 * @UniqueEntity("cbu", message="El número de cbu ya se encuentra en uso.")
 */
class CuentaBancaria extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_cuenta", referencedColumnName="id", nullable=false)
     * })
     */
    protected $idTipoCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="cbu", type="string", length=255, unique=true, nullable=false)
     */
    protected $cbu;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Banco
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Banco", inversedBy="cuentas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_banco", referencedColumnName="id", nullable=false)
     * })
     */
    protected $idBanco;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set tipoCuenta
     *
     * @param integer $tipoCuenta
     * @return CuentaBancaria
     */
    public function setIdTipoCuenta($tipoCuenta) {
        $this->idTipoCuenta = $tipoCuenta;

        return $this;
    }

    /**
     * Get tipoCuenta
     *
     * @return integer 
     */
    public function getIdTipoCuenta() {
        return $this->idTipoCuenta;
    }

    /**
     * Set cbu
     *
     * @param string $cbu
     * @return CuentaBancaria
     */
    public function setCbu($cbu) {
        $this->cbu = $cbu;

        return $this;
    }

    /**
     * Get cbu
     *
     * @return string 
     */
    public function getCbu() {
        return $this->cbu;
    }

    /**
     * Set banco
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Banco $idBanco
     * @return CuentaBancaria
     */
    public function setIdBanco(\ADIF\RecursosHumanosBundle\Entity\Banco $idBanco = null) {
        $this->idBanco = $idBanco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Banco 
     */
    public function getIdBanco() {
        return $this->idBanco;
    }

}
