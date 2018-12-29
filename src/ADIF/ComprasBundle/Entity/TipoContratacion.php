<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoContratacion 
 * 
 * Indica el tipo de contratación. 
 * 
 * Por ejemplo:
 *      Contratación de compra.
 *      Compulsa de precios.
 *      Contratación directa.
 * 
 *
 * @author Carlos Sabena
 * created 08/07/2014
 * 
 * @ORM\Table(name="tipo_contratacion")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\TipoContratacionRepository")
 */
class TipoContratacion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del tipo de contratación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionTipoContratacion;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El alias del tipo de contratación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $alias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="requiere_oc", type="boolean", nullable=false)
     */
    protected $requiereOC;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_desde", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoDesde;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_hasta", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoHasta;

    /**
     * @var integer
     * 
     * @ORM\Column(name="cantidad_minima_oferentes", type="integer", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="La cantidad mínima de oferentes debe ser de tipo numérico.")
     */
    protected $cantidadMinimaOferentes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->requiereOC = TRUE;
        $this->montoDesde = 0;
        $this->montoHasta = 0;
        $this->cantidadMinimaOferentes = 0;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return
                $this->denominacionTipoContratacion
                . ' (' . $this->getMontoDesde()
                . ' - ' . $this->getMontoHasta() . ')';
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
     * Set denominacionTipoContratacion
     *
     * @param string $denominacionTipoContratacion
     * @return TipoContratacion
     */
    public function setDenominacionTipoContratacion($denominacionTipoContratacion) {
        $this->denominacionTipoContratacion = $denominacionTipoContratacion;

        return $this;
    }

    /**
     * Get denominacionTipoContratacion
     *
     * @return string 
     */
    public function getDenominacionTipoContratacion() {
        return $this->denominacionTipoContratacion;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return TipoContratacion
     */
    public function setAlias($alias) {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Set requiereOC
     *
     * @param boolean $requiereOC
     * @return TipoContratacion
     */
    public function setRequiereOC($requiereOC) {
        $this->requiereOC = $requiereOC;

        return $this;
    }

    /**
     * Get requiereOC
     *
     * @return boolean 
     */
    public function getRequiereOC() {
        return $this->requiereOC;
    }

    /**
     * Set montoDesde
     *
     * @param float $montoDesde
     * @return TipoContratacion
     */
    public function setMontoDesde($montoDesde) {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return float 
     */
    public function getMontoDesde() {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param float $montoHasta
     * @return TipoContratacion
     */
    public function setMontoHasta($montoHasta) {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return float 
     */
    public function getMontoHasta() {
        return $this->montoHasta;
    }

    /**
     * Set cantidadMinimaOferentes
     *
     * @param integer $cantidadMinimaOferentes
     * @return TipoContratacion
     */
    public function setCantidadMinimaOferentes($cantidadMinimaOferentes) {
        $this->cantidadMinimaOferentes = $cantidadMinimaOferentes;

        return $this;
    }

    /**
     * Get cantidadMinimaOferentes
     *
     * @return integer 
     */
    public function getCantidadMinimaOferentes() {
        return $this->cantidadMinimaOferentes;
    }

    /**
     * Get aliasYMonto
     *
     * @return string 
     */
    public function getAliasYMonto() {

        $montoDesde = "$" . number_format($this->getMontoDesde(), 2);
        $montoHasta = "$" . number_format($this->getMontoHasta(), 2);

        return $this->alias . ' (' . $montoDesde . ' - ' . $montoHasta . ')';
    }

}
