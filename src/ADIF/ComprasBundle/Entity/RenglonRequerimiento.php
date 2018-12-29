<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonRequerimiento
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="renglon_requerimiento")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RenglonRequerimientoRepository")
 */
class RenglonRequerimiento extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\Requerimiento
     *
     * @ORM\ManyToOne(targetEntity="Requerimiento", inversedBy="renglonesRequerimiento")
     * @ORM\JoinColumn(name="id_requerimiento", referencedColumnName="id", nullable=false)
     * 
     */
    protected $requerimiento;

    /**
     * @var \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="RenglonSolicitudCompra", inversedBy="renglonesRequerimiento")
     * @ORM\JoinColumn(name="id_renglon_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $renglonSolicitudCompra;

    /**
     * @var float
     * 
     * @ORM\Column(name="cantidad", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad debe ser de tipo numérico.")
     */
    protected $cantidad;

    /**
     * @var float
     * 
     * @ORM\Column(name="justiprecio_unitario", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El justiprecio unitario debe ser de tipo numérico.")
     */
    protected $justiprecioUnitario;

    /**
     * @ORM\OneToMany(targetEntity="RenglonCotizacion", mappedBy="renglonRequerimiento")
     */
    protected $renglonesCotizacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cantidad = 0;
        $this->renglonesCotizacion = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNumero();
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
     * Set cantidad
     *
     * @param float $cantidad
     * @return RenglonRequerimiento
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float 
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set justiprecioUnitario
     *
     * @param float $justiprecioUnitario
     * @return RenglonSolicitudCompra
     */
    public function setJustiprecioUnitario($justiprecioUnitario) {
        $this->justiprecioUnitario = $justiprecioUnitario;

        return $this;
    }

    /**
     * Get justiprecioUnitario
     *
     * @return float 
     */
    public function getJustiprecioUnitario() {
        return $this->justiprecioUnitario;
    }

    /**
     * Set requerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\Requerimiento $requerimiento
     * @return RenglonRequerimiento
     */
    public function setRequerimiento(\ADIF\ComprasBundle\Entity\Requerimiento $requerimiento) {
        $this->requerimiento = $requerimiento;

        return $this;
    }

    /**
     * Get requerimiento
     *
     * @return \ADIF\ComprasBundle\Entity\Requerimiento 
     */
    public function getRequerimiento() {
        return $this->requerimiento;
    }

    /**
     * Set renglonSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonSolicitudCompra
     * @return RenglonRequerimiento
     */
    public function setRenglonSolicitudCompra(\ADIF\ComprasBundle\Entity\RenglonSolicitudCompra $renglonSolicitudCompra) {
        $this->renglonSolicitudCompra = $renglonSolicitudCompra;

        return $this;
    }

    /**
     * Get renglonSolicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra 
     */
    public function getRenglonSolicitudCompra() {
        return $this->renglonSolicitudCompra;
    }

    /**
     * Add renglonesCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonesCotizacion
     * @return RenglonRequerimiento
     */
    public function addRenglonesCotizacion(\ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonesCotizacion) {
        $this->renglonesCotizacion[] = $renglonesCotizacion;

        return $this;
    }

    /**
     * Remove renglonesCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonesCotizacion
     */
    public function removeRenglonesCotizacion(\ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonesCotizacion) {
        $this->renglonesCotizacion->removeElement($renglonesCotizacion);
    }

    /**
     * Get renglonesCotizacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesCotizacion() {
        return $this->renglonesCotizacion;
    }

    /**
     * Get justiprecioTotal
     *
     * @return float 
     */
    public function getJustiprecioTotal() {
        return $this->justiprecioUnitario * $this->cantidad;
    }

    /**
     * 
     * @return type
     */
    public function getNumero() {

        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * 
     * @return type
     */
    public function getBienEconomico() {
        return $this->renglonSolicitudCompra->getBienEconomico();
    }

}
