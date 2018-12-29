<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RetencionClienteParametrizacion
 * 
 * @author Augusto Villa Monte
 * created 15/07/2015
 * 
 * @ORM\Table(name="retencion_cliente_parametrizacion")
 * @ORM\Entity 
 */
class RetencionClienteParametrizacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="TipoImpuesto")
     * @ORM\JoinColumn(name="id_tipo_impuesto", referencedColumnName="id", nullable=false)
     */
    protected $tipoImpuesto; 

    /**
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=false)
     */    
    protected $cuentaContable;

    /**
     * @ORM\ManyToOne(targetEntity="Jurisdiccion")
     * @ORM\JoinColumn(name="id_jurisdiccion", referencedColumnName="id", nullable=true)
     */
    protected $jurisdiccion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set tipoImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto
     */
    public function setTipoImpuesto(\ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto) {
        $this->tipoImpuesto = $tipoImpuesto;

        return $this;
    }

    /**
     * Get tipoImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\TipoImpuesto 
     */
    public function getTipoImpuesto() {
        return $this->tipoImpuesto;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {
	$this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set jurisdiccion
     *
     * @param \ADIF\ContableBundle\Entity\Jurisdiccion $jurisdiccion
     * @return RetencionClienteParametrizacion
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
    
    public function __toString() {       
        return $this->getTipoImpuesto()->getDenominacion() . ' ' . ($this->getJurisdiccion() ? $this->getJurisdiccion()->getDenominacion() : '');
    }    
    
}
