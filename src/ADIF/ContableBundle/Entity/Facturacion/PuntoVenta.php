<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @author Darío Rapetti
 * created 26/02/2015
 * 
 * @ORM\Table(name="punto_venta")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\PuntoVentaRepository")
 * @UniqueEntity(fields={"numero","fechaBaja"}, ignoreNull=false, message="El número ya se encuentra en uso.")
 */
class PuntoVenta extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="numero", type="string", nullable=false)
     */
    protected $numero;

    /**
     *
     * @var PuntoVentaClaseContrato
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato", mappedBy="puntoVenta", cascade={"all"})
     */
    protected $puntosVentaClaseContrato;

    /**
     * @var boolean
     *
     * @ORM\Column(name="genera_comprobante_electronico", type="boolean", nullable=false)
     */
    protected $generaComprobanteElectronico;

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->numero;
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
     * Set numero
     *
     * @param string $numero
     * @return PuntoVenta
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->puntosVentaClaseContrato = new \Doctrine\Common\Collections\ArrayCollection();
        $this->generaComprobanteElectronico = false;
    }

    /**
     * Add puntosVentaClaseContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato $puntosVentaClaseContrato
     * @return PuntoVenta
     */
    public function addPuntosVentaClaseContrato(\ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato $puntosVentaClaseContrato) {
        $this->puntosVentaClaseContrato[] = $puntosVentaClaseContrato;

        $puntosVentaClaseContrato->setPuntoVenta($this);

        return $this;
    }

    /**
     * Remove puntosVentaClaseContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato $puntosVentaClaseContrato
     */
    public function removePuntosVentaClaseContrato(\ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato $puntosVentaClaseContrato) {
        $this->puntosVentaClaseContrato->removeElement($puntosVentaClaseContrato);
    }

    /**
     * Get puntosVentaClaseContrato
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPuntosVentaClaseContrato() {
        return $this->puntosVentaClaseContrato;
    }

    /**
     * Set generaComprobanteElectronico
     *
     * @param boolean $generaComprobanteElectronico
     * @return PuntoVenta
     */
    public function setGeneraComprobanteElectronico($generaComprobanteElectronico) {
        $this->generaComprobanteElectronico = $generaComprobanteElectronico;

        return $this;
    }

    /**
     * Get generaComprobanteElectronico
     *
     * @return boolean 
     */
    public function getGeneraComprobanteElectronico() {
        return $this->generaComprobanteElectronico;
    }

}
