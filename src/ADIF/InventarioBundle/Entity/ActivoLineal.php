<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\InventarioBundle\Validator\Constraints as InventarioAssert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Estacion;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Entity\Corredor;
use ADIF\InventarioBundle\Entity\Ramal;
use ADIF\InventarioBundle\Entity\Divisiones;
use ADIF\InventarioBundle\Entity\Categorizacion;
use ADIF\InventarioBundle\Entity\EstadoConservacion;
use ADIF\InventarioBundle\Entity\TipoActivo;
use ADIF\InventarioBundle\Entity\TipoVia;
use ADIF\InventarioBundle\Entity\EstadoInventario;
use ADIF\InventarioBundle\Entity\ValoresAtributo;
use ADIF\InventarioBundle\Entity\PropiedadValor;
use ADIF\InventarioBundle\Entity\FotoInventario;
use ADIF\InventarioBundle\Entity\TipoServicio;

/**
 * ActivoLineal
 *
 * @ORM\Table("activo_lineal")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\ActivoLinealRepository")
 * @UniqueEntity(
 *     fields={"operador", "linea", "division", "corredor", "tipoActivo", "tipoVia", "progresivaInicioTramo", "progresivaFinalTramo"},
 *     message="El activo lineal ya ha sido creado."
 * )
 * @Assert\Expression(
 *      "this.getProgresivaInicioTramo() < this.getProgresivaFinalTramo() or this.getTipoActivo() == null or this.getTipoActivo().getDenominacion() != 'Vía'",
 *      message="Para el tipo de activo Vía la progresiva de inicio debe ser menor a la final."
 * )
 * @Assert\Expression(
 *      "this.getProgresivaInicioTramo() < this.getProgresivaFinalTramo() or this.getTipoActivo() == null or this.getTipoActivo().getDenominacion() != 'Tendido Fibra Óptica'",
 *      message="Para el tipo de activo Tendido Fibra Óptica la progresiva de inicio debe ser menor a la final."
 * )
 * @Assert\Expression(
 *      "this.getProgresivaInicioTramo() == this.getProgresivaFinalTramo() or this.getTipoActivo() == null or this.getTipoActivo().getDenominacion() == 'Vía' or this.getTipoActivo().getDenominacion() == 'Tendido Fibra Óptica'",
 *      message="Para el tipo de activo seleccionado la progresiva de inicio debe ser igual a la final."
 * )
 * @InventarioAssert\ProgresivaContinua
 */
class ActivoLineal extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\Column(name="numero_interno", type="integer", nullable=true)
     */
    private $numeroInterno;

    /**
     * @ORM\Column(name="id_localidad", type="integer", nullable=true)
     */
    private $idLocalidad;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Localidad
     */
    protected $localidad;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Operador")
     * @ORM\JoinColumn(name="id_operador", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"denominacion" = "asc"})
     * @Assert\NotBlank()
     */
    private $operador;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"denominacion" = "asc"})
     * @Assert\NotBlank()
     */
    private $linea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Corredor")
     * @ORM\JoinColumn(name="id_corredor", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"denominacion" = "asc"})
     * @Assert\NotBlank()
     */
    private $corredor;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Divisiones")
     * @ORM\JoinColumn(name="id_division", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $division;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ramal")
     * @ORM\JoinColumn(name="id_ramal", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $ramal;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Categorizacion")
     * @ORM\JoinColumn(name="id_categorizacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $categoria;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoConservacion")
     * @ORM\JoinColumn(name="id_estado_conservacion", referencedColumnName="id", nullable=true)
     */
    private $estadoConservacion;

    /**
     * @ORM\OneToMany(targetEntity="FotoInventario", mappedBy="activoLineal",
     *                cascade={"persist","remove"})
     */
    protected $fotos;

    /**
     * @var string
     *
     * @ORM\Column(name="progresiva_inicio_tramo", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     *
     *
     */
    private $progresivaInicioTramo;

    /**
     * @var string
     *
     * @ORM\Column(name="progresiva_final_tramo", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     *
     */
    private $progresivaFinalTramo;

    /**
     * @var string
     */
    private $kilometraje;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoVia")
     * @ORM\JoinColumn(name="id_tipo_via", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @ORM\OrderBy({"denominacionCorta" = "asc", "denominacion" = "asc"})
     */
    private $tipoVia;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoActivo")
     * @ORM\JoinColumn(name="id_tipo_activo", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $tipoActivo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoInventario")
     * @ORM\JoinColumn(name="id_estado_inventario", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $estadoInventario;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Estacion")
     * @ORM\JoinColumn(name="id_estacion", referencedColumnName="id", nullable=true)
     * @Assert\Expression(
     *      "this.getTipoActivo() == null or this.getTipoActivo().getDenominacion() != 'Estación' or value != null ",
     *      message="Este valor es obligatorio."
     * )
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $estacion;

    /**
     * @var ValoresAtributo
     *
     * @ORM\ManyToMany(targetEntity="ValoresAtributo")
     * @ORM\JoinTable(name="activo_lineal_atributo_valor",
     *      joinColumns={@ORM\JoinColumn(name="id_activo_lineal", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_valor_atributo", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"denominacion" = "asc"})
     */
    private $valoresAtributo;

    /**
     * @var PropiedadValor
     *
     * @ORM\ManyToMany(targetEntity="PropiedadValor")
     * @ORM\JoinTable(name="activo_lineal_propiedad_valor",
     *      joinColumns={@ORM\JoinColumn(name="id_activo_lineal", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_valor_propiedad", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"valor" = "asc"})
     */
    private $valoresPropiedad;

    /**
     * @var string
     *
     * @ORM\Column(name="zona_via", type="string", length=100, nullable=true)
     * @Assert\Expression(
     *      "this.getTipoActivo() == null or this.getTipoActivo().getDenominacion() != 'Vía' or value != null ",
     *      message="Este valor es obligatorio."
     * )
     *
     */
    private $zonaVia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_inventario", type="boolean", nullable=true)
     */
    private $participaInventario;

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
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_ultimo_relevamiento", type="integer", nullable=true)
     */
    private $ultimoRelevamiento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_activo_fijo", type="boolean", nullable=true)
     */
    private $esActivoFijo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esta_sujeto_impuestos", type="boolean", nullable=true)
     */
    private $estaSujetoImpuestos;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_origen", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $valorOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="rubro", type="string", length=100, nullable=true)
     */
    private $rubro;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_amortizacion", type="string", length=100, nullable=true)
     */
    private $metodoAmortizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="vida_util", type="string", length=100, nullable=true)
     */
    private $vidaUtil;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoServicio")
     * @ORM\JoinColumn(name="id_tipo_servicio", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @ORM\OrderBy({"denominacionCorta" = "asc", "denominacion" = "asc"})
     */
    private $tipoServicio;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    /**
     * @ORM\OneToMany(targetEntity="ItemHojaRutaActivoLineal", mappedBy="activoLineal")
     */
    private $itemsHojaRutaActivoLineal;

    public function __construct() {
        $this->valoresAtributo = new ArrayCollection();
        $this->valoresPropiedad = new ArrayCollection();
        $this->itemsHojaRutaActivoLineal = new ArrayCollection();
        $this->fotos = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->getNumeroInterno();
    }

    public function toArray(){
        return get_object_vars($this);
    }

    public function __clone() {
        $this->id = null;
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
     * Set numeroInterno
     *
     * @param integer $numeroInterno
     * @return ActivoLineal
     */
    public function setNumeroInterno($numeroInterno)
    {
        $this->numeroInterno = $numeroInterno;

        return $this;
    }

    /**
     * Get numeroInterno
     *
     * @return integer
     */
    public function getNumeroInterno()
    {
        return $this->numeroInterno;
    }

    //Localidad de RecursosHumanosBundle:

    public function getIdLocalidad()
    {
        return $this->idLocalidad;
    }

    public function setIdLocalidad($idLocalidad)
    {
        $this->idLocalidad = $idLocalidad;

        return $this;
    }

    /**
     * Set localidad
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Localidad $localidad
     */
    public function setLocalidad($localidad)
    {
        if (null != $localidad) {
            $this->idLocalidad = $localidad->getId();
        } else {
            $this->idLocalidad = null;
        }

        $this->localidad = $localidad;
    }

    /**
     * Get localidad
     *
     * @return integer
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set operador
     *
     * @param integer $operador
     * @return ActivoLineal
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;

        return $this;
    }

    /**
     * Get operador
     *
     * @return integer
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     * @return ActivoLineal
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
     * Set corredor
     *
     * @param integer $corredor
     * @return ActivoLineal
     */
    public function setCorredor($corredor)
    {
        $this->corredor = $corredor;

        return $this;
    }

    /**
     * Get corredor
     *
     * @return integer
     */
    public function getCorredor()
    {
        return $this->corredor;
    }

    /**
     * Set division
     *
     * @param integer $division
     * @return ActivoLineal
     */
    public function setDivision($division)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return integer
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set ramal
     *
     * @param integer $ramal
     * @return ActivoLineal
     */
    public function setRamal($ramal)
    {
        $this->ramal = $ramal;

        return $this;
    }

    /**
     * Get ramal
     *
     * @return integer
     */
    public function getRamal()
    {
        return $this->ramal;
    }

    /**
     * Set categoria
     *
     * @param integer $categoria
     * @return ActivoLineal
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return integer
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set estadoConservacion
     *
     * @param integer $estadoConservacion
     * @return ActivoLineal
     */
    public function setEstadoConservacion($estadoConservacion)
    {
        $this->estadoConservacion = $estadoConservacion;

        return $this;
    }

    /**
     * Get estadoConservacion
     *
     * @return integer
     */
    public function getEstadoConservacion()
    {
        return $this->estadoConservacion;
    }

    /**
     * Set progresivaInicioTramo
     *
     * @param string $progresivaInicioTramo
     * @return ActivoLineal
     */
    public function setProgresivaInicioTramo($progresivaInicioTramo)
    {
        $this->progresivaInicioTramo = $progresivaInicioTramo;

        return $this;
    }

    /**
     * Get progresivaInicioTramo
     *
     * @return string
     */
    public function getProgresivaInicioTramo()
    {
        //return number_format($this->progresivaInicioTramo,3,'.','');
        return $this->progresivaInicioTramo;
    }

    /**
     * Set progresivaFinalTramo
     *
     * @param string $progresivaFinalTramo
     * @return ActivoLineal
     */
    public function setProgresivaFinalTramo($progresivaFinalTramo)
    {
        $this->progresivaFinalTramo = $progresivaFinalTramo;

        return $this;
    }

    /**
     * Get progresivaFinalTramo
     *
     * @return string
     */
    public function getProgresivaFinalTramo()
    {
        //return number_format($this->progresivaFinalTramo,3,'.','');
        return $this->progresivaFinalTramo;
    }


    function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;

        return $this;
    }

    function getKilometraje()
    {
        //return number_format($this->progresivaFinalTramo - $this->progresivaInicioTramo,3,',','');
        return number_format($this->progresivaFinalTramo - $this->progresivaInicioTramo,3,'.','');
    }

    /**
     * Set tipoVia
     *
     * @param integer $tipoVia
     * @return ActivoLineal
     */
    public function setTipoVia($tipoVia)
    {
        $this->tipoVia = $tipoVia;

        return $this;
    }

    /**
     * Get tipoVia
     *
     * @return integer
     */
    public function getTipoVia()
    {
        return $this->tipoVia;
    }

    /**
     * Set tipoActivo
     *
     * @param integer $tipoActivo
     * @return ActivoLineal
     */
    public function setTipoActivo($tipoActivo)
    {
        $this->tipoActivo = $tipoActivo;

        return $this;
    }

    /**
     * Get tipoActivo
     *
     * @return integer
     */
    public function getTipoActivo()
    {
        return $this->tipoActivo;
    }

    /**
     * Set estadoInventario
     *
     * @param integer $estadoInventario
     * @return ActivoLineal
     */
    public function setEstadoInventario($estadoInventario)
    {
        $this->estadoInventario = $estadoInventario;

        return $this;
    }

    /**
     * Get estadoInventario
     *
     * @return integer
     */
    public function getEstadoInventario()
    {
        return $this->estadoInventario;
    }

    /**
     * Set estacion
     *
     * @param integer $estacion
     * @return ActivoLineal
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

    public function getValoresAtributo() {
        return $this->valoresAtributo;
    }

    public function setValoresAtributo($valoresAtributo) {

        $this->getValoresAtributo()->clear();

        foreach($valoresAtributo as $valorAtributo) {
            $this->addValoresAtributo($valorAtributo);
        }

        return $this;
    }

    public function addValoresAtributo(ValoresAtributo $valorAtributo){
        $this->valoresAtributo[] = $valorAtributo;
    }

    public function getValoresPropiedad() {
        return $this->valoresPropiedad;
    }

    public function setValoresPropiedad($valoresPropiedad) {

        $this->getValoresPropiedad()->clear();

        foreach($valoresPropiedad as $valorPropiedad) {
            $this->addValoresPropiedad($valorPropiedad);
        }

        return $this;
    }

    public function addValoresPropiedad(PropiedadValor $valorPropiedad){
        $this->valoresPropiedad[] = $valorPropiedad;
    }

    /**
     * Set zonaVia
     *
     * @param string $zonaVia
     * @return ActivoLineal
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
     * Set participaInventario
     *
     * @param boolean $participaInventario
     * @return ActivoLineal
     */
    public function setParticipaInventario($participaInventario)
    {
        $this->participaInventario = $participaInventario;

        return $this;
    }

    /**
     * Get participaInventario
     *
     * @return boolean
     */
    public function getParticipaInventario()
    {
        return $this->participaInventario;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     * @return ActivoLineal
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
     * @return ActivoLineal
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return ActivoLineal
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
     * Set ultimoRelevamiento
     *
     * @param integer $ultimoRelevamiento
     * @return ActivoLineal
     */
    public function setUltimoRelevamiento($ultimoRelevamiento)
    {
        $this->ultimoRelevamiento = $ultimoRelevamiento;

        return $this;
    }

    /**
     * Get ultimoRelevamiento
     *
     * @return integer
     */
    public function getUltimoRelevamiento()
    {
        return $this->ultimoRelevamiento;
    }

    /**
     * Set esActivoFijo
     *
     * @param boolean $esActivoFijo
     * @return ActivoLineal
     */
    public function setEsActivoFijo($esActivoFijo)
    {
        $this->esActivoFijo = $esActivoFijo;

        return $this;
    }

    /**
     * Get esActivoFijo
     *
     * @return boolean
     */
    public function getEsActivoFijo()
    {
        return $this->esActivoFijo;
    }

    /**
     * Set estaSujetoImpuestos
     *
     * @param boolean $estaSujetoImpuestos
     * @return ActivoLineal
     */
    public function setEstaSujetoImpuestos($estaSujetoImpuestos)
    {
        $this->estaSujetoImpuestos = $estaSujetoImpuestos;

        return $this;
    }

    /**
     * Get estaSujetoImpuestos
     *
     * @return boolean
     */
    public function getEstaSujetoImpuestos()
    {
        return $this->estaSujetoImpuestos;
    }

    /**
     * Set valorOrigen
     *
     * @param string $valorOrigen
     * @return ActivoLineal
     */
    public function setValorOrigen($valorOrigen)
    {
        $this->valorOrigen = $valorOrigen;

        return $this;
    }

    /**
     * Get valorOrigen
     *
     * @return string
     */
    public function getValorOrigen()
    {
        return $this->valorOrigen;
    }

    /**
     * Set rubro
     *
     * @param string $rubro
     * @return ActivoLineal
     */
    public function setRubro($rubro)
    {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * Get rubro
     *
     * @return string
     */
    public function getRubro()
    {
        return $this->rubro;
    }

    /**
     * Set metodoAmortizacion
     *
     * @param string $metodoAmortizacion
     * @return ActivoLineal
     */
    public function setMetodoAmortizacion($metodoAmortizacion)
    {
        $this->metodoAmortizacion = $metodoAmortizacion;

        return $this;
    }

    /**
     * Get metodoAmortizacion
     *
     * @return string
     */
    public function getMetodoAmortizacion()
    {
        return $this->metodoAmortizacion;
    }

    /**
     * Set vidaUtil
     *
     * @param string $vidaUtil
     * @return ActivoLineal
     */
    public function setVidaUtil($vidaUtil)
    {
        $this->vidaUtil = $vidaUtil;

        return $this;
    }

    /**
     * Get vidaUtil
     *
     * @return string
     */
    public function getVidaUtil()
    {
        return $this->vidaUtil;
    }

    /**
     * Set tipoServicio
     *
     * @param integer $tipoServicio
     * @return ActivoLineal
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->tipoServicio = $tipoServicio;

        return $this;
    }

    /**
     * Get tipoServicio
     *
     * @return integer
     */
    public function getTipoServicio()
    {
        return $this->tipoServicio;
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

    /**
     * Add foto
     */
    public function addFoto(FotoInventario $foto) {
        $foto->setActivoLineal($this);
        $this->fotos[] = $foto;

        return $this;
    }

    /**
     * Remove foto
     *
     * @param FotoInventario $archivo
     */
    public function removeFoto(FotoInventario $foto) {
        $this->fotos->removeElement($foto);
    }

    /**
     * Get foto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFotos() {
        return $this->fotos;
    }
}
