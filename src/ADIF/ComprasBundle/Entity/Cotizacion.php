<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cotizacion
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="cotizacion")
 * @ORM\Entity
 */
class Cotizacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_invitacion", type="date", nullable=false)
     */
    protected $fechaInvitacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\Requerimiento
     *
     * @ORM\ManyToOne(targetEntity="Requerimiento", inversedBy="cotizaciones")
     * @ORM\JoinColumn(name="id_requerimiento", referencedColumnName="id", nullable=false)
     * 
     */
    protected $requerimiento;

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="cotizacionesSolicitadas")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $proveedor;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_cotizacion", type="date", nullable=true)
     */
    protected $fechaCotizacion;

    /**
     * @ORM\OneToMany(targetEntity="RenglonCotizacion", mappedBy="cotizacion", cascade={"persist", "remove"})
     */
    protected $renglonesCotizacion;

    /**
     * @ORM\OneToMany(targetEntity="AdicionalCotizacion", mappedBy="cotizacion", cascade={"persist", "remove"})
     */
    protected $adicionalesCotizacion;

    /**
     * @ORM\OneToMany(targetEntity="CotizacionArchivo", mappedBy="cotizacion", cascade={"persist","remove"})
     */
    protected $archivos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->renglonesCotizacion = new ArrayCollection();
        $this->adicionalesCotizacion = new ArrayCollection();
        $this->archivos = new ArrayCollection();
    }

    /**
     * 
     * @return type
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
     * Set fechaInvitacion
     *
     * @param \DateTime $fechaInvitacion
     * @return Cotizacion
     */
    public function setFechaInvitacion($fechaInvitacion) {
        $this->fechaInvitacion = $fechaInvitacion;

        return $this;
    }

    /**
     * Get fechaInvitacion
     *
     * @return \DateTime 
     */
    public function getFechaInvitacion() {
        return $this->fechaInvitacion;
    }

    /**
     * Set fechaCotizacion
     *
     * @param \DateTime $fechaCotizacion
     * @return Cotizacion
     */
    public function setFechaCotizacion($fechaCotizacion) {
        $this->fechaCotizacion = $fechaCotizacion;

        return $this;
    }

    /**
     * Get fechaCotizacion
     *
     * @return \DateTime 
     */
    public function getFechaCotizacion() {
        return $this->fechaCotizacion;
    }

    /**
     * Set requerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\Requerimiento $requerimiento
     * @return Cotizacion
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
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return Cotizacion
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Add renglonesCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonesCotizacion
     * @return Cotizacion
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
     * Add adicionalesCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\AdicionalCotizacion $adicionalesCotizacion
     * @return Cotizacion
     */
    public function addAdicionalesCotizacion(\ADIF\ComprasBundle\Entity\AdicionalCotizacion $adicionalesCotizacion) {
        $this->adicionalesCotizacion[] = $adicionalesCotizacion;

        return $this;
    }

    /**
     * Remove adicionalesCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\AdicionalCotizacion $adicionalesCotizacion
     */
    public function removeAdicionalesCotizacion(\ADIF\ComprasBundle\Entity\AdicionalCotizacion $adicionalesCotizacion) {
        $this->adicionalesCotizacion->removeElement($adicionalesCotizacion);
    }

    /**
     * Get adicionalesCotizacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdicionalesCotizacion() {
        return $this->adicionalesCotizacion;
    }

    /**
     * 
     * @param type $tipoAdicional
     * @return boolean
     */
    public function contieneTipoAdicional($tipoAdicional) {

        $contieneTipoAdicional = false;

        foreach ($this->getAdicionalesCotizacion() as $adicionalCotizacion) {

            if ($adicionalCotizacion->getTipoAdicional() == $tipoAdicional) {
                $contieneTipoAdicional = true;
                break;
            }
        }

        return $contieneTipoAdicional;
    }

    /**
     * 
     * @param type $tipoAdicional
     */
    public function getAdicionalesCotizacionByTipoAdicional($tipoAdicional) {

        $tiposAdicional = new ArrayCollection();

        foreach ($this->getAdicionalesCotizacion() as $adicionalCotizacion) {

            if ($adicionalCotizacion->getTipoAdicional() == $tipoAdicional) {
                $tiposAdicional->add($adicionalCotizacion);
            }
        }

        return $tiposAdicional;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoSubtotal($enMCL = true) {

        $subtotal = 0;

        foreach ($this->getRenglonesCotizacion() as $renglonCotizacion) {

            $subtotal += $renglonCotizacion->getMontoTotalNeto($enMCL);
        }

        return $subtotal;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotalIva($enMCL = true) {

        $totalIVA = 0;

        foreach ($this->getRenglonesCotizacion() as $renglonCotizacion) {

            $totalIVA += $renglonCotizacion->getMontoTotalIva($enMCL);
        }

        return $totalIVA;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotal($enMCL = true) {

        $total = 0;

        foreach ($this->getRenglonesCotizacion() as $renglonCotizacion) {

            $total += $renglonCotizacion->getMontoTotal($enMCL);
        }

        return $total;
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
    public function getRenglonCotizacionIds() {

        $ids = array();

        foreach ($this->getRenglonesCotizacion() as $renglonCotizacion) {

            $ids[] = $renglonCotizacion->getId();
        }

        return $ids;
    }

    /**
     * 
     * @return type
     */
    public function getAdicionalesElegidos() {
        return $this->getAdicionalesCotizacion()->filter(
                        function($adicionalCotizacion) {
                    return $adicionalCotizacion->getAdicionalElegido();
                }
        );
    }

    /**
     * Add archivo
     *
     * @param CotizacionArchivo $archivo
     * @return Cotizacion
     */
    public function addArchivo(CotizacionArchivo $archivo) {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove archivo
     *
     * @param CotizacionArchivo $archivo
     */
    public function removeArchivo(CotizacionArchivo $archivo) {
        $this->archivos->removeElement($archivo);
    }

    /**
     * Get archivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchivos() {
        return $this->archivos;
    }

}
