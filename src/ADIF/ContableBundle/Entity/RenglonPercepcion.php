<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonPercepcion
 *
 * @author Darío Rapetti
 * created 22/10/2014
 * 
 * @ORM\Table(name="renglon_percepcion")
 * @ORM\Entity 
 */
class RenglonPercepcion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConceptoPercepcion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_percepcion", referencedColumnName="id", nullable=false)
     * })
     */
    private $conceptoPercepcion;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Jurisdiccion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_jurisdiccion", referencedColumnName="id", nullable=true)
     * })
     */
    private $jurisdiccion;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $monto;

    /**
     * @var Comprobante
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Comprobante", inversedBy="renglonesPercepcion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    private $comprobante;
    
    /**
     * @ORM\OneToOne(targetEntity="ADIF\ContableBundle\Entity\RenglonDeclaracionJurada", cascade={"all"})
     * @ORM\JoinColumn(name="id_renglon_declaracion_jurada", referencedColumnName="id", nullable=true)
     **/
    private $renglonDeclaracionJurada;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenPercepcion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_regimen_percepcion", referencedColumnName="id", nullable=true)
     * })
     */
    private $regimenPercepcion;

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
     * @return RenglonPercepcion
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
     * @return RenglonPercepcion
     */
    public function setJurisdiccion(\ADIF\ContableBundle\Entity\Jurisdiccion $jurisdiccion = null) {
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
     * Set monto
     *
     * @param string $monto
     * @return RenglonPercepcion
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }
    
    /**
     * Set comprobante
     *
     * @param Comprobante $comprobante
     * @return RenglonComprobante
     */
    public function setComprobante($comprobante) {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return Comprobante 
     */
    public function getComprobante() {
        return $this->comprobante;
    }
    
    /**
     * Set renglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada
     * @return RenglonPercepcion
     */
    public function setRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada) {
        $this->renglonDeclaracionJurada = $renglonDeclaracionJurada;

        return $this;
    }
    
    /**
     * Get renglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     */
    public function getRenglonDeclaracionJurada() {
        return $this->renglonDeclaracionJurada;
    }
    
    /**
     * Set regimenPercepcion
     *
     * @param \ADIF\ContableBundle\Entity\RegimenPercepcion $regimenPercepcion
     * @return RenglonPercepcion
     */
    public function setRegimenPercepcion(\ADIF\ContableBundle\Entity\RegimenPercepcion $regimenPercepcion) {
        $this->regimenPercepcion = $regimenPercepcion;

        return $this;
    }
    
    /**
     * Get regimenPercepcion
     *
     * @return \ADIF\ContableBundle\Entity\RegimenPercepcion
     */
    public function getRegimenPercepcion() {
        return $this->regimenPercepcion;
    }

}
