<?php

namespace ADIF\AutenticacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebService
 *
 * @ORM\Table(name="web_service")
 * @ORM\Entity
 */
class WebService
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="webServices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empresa", referencedColumnName="id", nullable=false)
     * })
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="ambiente", type="string", length=10)
     */
    private $ambiente;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="url_wsdl", type="string", length=255)
     */
    private $urlWsdl;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="string", length=255)
     */
    private $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="certificate", type="string", length=255)
     */
    private $certificate;

    /**
     * @var string
     *
     * @ORM\Column(name="otros_parametros", type="text")
     */
    private $otrosParametros;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set empresa
     *
     * @param \stdClass $empresa
     * @return WebService
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return \stdClass 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return WebService
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set ambiente
     *
     * @param string $ambiente
     * @return WebService
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;

        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string 
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return WebService
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set urlWsdl
     *
     * @param string $urlWsdl
     * @return WebService
     */
    public function setUrlWsdl($urlWsdl)
    {
        $this->urlWsdl = $urlWsdl;

        return $this;
    }

    /**
     * Get urlWsdl
     *
     * @return string 
     */
    public function getUrlWsdl()
    {
        return $this->urlWsdl;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return WebService
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set privateKey
     *
     * @param string $privateKey
     * @return WebService
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get privateKey
     *
     * @return string 
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set certificate
     *
     * @param string $certificate
     * @return WebService
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * Get certificate
     *
     * @return string 
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * Set otrosParametros
     *
     * @param string $otrosParametros
     * @return WebService
     */
    public function setOtrosParametros($otrosParametros)
    {
        $this->otrosParametros = $otrosParametros;

        return $this;
    }

    /**
     * Get otrosParametros
     *
     * @return string 
     */
    public function getOtrosParametros()
    {
        return $this->otrosParametros;
    }
}
