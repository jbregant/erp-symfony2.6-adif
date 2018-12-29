<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ComprobanteImpresion
 * 
 * @author Manuel Becerra
 * created 28/08/2015
 * 
 * @ORM\Table(name="comprobante_impresion")
 * @ORM\Entity
 */
class ComprobanteImpresion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="razon_social", type="string", nullable=true)
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string", nullable=true)
     */
    protected $numeroDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", nullable=true)
     */
    protected $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", nullable=true)
     */
    protected $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", nullable=true)
     */
    protected $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="domicilio_legal", type="string", nullable=true)
     */
    protected $domicilioLegal;

    /**
     * @var string
     *
     * @ORM\Column(name="condicion_iva", type="string", nullable=true)
     */
    protected $condicionIVA;

    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", nullable=true)
     */
    protected $periodo;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return ComprobanteImpresion
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
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     *
     * @return ComprobanteImpresion
     */
    public function setNumeroDocumento($numeroDocumento) {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string
     */
    public function getNumeroDocumento() {
        return $this->numeroDocumento;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     *
     * @return ComprobanteImpresion
     */
    public function setProvincia($provincia) {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return string
     */
    public function getProvincia() {
        return $this->provincia;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return ComprobanteImpresion
     */
    public function setLocalidad($localidad) {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string
     */
    public function getLocalidad() {
        return $this->localidad;
    }

    /**
     * Set codigoPostal
     *
     * @param string $codigoPostal
     *
     * @return ComprobanteImpresion
     */
    public function setCodigoPostal($codigoPostal) {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string
     */
    public function getCodigoPostal() {
        return $this->codigoPostal;
    }

    /**
     * Set domicilioLegal
     *
     * @param string $domicilioLegal
     *
     * @return ComprobanteImpresion
     */
    public function setDomicilioLegal($domicilioLegal) {
        $this->domicilioLegal = $domicilioLegal;

        return $this;
    }

    /**
     * Get domicilioLegal
     *
     * @return string
     */
    public function getDomicilioLegal() {
        return $this->domicilioLegal;
    }

    /**
     * Set condicionIVA
     *
     * @param string $condicionIVA
     *
     * @return ComprobanteImpresion
     */
    public function setCondicionIVA($condicionIVA) {
        $this->condicionIVA = $condicionIVA;

        return $this;
    }

    /**
     * Get condicionIVA
     *
     * @return string
     */
    public function getCondicionIVA() {
        return $this->condicionIVA;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroDocumentoAndRazonSocial() {
        return $this->getNumeroDocumento() . ' — ' . $this->getRazonSocial();
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return ComprobanteImpresion
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo() {
        return $this->periodo;
    }
    
}
