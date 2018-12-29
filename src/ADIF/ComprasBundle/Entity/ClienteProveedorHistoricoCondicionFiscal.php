<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ClienteProveedorHistoricoCondicionFiscal
 *
 * @author Manuel Becerra
 * created 14/07/2014
 * 
 * @ORM\Table(name="cliente_proveedor_historico_condicion_fiscal")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="impuesto", type="string")
 * @ORM\DiscriminatorMap({
 *      "impuesto_general" = "ClienteProveedorHistoricoCondicionFiscal",
 *      "impuesto_ganancias" = "ClienteProveedorHistoricoGanancias",
 *      "impuesto_iva" = "ClienteProveedorHistoricoIVA",
 *      "impuesto_suss" = "ClienteProveedorHistoricoSUSS",
 *      "impuesto_iibb" = "ClienteProveedorHistoricoIIBB"
 * })
 */
class ClienteProveedorHistoricoCondicionFiscal extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\ClienteProveedor
     *
     * @ORM\ManyToOne(targetEntity="ClienteProveedor")
     * @ORM\JoinColumn(name="id_cliente_proveedor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $clienteProveedor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="datetime", nullable=false)
     */
    protected $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="datetime", nullable=true)
     */
    protected $fechaHasta;

    /**
     * @ORM\Column(name="id_condicion", type="integer", nullable=true)
     */
    protected $idCondicion;

    /**
     * @var ADIF\ContableBundle\Entity\TipoResponsable
     */
    protected $condicion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento", type="boolean", nullable=false)
     */
    protected $exento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_retencion", type="boolean", nullable=false)
     */
    protected $pasibleRetencion;

    /**
     * @var ADIF\ContableBundle\Entity\CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion", referencedColumnName="id", nullable=true)
     */
    protected $certificadoExencion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->exento = false;
        $this->pasibleRetencion = false;
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
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setFechaDesde($fechaDesde) {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde() {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setFechaHasta($fechaHasta) {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta() {
        return $this->fechaHasta;
    }

    /**
     * Set idCondicion
     *
     * @param integer $idCondicion
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setIdCondicion($idCondicion) {
        $this->idCondicion = $idCondicion;

        return $this;
    }

    /**
     * Get idCondicion
     *
     * @return integer 
     */
    public function getIdCondicion() {
        return $this->idCondicion;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicion($tipoResponsable) {

        if (null != $tipoResponsable) {
            $this->idCondicion = $tipoResponsable->getId();
        } //.
        else {
            $this->idCondicion = null;
        }

        $this->condicion = $tipoResponsable;
    }

    /**
     * 
     * @return type
     */
    public function getCondicion() {
        return $this->condicion;
    }

    /**
     * Set exento
     *
     * @param boolean $exento
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setExento($exento) {
        $this->exento = $exento;

        return $this;
    }

    /**
     * Get exento
     *
     * @return boolean 
     */
    public function getExento() {
        return $this->exento;
    }

    /**
     * Set pasibleRetencion
     *
     * @param boolean $pasibleRetencion
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setPasibleRetencion($pasibleRetencion) {
        $this->pasibleRetencion = $pasibleRetencion;

        return $this;
    }

    /**
     * Get pasibleRetencion
     *
     * @return boolean 
     */
    public function getPasibleRetencion() {
        return $this->pasibleRetencion;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor
     * @return ClienteProveedorHistoricoCondicionFiscal
     */
    public function setClienteProveedor(\ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor) {
        $this->clienteProveedor = $clienteProveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\ClienteProveedor 
     */
    public function getClienteProveedor() {
        return $this->clienteProveedor;
    }

    /**
     * Set certificadoExencion
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencion
     * @return ClienteProveedorHistoricoCondicionFiscal
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

}
