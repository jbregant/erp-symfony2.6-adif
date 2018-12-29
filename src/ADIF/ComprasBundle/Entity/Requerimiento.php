<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Requerimiento 
 * 
 * 
 * @author Carlos Sabena
 * created 15/07/2014
 * 
 * @ORM\Table(name="requerimiento")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RequerimientoRepository")
 */
class Requerimiento extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_requerimiento", type="date", nullable=false)
     */
    protected $fechaRequerimiento;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=true)
     */
    protected $numeroReferencia;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoContratacion
     *
     * @ORM\ManyToOne(targetEntity="TipoContratacion")
     * @ORM\JoinColumn(name="id_tipo_contratacion", referencedColumnName="id", nullable=false)
     * 
     */
    protected $tipoContratacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoRequerimiento
     *
     * @ORM\ManyToOne(targetEntity="EstadoRequerimiento", inversedBy="requerimientos")
     * @ORM\JoinColumn(name="id_estado_requerimiento", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoRequerimiento;

    /**
     * @ORM\OneToMany(targetEntity="RenglonRequerimiento", mappedBy="requerimiento", cascade={"persist", "remove"})
     */
    protected $renglonesRequerimiento;

    /**
     * @ORM\OneToMany(targetEntity="Cotizacion", mappedBy="requerimiento", cascade={"persist", "remove"})
     */
    protected $cotizaciones;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaRequerimiento = new \DateTime();
        $this->renglonesRequerimiento = new ArrayCollection();
        $this->cotizaciones = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->descripcion;
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
     * Set fechaRequerimiento
     *
     * @param \DateTime $fechaRequerimiento
     * @return Requerimiento
     */
    public function setFechaRequerimiento($fechaRequerimiento) {
        $this->fechaRequerimiento = $fechaRequerimiento;

        return $this;
    }

    /**
     * Get fechaRequerimiento
     *
     * @return \DateTime 
     */
    public function getFechaRequerimiento() {
        return $this->fechaRequerimiento;
    }

    /**
     * 
     * @return type
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     */
    public function setUsuario($usuario) {

        if (null != $usuario) {
            $this->idUsuario = $usuario->getId();
        } //.
        else {
            $this->idUsuario = null;
        }

        $this->usuario = $usuario;
    }

    /**
     * 
     * @return type
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return Requerimiento
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    /**
     * Set tipoContratacion
     *
     * @param \ADIF\ComprasBundle\Entity\TipoContratacion $tipoContratacion
     * @return Requerimiento
     */
    public function setTipoContratacion(\ADIF\ComprasBundle\Entity\TipoContratacion $tipoContratacion) {
        $this->tipoContratacion = $tipoContratacion;

        return $this;
    }

    /**
     * Get tipoContratacion
     *
     * @return \ADIF\ComprasBundle\Entity\TipoContratacion 
     */
    public function getTipoContratacion() {
        return $this->tipoContratacion;
    }

    /**
     * Add renglonesRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento
     * @return Requerimiento
     */
    public function addRenglonesRequerimiento(\ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento) {
        $this->renglonesRequerimiento[] = $renglonesRequerimiento;

        return $this;
    }

    /**
     * Remove renglonesRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento
     */
    public function removeRenglonesRequerimiento(\ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonesRequerimiento) {
        $this->renglonesRequerimiento->removeElement($renglonesRequerimiento);
    }

    /**
     * Get renglonesRequerimiento
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesRequerimiento() {
        return $this->renglonesRequerimiento;
    }

    /**
     * Add cotizaciones
     *
     * @param \ADIF\ComprasBundle\Entity\Cotizacion $cotizaciones
     * @return Requerimiento
     */
    public function addCotizacione(\ADIF\ComprasBundle\Entity\Cotizacion $cotizaciones) {
        $this->cotizaciones[] = $cotizaciones;

        return $this;
    }

    /**
     * Remove cotizaciones
     *
     * @param \ADIF\ComprasBundle\Entity\Cotizacion $cotizaciones
     */
    public function removeCotizacione(\ADIF\ComprasBundle\Entity\Cotizacion $cotizaciones) {
        $this->cotizaciones->removeElement($cotizaciones);
    }

    /**
     * Get cotizaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCotizaciones() {
        return $this->cotizaciones;
    }

    /**
     * Get justiprecioTotal
     */
    public function getJustiprecioTotal() {

        $justiprecioTotal = 0;

        foreach ($this->getRenglonesRequerimiento() as $renglon) {
            $justiprecioTotal += $renglon->getJustiprecioTotal();
        }

        return $justiprecioTotal;
    }

    /**
     * Set estadoRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoRequerimiento $estadoRequerimiento
     * @return Requerimiento
     */
    public function setEstadoRequerimiento(\ADIF\ComprasBundle\Entity\EstadoRequerimiento $estadoRequerimiento) {
        $this->estadoRequerimiento = $estadoRequerimiento;

        return $this;
    }

    /**
     * Get estadoRequerimiento
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoRequerimiento 
     */
    public function getEstadoRequerimiento() {
        return $this->estadoRequerimiento;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Requerimiento
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
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
     * @return ArrayCollection
     */
    public function getTipoAdicionales() {

        $tipoAdicionales = new ArrayCollection();

        // Por cada Cotizacion del Requerimiento
        foreach ($this->getCotizaciones() as $cotizacion) {

            // Por cada AdicionalCotizacion de la Cotizacion
            foreach ($cotizacion->getAdicionalesCotizacion() as $adicional) {

                // Obtengo el TipoAdicional
                $tipoAdicional = $adicional->getTipoAdicional();

                // Si el TipoAdicional no fue agregado al array
                if (!$tipoAdicionales->contains($tipoAdicional)) {
                    $tipoAdicionales->add($tipoAdicional);
                }
            }
        }

        return $tipoAdicionales;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsArchivable() {

        if ($this->getEstadoRequerimiento()->getDenominacionEstadoRequerimiento() != ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ARCHIVADO) {

            $cantidadRenglones = $this->getRenglonesRequerimiento()->count();

            $cantidadCotizacionesElegidas = 0;

            // Por cada renglon del requerimiento
            foreach ($this->getRenglonesRequerimiento() as $renglonRequerimiento) {

                // Por cada cotizacion asociada al renglon del requerimiento
                foreach ($renglonRequerimiento->getRenglonesCotizacion() as $renglonCotizacion) {

                    if ($renglonCotizacion->getCotizacionElegida()) {

                        $cantidadCotizacionesElegidas++;

                        break;
                    }
                }
            }

            return $cantidadRenglones == $cantidadCotizacionesElegidas;
        } else {

            return false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEditable() {

        $denominacionEstadoRequerimiento = $this->getEstadoRequerimiento()->getDenominacionEstadoRequerimiento();

        return $denominacionEstadoRequerimiento == ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_BORRADOR //
                || $denominacionEstadoRequerimiento == ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_A_CORREGIR //
                || $denominacionEstadoRequerimiento == ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_DESAPROBADO;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAnulable() {

        $esAnulable = true;

        if ($this->getEstadoRequerimiento()->getDenominacionEstadoRequerimiento() != ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ANULADO) {

            // Por cada renglon del requerimiento
            foreach ($this->getRenglonesRequerimiento() as $renglonRequerimiento) {

                // Por cada cotizacion asociada al renglon del requerimiento
                foreach ($renglonRequerimiento->getRenglonesCotizacion() as $renglonCotizacion) {

                    // Si la cotizacion fue elegida
                    if ($renglonCotizacion->getCotizacionElegida()) {

                        $esAnulable = false;

                        break 2;
                    }
                }
            }
        } else {

            $esAnulable = false;
        }

        return $esAnulable;
    }
	
	public function getNumerosSolicitudesCompra()
	{
		$numeros = array();
		if ($this->renglonesRequerimiento != null) {
			foreach($this->renglonesRequerimiento as $renglonRequerimiento) {
				$renglonSolicitudCompra = $renglonRequerimiento->getRenglonSolicitudCompra();
				if ($renglonSolicitudCompra != null) {
					$solicitudCompra = $renglonSolicitudCompra->getSolicitudCompra();
					if ($solicitudCompra != null) {
						$numeros[] = $solicitudCompra->getNumero();
					}
				}
			}
			
			$numeros = array_unique($numeros);
			$numeros = implode(', ', $numeros);
		}
		
		return !empty($numeros) ? $numeros : null;
	}

}
