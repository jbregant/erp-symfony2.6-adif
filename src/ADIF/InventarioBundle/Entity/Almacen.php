<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Estacion;

/**
 * Almacen
 *
 * @ORM\Table("almacen")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\AlmacenRepository")
 * @UniqueEntity("denominacion", message="La denominaciÃ³n ingresada ya se encuentra en uso.")
 * @Assert\Expression(
 *     expression="this.getEstacion() != '' or this.getZonaVia() != ''",
 *     message="Uno de los siguientes campos es requerido (EstaciÃ³n o Zona VÃ­a)"
 * )
 */
class Almacen extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=1, nullable=false)
     * @Assert\NotBlank()
     */
    private $tipo;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_deposito", type="integer", nullable=true)
     */
    private $numeroDeposito;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     */
    private $denominacion;

    /**
     * @ORM\Column(name="id_provincia", type="integer", nullable=true)
     */
    private $idProvincia;


    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Provincia
     */
    protected $provincia;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=true)
     */
    private $linea;

    /**
     * @var string
     *
     * @ORM\Column(name="zona_via", type="string", length=255, nullable=true)
     */
    private $zonaVia;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Estacion")
     * @ORM\JoinColumn(name="id_estacion", referencedColumnName="id", nullable=true)
     */
    private $estacion;

    /**
     * @var string
     *
     * @ORM\Column(name="latitud", type="string", length=20, nullable=true)
     */
    private $latitud;

    /**
     * @var string
     *
     * @ORM\Column(name="longitud", type="string", length=20, nullable=true)
     */
    private $longitud;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    /**
     * @return string
     */
    public function __toString() {
        return $this->getDenominacion();
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
     * Set tipo
     *
     * @param string $tipo
     * @return Almacen
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set numeroDeposito
     *
     * @param integer $numeroDeposito
     * @return Almacen
     */
    public function setNumeroDeposito($numeroDeposito)
    {
        $this->numeroDeposito = $numeroDeposito;

        return $this;
    }

    /**
     * Get numeroDeposito
     *
     * @return integer
     */
    public function getNumeroDeposito()
    {
        return $this->numeroDeposito;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return Almacen
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

    //Provincia de RecursosHumanosBundle:

    public function getIdProvincia()
    {
        return $this->idProvincia;
    }

    public function setIdProvincia($idProvincia)
    {
        $this->idProvincia = $idProvincia;

        return $this;
    }

    /**
     * Set provincia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Provincia $provincia
     */
    public function setProvincia($provincia)
    {
        if (null != $provincia) {
            $this->idProvincia = $provincia->getId();
        } else {
            $this->idProvincia = null;
        }

        $this->provincia = $provincia;
    }

    /**
     * Get provincia
     *
     * @return integer
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     * @return Almacen
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;

        return $this;
    }

    /**
     * Get linea
     *
     * @return integer
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set zonaVia
     *
     * @param string $zonaVia
     * @return Almacen
     */
    public function setZonaVia($zonaVia)
    {
        $this->zonaVia = $zonaVia;

        return $this;
    }

    /**
     * Get zonaVia
     *
     * @return string
     */
    public function getZonaVia()
    {
        return $this->zonaVia;
    }

    /**
     * Set estacion
     *
     * @param integer $estacion
     * @return Almacen
     */
    public function setEstacion($estacion)
    {
        $this->estacion = $estacion;

        return $this;
    }

    /**
     * Get estacion
     *
     * @return integer
     */
    public function getEstacion()
    {
        return $this->estacion;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     * @return Almacen
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get latitud
     *
     * @return string
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longitud
     *
     * @param string $longitud
     * @return Almacen
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return string
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Almacen
     */
    public function setIdEmpresa($idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa
     *
     * @return integer
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }
}
