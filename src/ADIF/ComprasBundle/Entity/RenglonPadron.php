<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RenglonPadron
 *
 * @ORM\Table(name="renglon_padron")
 * @ORM\Entity
 */
class RenglonPadron extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_proveedor", type="integer")
     */
    private $idProveedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_padron", type="integer")
     */
    private $idPadron;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_certificado", type="string", length=255)
     */
    private $numeroCertificado;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_regimen", type="string", length=255)
     */
    private $tipoRegimen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date")
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date")
     */
    private $fechaHasta;

    /**
     * @var integer
     *
     * @ORM\Column(name="porcentaje_exencion", type="integer")
     */
    private $porcentajeExencion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actualiza", type="boolean")
     */
    private $actualiza;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", cascade={"persist"})
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     * 
     */
    protected $proveedor;

    /**
     * @var \ADIF\ComprasBundle\Entity\ClienteProveedor
     *
     * @ORM\ManyToOne(targetEntity="ClienteProveedor", cascade={"persist"})
     * @ORM\JoinColumn(name="id_cliente_proveedor", referencedColumnName="id")
     * 
     */
    protected $clienteProveedor;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="ADIF\ComprasBundle\Entity\CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion", referencedColumnName="id")
     */
    protected $certificadoExencion;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getClienteProveedor()->getRazonSocial();
    }

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
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return RenglonPadron
     */
    public function setIdProveedor($idProveedor)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }

    /**
     * Set idPadron
     *
     * @param integer $idPadron
     * @return RenglonPadron
     */
    public function setIdPadron($idPadron)
    {
        $this->idPadron = $idPadron;

        return $this;
    }

    /**
     * Get idPadron
     *
     * @return integer 
     */
    public function getIdPadron()
    {
        return $this->idPadron;
    }

    /**
     * Set numeroCertificado
     *
     * @param string $numeroCertificado
     * @return RenglonPadron
     */
    public function setNumeroCertificado($numeroCertificado)
    {
        $this->numeroCertificado = $numeroCertificado;

        return $this;
    }

    /**
     * Get numeroCertificado
     *
     * @return string 
     */
    public function getNumeroCertificado()
    {
        return $this->numeroCertificado;
    }

    /**
     * Set tipoRegimen
     *
     * @param string $tipoRegimen
     * @return RenglonPadron
     */
    public function setTipoRegimen($tipoRegimen)
    {
        $this->tipoRegimen = $tipoRegimen;

        return $this;
    }

    /**
     * Get tipoRegimen
     *
     * @return string 
     */
    public function getTipoRegimen()
    {
        return $this->tipoRegimen;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return RenglonPadron
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return RenglonPadron
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set porcentajeExencion
     *
     * @param integer $porcentajeExencion
     * @return RenglonPadron
     */
    public function setPorcentajeExencion($porcentajeExencion)
    {
        $this->porcentajeExencion = $porcentajeExencion;

        return $this;
    }

    /**
     * Get porcentajeExencion
     *
     * @return integer 
     */
    public function getPorcentajeExencion()
    {
        return $this->porcentajeExencion;
    }

    /**
     * Set actualiza
     *
     * @param boolean $actualiza
     * @return RenglonPadron
     */
    public function setActualiza($actualiza)
    {
        $this->actualiza = $actualiza;

        return $this;
    }

    /**
     * Get actualiza
     *
     * @return boolean 
     */
    public function getActualiza()
    {
        return $this->actualiza;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {

        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set clienteProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor
     * @return Cliente
     */
    public function setClienteProveedor(\ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor = null) {
        $this->clienteProveedor = $clienteProveedor;

        return $this;
    }

    /**
     * Get clienteProveedor
     *
     * @return \ADIF\ComprasBundle\Entity\ClienteProveedor 
     */
    public function getClienteProveedor() {
        return $this->clienteProveedor;
    }

    /**
     * 
     * @return type
     */
    public function getCUIT() {
        return $this->clienteProveedor->getCUIT();
    }


    /**
     * Set certificadoExencion
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencion
     * @return Cliente
     */
    public function setCertificadoExencion(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencion = null) {
        $this->certificadoExencion = $certificadoExencion;

        return $this;
    }

    /**
     * Get certificadoExencion
     *
     * @return \ADIF\ComprasBundle\Entity\CertificadoExencion 
     */
    public function getCertificadoExencion() {
        return $this->certificadoExencion;
    }

    /**
     * 
     * @return type
     */
    public function getRazonSocial() {
        return $this->clienteProveedor->getRazonSocial();
    }

}
