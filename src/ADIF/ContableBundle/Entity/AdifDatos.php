<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of AdifDatos
 *
 * @author Manuel Becerra
 * created 03/11/2014
 * 
 * @ORM\Table(name="adif_datos")
 * @ORM\Entity
 */
class AdifDatos extends BaseAuditoria implements BaseAuditable {

    /**
     * RAZON_SOCIAL
     */
    const RAZON_SOCIAL = 'ADIF';

    /**
     * CUIT
     */
    const CUIT = '30-71069599-3';

    /**
     * LOCALIDAD
     */
    const LOCALIDAD = 'Capital Federal';

    /**
     * DOMICILIO
     */
    const DOMICILIO = 'AV. RAMOS MEJIA 1302';

    public function __toString() {

        return $this->getRazonSocial();
    }

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
     *      maxMessage="La razón social no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El CUIT no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_ingresos_brutos", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de Ingresos Brutos no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroIngresosBrutos;

    /**
     * @ORM\Column(name="id_domicilio_fiscal", type="integer", nullable=true)
     */
    protected $idDomicilioFiscal;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     */
    protected $domicilioFiscal;

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
     * @return AdifDatos
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
        return self::RAZON_SOCIAL;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return AdifDatos
     */
    public function setCuit($cuit) {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCuit() {
        return self::CUIT;
    }

    /**
     * Set numeroIngresosBrutos
     *
     * @param string $numeroIngresosBrutos
     * @return AdifDatos
     */
    public function setNumeroIngresosBrutos($numeroIngresosBrutos) {
        $this->numeroIngresosBrutos = $numeroIngresosBrutos;

        return $this;
    }

    /**
     * Get numeroIngresosBrutos
     *
     * @return string 
     */
    public function getNumeroIngresosBrutos() {
        return $this->numeroIngresosBrutos;
    }

    /**
     * Set idDomicilioFiscal
     *
     * @param integer $idDomicilioFiscal
     * @return AdifDatos
     */
    public function setIdDomicilioFiscal($idDomicilioFiscal) {
        $this->idDomicilioFiscal = $idDomicilioFiscal;

        return $this;
    }

    /**
     * Get idDomicilioFiscal
     *
     * @return integer 
     */
    public function getIdDomicilioFiscal() {
        return $this->idDomicilioFiscal;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioFiscal
     */
    public function setDomicilioFiscal($domicilioFiscal) {

        if (null != $domicilioFiscal) {
            $this->idDomicilioFiscal = $domicilioFiscal->getId();
        } //.
        else {
            $this->idDomicilioFiscal = null;
        }

        $this->domicilioFiscal = $domicilioFiscal;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilioFiscal() {
        return $this->domicilioFiscal;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilio() {
        return self::DOMICILIO;
    }

    /**
     * 
     * @return type
     */
    public function getLocalidad() {
        return self::LOCALIDAD;
    }

    /**
     * 
     * @return string
     */
    public function getTipoDocumento() {
        return 'CUIT';
    }

    /**
     * 
     * @return type
     */
    public function getNroDocumento() {
        return self::CUIT;
    }

}
