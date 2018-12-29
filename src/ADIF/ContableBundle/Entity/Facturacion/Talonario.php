<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Darío Rapetti
 * created 29/01/2015
 * 
 * @ORM\Table(name="talonario")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\TalonarioRepository")
 * @UniqueEntity(
 *      fields = {"tipoComprobante", "letraComprobante", "puntoVenta", "numeroDesde", "numeroHasta"}, 
 *      ignoreNull = false, 
 *      message="La numeración ingresada ya se encuentra en uso."
 * )
 */
class Talonario extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\TipoComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoComprobante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\LetraComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_letra_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    protected $letraComprobante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\PuntoVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_punto_venta", referencedColumnName="id", nullable=false)
     * })
     */
    protected $puntoVenta;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_desde", type="integer", nullable=false)
     */
    protected $numeroDesde;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_hasta", type="integer", nullable=false)
     */
    protected $numeroHasta;

    /**
     * @var \ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario
     *
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario", inversedBy="talonario", cascade={"all"})
     * @ORM\JoinColumn(name="id_cai", referencedColumnName="id", nullable=false)
     * 
     */
    protected $codigoAutorizacionImpresionTalonario;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_siguiente", type="integer", nullable=false)
     */
    protected $numeroSiguiente;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esta_agotado", type="boolean", nullable=false)
     */
    protected $estaAgotado;

    /**
     * Constructor
     */
    public function __construct() {
        $this->estaAgotado = false;
        $this->numeroSiguiente = $this->numeroDesde;
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
     * Set tipoComprobante
     *
     * @param \ADIF\ContableBundle\Entity\TipoComprobante $letraComprobante
     * @return Talonario
     */
    public function setTipoComprobante(\ADIF\ContableBundle\Entity\TipoComprobante $tipoComprobante) {
        $this->tipoComprobante = $tipoComprobante;

        return $this;
    }

    /**
     * Get tipoComprobante
     *
     * @return \ADIF\ContableBundle\Entity\TipoComprobante 
     */
    public function getTipoComprobante() {
        return $this->tipoComprobante;
    }

    /**
     * Set letraComprobante
     *
     * @param \ADIF\ContableBundle\Entity\LetraComprobante $letraComprobante
     * @return Comprobante
     */
    public function setLetraComprobante(\ADIF\ContableBundle\Entity\LetraComprobante $letraComprobante) {
        $this->letraComprobante = $letraComprobante;

        return $this;
    }

    /**
     * Get letraComprobante
     *
     * @return \ADIF\ContableBundle\Entity\LetraComprobante 
     */
    public function getLetraComprobante() {
        return $this->letraComprobante;
    }

    /**
     * Set puntoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta $puntoVenta
     * @return Talonario
     */
    public function setPuntoVenta($puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

    /**
     * Set numeroDesde
     *
     * @param integer $numeroDesde
     * @return Talonario
     */
    public function setNumeroDesde($numeroDesde) {
        $this->numeroDesde = $numeroDesde;

        return $this;
    }

    /**
     * Get numeroDesde
     *
     * @return integer 
     */
    public function getNumeroDesde() {
        return $this->numeroDesde;
    }

    /**
     * Set numeroHasta
     *
     * @param integer $numeroHasta
     * @return Talonario
     */
    public function setNumeroHasta($numeroHasta) {
        $this->numeroHasta = $numeroHasta;

        return $this;
    }

    /**
     * Get numeroHasta
     *
     * @return integer 
     */
    public function getNumeroHasta() {
        return $this->numeroHasta;
    }

    /**
     * Set codigoAutorizacionImpresionTalonario
     *
     * @param \ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario $codigoAutorizacionImpresionTalonario
     * @return Talonario
     */
    public function setCodigoAutorizacionImpresionTalonario(\ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario $codigoAutorizacionImpresionTalonario) {
        $this->codigoAutorizacionImpresionTalonario = $codigoAutorizacionImpresionTalonario;

        return $this;
    }

    /**
     * Get codigoAutorizacionImpresionTalonario
     *
     * @return \ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario 
     */
    public function getCodigoAutorizacionImpresionTalonario() {
        return $this->codigoAutorizacionImpresionTalonario;
    }

    /**
     * Set numeroSiguiente
     *
     * @param integer $numeroSiguiente
     * @return Talonario
     */
    public function setNumeroSiguiente($numeroSiguiente) {
        $this->numeroSiguiente = $numeroSiguiente;

        return $this;
    }

    /**
     * Get numeroSiguiente
     *
     * @return integer 
     */
    public function getNumeroSiguiente() {
        return $this->numeroSiguiente;
    }

    /**
     * Set estaAgotado
     *
     * @param boolean $estaAgotado
     * @return Talonario
     */
    public function setEstaAgotado($estaAgotado) {
        $this->estaAgotado = $estaAgotado;

        return $this;
    }

    /**
     * Get estaAgotado
     *
     * @return boolean 
     */
    public function getEstaAgotado() {
        return $this->estaAgotado;
    }

    /**
     * 
     * @return type
     */
    public function getEsEditable() {
        return !$this->getEstaAgotado() && $this->getNumeroDesde() == $this->getNumeroSiguiente();
    }

}
