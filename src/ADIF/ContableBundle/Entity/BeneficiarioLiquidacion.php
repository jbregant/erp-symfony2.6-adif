<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BenficiarioLiquidacion
 *
 * @author Darío Rapetti
 * created 26/05/2015
 * 
 * @ORM\Table(name="beneficiario_liquidacion")
 * @ORM\Entity 
 */
class BeneficiarioLiquidacion extends BaseAuditoria implements BaseAuditable {
    
    const __APDFA = '1';
    const __UF = '2';
    const __NACION = '3';
    const __OTROS = '4';

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
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El nombre no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $razonSocial;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El CUIT no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $CUIT;
    
    /**
     * @ORM\Column(name="id_domicilio", type="integer", nullable=true)
     */
    protected $idDomicilio;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     */
    protected $domicilio;

    /**
     * @ORM\ManyToMany(targetEntity="CuentaContable")
     * @ORM\JoinTable(name="beneficiario_liquidacion_cuenta_contable",
     *      joinColumns={@ORM\JoinColumn(name="id_beneficiario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", unique=true)}
     *      )
     * */
    private $cuentasContables;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function __construct() {
        $this->cuentasContables = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return BeneficiarioLiquidacion
     */
    public function setRazonSocial($razonSocial) {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial() {
        return $this->razonSocial;
    }
    
    /**
     * Set cuit
     *
     * @param string $cuit
     * @return BeneficiarioLiquidacion
     */
    public function setCUIT($cuit) {
        $this->CUIT = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCUIT() {
        return $this->CUIT;
    }
    
    /**
     * 
     * @param type $idDomicilio
     * @return BeneficiarioLiquidacion
     */
    public function setIdDomicilio($idDomicilio) {
        $this->idDomicilio = $idDomicilio;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdDomicilio() {
        return $this->idDomicilio;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilio
     */
    public function setDomicilio($domicilio) {
        if (null != $domicilio) {
            $this->idDomicilio = $domicilio->getId();
        } else {
            $this->idDomicilio = null;
        }

        $this->domicilio = $domicilio;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilio() {
        return $this->domicilio;
    }

    /**
     * Add cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentasContables
     * @return BeneficiarioLiquidacion
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentasContables) {
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
    }

    /**
     * Get cuentasContables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasContables() {
        return $this->cuentasContables;
    }
    
    public function getTipoDocumento(){
        return 'CUIT';
    }
    
    public function getNroDocumento(){
        return $this->getCUIT();
    }
    
    public function getLocalidad(){
        return $this->getDomicilio()->getLocalidad();
    }
    
    public function __toString() {
        return $this->getRazonSocial();
    }

}
