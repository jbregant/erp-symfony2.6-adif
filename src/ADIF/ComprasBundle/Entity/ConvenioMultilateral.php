<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactoProveedor
 * 
 * @author Manuel Becerra
 * created 05/10/2014
 * 
 * @ORM\Table(name="convenio_multilateral")
 * @ORM\Entity
 */
class ConvenioMultilateral extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="DatosImpositivos", inversedBy="convenioMultilateralIngresosBrutos")
     * @ORM\JoinColumn(name="id_datos_impositivos", referencedColumnName="id", nullable=false)
     */
    protected $datosImpositivos;

    /**
     * @var string
     *
     * @ORM\Column(name="jurisdiccion", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="La jurisdicción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $jurisdiccion;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_aplicacion_CABA", type="decimal", precision=8, scale=5, nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El porcentaje debe ser de tipo numérico.")
     */
    protected $porcentajeAplicacionCABA;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set datosImpositivos
     *
     * @param \ADIF\ComprasBundle\Entity\DatosImpositivos $datosImpositivos
     * @return ConvenioMultilateral
     */
    public function setDatosImpositivos($datosImpositivos) {
        $this->datosImpositivos = $datosImpositivos;

        return $this;
    }
    
    /**
     * Get datosImpositivos
     *
     * @return \ADIF\ComprasBundle\Entity\DatosImpositivos 
     */
    public function getDatosImpositivos() {
        return $this->datosImpositivos;
    }

    /**
     * Set jurisdiccion
     *
     * @param string $jurisdiccion
     * @return ConvenioMultilateral
     */
    public function setJurisdiccion($jurisdiccion) {
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return string 
     */
    public function getJurisdiccion() {
        return $this->jurisdiccion;
    }

    /**
     * Set porcentajeAplicacionCABA
     *
     * @param float $porcentajeAplicacionCABA
     * @return ConvenioMultilateral
     */
    public function setPorcentajeAplicacionCABA($porcentajeAplicacionCABA) {
        $this->porcentajeAplicacionCABA = $porcentajeAplicacionCABA;

        return $this;
    }

    /**
     * Get porcentajeAplicacionCABA
     *
     * @return float 
     */
    public function getPorcentajeAplicacionCABA() {
        return $this->porcentajeAplicacionCABA;
    }

}
