<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\InventarioBundle\Entity\GrupoRodante;
use ADIF\InventarioBundle\Entity\TipoRodante;
use ADIF\InventarioBundle\Entity\Marca;
use ADIF\InventarioBundle\Entity\Modelo;
use ADIF\InventarioBundle\Entity\EstadoConservacion;
use ADIF\InventarioBundle\Entity\Servicio;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Estacion;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Entity\TipoEnvio;
use ADIF\InventarioBundle\Entity\CodigoTrafico;
use ADIF\InventarioBundle\Entity\EstadoInventario;
use ADIF\InventarioBundle\Entity\PropiedadValor;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use ADIF\InventarioBundle\Entity\FotoInventario;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\RecursosHumanosBundle\Entity\Provincia;


use Doctrine\ORM\Mapping as ORM;

/**
 * CatalogoMaterialesRodantes
 *
 * @ORM\Table("catalogo_material_rodante")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\CatalogoMaterialesRodantesRepository")
 * @UniqueEntity(
 *     fields={"numeroVehiculo", "idGrupoRodante"},
 *     message="El material para este vehiculo y grupo rodante ya ha sido creado."
 * )
 */
class CatalogoMaterialesRodantes extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="GrupoRodante")
     * @ORM\JoinColumn(name="id_grupo_rodante", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idGrupoRodante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoRodante")
     * @ORM\JoinColumn(name="id_tipo_rodante", referencedColumnName="id", nullable=true)
     */
    private $idTipoRodante;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_vehiculo", type="string", length=100, nullable=true)
     */
    private $numeroVehiculo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Marca")
     * @ORM\JoinColumn(name="id_marca", referencedColumnName="id", nullable=true)
     */
    private $idMarca;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Modelo")
     * @ORM\JoinColumn(name="id_modelo", referencedColumnName="id", nullable=true)
     */
    private $idModelo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoConservacion")
     * @ORM\JoinColumn(name="id_estado_conservacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idEstadoConservacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumn(name="id_estado_servicio", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idEstadoServicio;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idLinea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Operador")
     * @ORM\JoinColumn(name="id_operador", referencedColumnName="id", nullable=true)
     */
    private $idOperador;

    /**
     * @ORM\OneToMany(targetEntity="FotoInventario", mappedBy="catalogoMaterialesRodantes",
     *                cascade={"persist","remove"})
     */
    protected $fotos;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=true)
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion_otro_lenguaje", type="string", length=100, nullable=true)
     */
    private $denominacionOtroLenguaje;

    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="string", length=100, nullable=true)
     */
    private $ubicacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_inventario", type="boolean")
     */
    private $participaInventario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_venta", type="boolean")
     */
    private $participaVenta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_activo_fijo", type="boolean")
     */
    private $esActivoFijo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoEnvio")
     * @ORM\JoinColumn(name="id_tipo_envio", referencedColumnName="id", nullable=true)
     */
    private $idTipoEnvio;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CodigoTrafico")
     * @ORM\JoinColumn(name="id_codigo_trafico", referencedColumnName="id", nullable=true)
     */
    private $idCodigoTrafico;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_actual", type="decimal", nullable=true)
     */
    private $valorActual;

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
     * @var boolean
     *
     * @ORM\Column(name="sujeto_a_impuesto", type="boolean")
     */
    private $esSujetoImpuesto;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Fabricante")
     * @ORM\JoinColumn(name="id_fabricante", referencedColumnName="id", nullable=true)
     */
    private $idFabricante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_adquisicion", type="datetime", nullable=true)
     * @Assert\LessThan("today")
     */
    private $fechaAdquisicion;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_adquisicion", type="decimal", nullable=true)
     */
    private $valorAdquisicion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

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
     * @ORM\ManyToOne(targetEntity="EstadoInventario")
     * @ORM\JoinColumn(name="id_estado_inventario", referencedColumnName="id", nullable=true)
     */
    private $idEstadoInventario;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


    /**
     * @var PropiedadValor
     *
     * @ORM\ManyToMany(targetEntity="PropiedadValor")
     * @ORM\JoinTable(name="material_rodante_propiedad_valor",
     *      joinColumns={@ORM\JoinColumn(name="id_material_rodante", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_valor_propiedad", referencedColumnName="id")}
     * )
     */
    private $valoresPropiedad;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Estacion")
     * @ORM\JoinColumn(name="id_estacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     */
    private $idEstacion;

    /**
     * @ORM\OneToMany(targetEntity="ItemHojaRutaMaterialRodante", mappedBy="materialRodante")
     */
    private $itemsHojaRutaMaterialRodante;


    public function __construct() {
       $this->valoresPropiedad = new ArrayCollection();
       $this->idLinea = new ArrayCollection();
       $this->idEstacion = new ArrayCollection();
       $this->fotos = new ArrayCollection();
       $this->itemsHojaRutaMaterialRodante = new ArrayCollection();
       $this->participaInventario = true;
       $this->esActivoFijo = true;
    }

    public function __toString() {
        return (string) $this->getDenominacion();
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
     * @return CatalogoMaterialesRodantes
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
     * Set Grupo Rodante
     *
     * @param integer $idGrupoRodante
     * @return CatalogoMaterialesRodantes
     */
    public function setIdGrupoRodante($idGrupoRodante)
    {
        $this->idGrupoRodante = $idGrupoRodante;
        return $this;
    }

    /**
     * Get Grupo Rodante
     *
     * @return integer
     */
    public function getIdGrupoRodante()
    {
        return $this->idGrupoRodante;
    }

    /**
     * Set Tipo Rodante
     *
     * @param integer $idTipoRodante
     * @return CatalogoMaterialesRodantes
     */
    public function setIdTipoRodante($idTipoRodante)
    {
        $this->idTipoRodante = $idTipoRodante;
        return $this;
    }

    /**
     * Get Tipo Rodante
     *
     * @return integer
     */
    public function getIdTipoRodante()
    {
        return $this->idTipoRodante;
    }

    /**
     * Set numeroVehiculo
     *
     * @param string $numeroVehiculo
     * @return CatalogoMaterialesRodantes
     */
    public function setNumeroVehiculo($numeroVehiculo)
    {
        $this->numeroVehiculo = $numeroVehiculo;
        return $this;
    }

    /**
     * Get numeroVehiculo
     *
     * @return string
     */
    public function getNumeroVehiculo()
    {
        return $this->numeroVehiculo;
    }

    /**
     * Set Marca
     *
     * @param integer $idMarca
     * @return CatalogoMaterialesRodantes
     */
    public function setIdMarca($idMarca)
    {
        $this->idMarca = $idMarca;
        return $this;
    }

    /**
     * Get Marca
     *
     * @return integer
     */
    public function getIdMarca()
    {
        return $this->idMarca;
    }

    /**
     * Set Modelo
     *
     * @param integer $idModelo
     * @return CatalogoMaterialesRodantes
     */
    public function setIdModelo($idModelo)
    {
        $this->idModelo = $idModelo;
        return $this;
    }

    /**
     * Get Modelo
     *
     * @return integer
     */
    public function getIdModelo()
    {
        return $this->idModelo;
    }

    /**
     * Set Estado Conservacion
     *
     * @param integer $idEstadoConservacion
     * @return CatalogoMaterialesRodantes
     */
    public function setIdEstadoConservacion($idEstadoConservacion)
    {
        $this->idEstadoConservacion = $idEstadoConservacion;
        return $this;
    }

    /**
     * Get Estado Conservacion
     *
     * @return integer
     */
    public function getIdEstadoConservacion()
    {
        return $this->idEstadoConservacion;
    }

    /**
     * Set Estado Servicio
     *
     * @param integer $idEstadoServicio
     * @return CatalogoMaterialesRodantes
     */
    public function setIdEstadoServicio($idEstadoServicio)
    {
        $this->idEstadoServicio = $idEstadoServicio;
        return $this;
    }

    /**
     * Get Estado Servicio
     *
     * @return integer
     */
    public function getIdEstadoServicio()
    {
        return $this->idEstadoServicio;
    }

    /**
     * Set Linea
     *
     * @param integer $idLinea
     * @return CatalogoMaterialesRodantes
     */
    public function setIdLinea(Linea $idLinea = null)
    {
        $this->idLinea = $idLinea;
        return $this;
    }

    /**
     * Get Linea
     *
     * @return integer
     */
    public function getIdLinea()
    {
        return $this->idLinea;
    }

    /**
     * Set Operador
     *
     * @param integer $idOperador
     * @return CatalogoMaterialesRodantes
     */
    public function setIdOperador($idOperador)
    {
        $this->idOperador = $idOperador;
        return $this;
    }

    /**
     * Get Operador
     *
     * @return integer
     */
    public function getIdOperador()
    {
        return $this->idOperador;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return CatalogoMaterialesRodantes
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

    /**
     * Set denominacionOtroLenguaje
     *
     * @param string $denominacionOtroLenguaje
     * @return CatalogoMaterialesRodantes
     */
    public function setDenominacionOtroLenguaje($denominacionOtroLenguaje)
    {
        $this->denominacionOtroLenguaje = $denominacionOtroLenguaje;
        return $this;
    }

    /**
     * Get denominacionOtroLenguaje
     *
     * @return string
     */
    public function getDenominacionOtroLenguaje()
    {
        return $this->denominacionOtroLenguaje;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return CatalogoMaterialesRodantes
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set participaInventario
     *
     * @param boolean $participaInventario
     * @return CatalogoMaterialesRodantes
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
     * Set participaVenta
     *
     * @param boolean $participaVenta
     * @return CatalogoMaterialesRodantes
     */
    public function setParticipaVenta($participaVenta)
    {
        $this->participaVenta = $participaVenta;
        return $this;
    }

    /**
     * Get participaVenta
     *
     * @return boolean
     */
    public function getParticipaVenta()
    {
        return $this->participaVenta;
    }

    /**
     * Set esActivoFijo
     *
     * @param boolean $esActivoFijo
     * @return CatalogoMaterialesRodantes
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
     * Set Tipo Envio
     *
     * @param integer $idTipoEnvio
     * @return CatalogoMaterialesRodantes
     */
    public function setIdTipoEnvio($idTipoEnvio)
    {
        $this->idTipoEnvio = $idTipoEnvio;
        return $this;
    }

    /**
     * Get Tipo Envio
     *
     * @return integer
     */
    public function getIdTipoEnvio()
    {
        return $this->idTipoEnvio;
    }

    /**
     * Set Codigo Trafico
     *
     * @param integer $idCodigoTrafico
     * @return CatalogoMaterialesRodantes
     */
    public function setIdCodigoTrafico($idCodigoTrafico)
    {
        $this->idCodigoTrafico = $idCodigoTrafico;
        return $this;
    }

    /**
     * Get Codigo Trafico
     *
     * @return integer
     */
    public function getIdCodigoTrafico()
    {
        return $this->idCodigoTrafico;
    }

    /**
     * Set valorActual
     *
     * @param string $valorActual
     * @return CatalogoMaterialesRodantes
     */
    public function setValorActual($valorActual)
    {
        $this->valorActual = $valorActual;
        return $this;
    }

    /**
     * Get valorActual
     *
     * @return string
     */
    public function getValorActual()
    {
        return $this->valorActual;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     * @return CatalogoMaterialesRodantes
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
     * @return CatalogoMaterialesRodantes
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
     * Set esSujetoImpuesto
     *
     * @param boolean $esSujetoImpuesto
     * @return CatalogoMaterialesRodantes
     */
    public function setEsSujetoImpuesto($esSujetoImpuesto)
    {
        $this->esSujetoImpuesto = $esSujetoImpuesto;
        return $this;
    }

    /**
     * Get esSujetoImpuesto
     *
     * @return boolean
     */
    public function getEsSujetoImpuesto()
    {
        return $this->esSujetoImpuesto;
    }

    /**
     * Set idFabricante
     *
     * @param string $idFabricante
     * @return CatalogoMaterialesRodantes
     */
    public function setIdFabricante($idFabricante)
    {
        $this->idFabricante = $idFabricante;
        return $this;
    }

    /**
     * Get idFabricante
     *
     * @return string
     */
    public function getIdFabricante()
    {
        return $this->idFabricante;
    }

    /**
     * Set fechaAdquisicion
     *
     * @param \DateTime $fechaAdquisicion
     * @return CatalogoMaterialesRodantes
     */
    public function setFechaAdquisicion($fechaAdquisicion)
    {
        $this->fechaAdquisicion = $fechaAdquisicion;
        return $this;
    }

    /**
     * Get fechaAdquisicion
     *
     * @return \DateTime
     */
    public function getFechaAdquisicion()
    {
        return $this->fechaAdquisicion;
    }

    /**
     * Set valorAdquisicion
     *
     * @param string $valorAdquisicion
     * @return CatalogoMaterialesRodantes
     */
    public function setValorAdquisicion($valorAdquisicion)
    {
        $this->valorAdquisicion = $valorAdquisicion;
        return $this;
    }

    /**
     * Get valorAdquisicion
     *
     * @return string
     */
    public function getValorAdquisicion()
    {
        return $this->valorAdquisicion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return CatalogoMaterialesRodantes
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set valorOrigen
     *
     * @param string $valorOrigen
     * @return CatalogoMaterialesRodantes
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
     * @return CatalogoMaterialesRodantes
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
     * @return CatalogoMaterialesRodantes
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
     * @return CatalogoMaterialesRodantes
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
     * Set Estado Inventario
     *
     * @param integer $idEstadoInventario
     * @return CatalogoMaterialesRodantes
     */
    public function setIdEstadoInventario($idEstadoInventario)
    {
        $this->idEstadoInventario = $idEstadoInventario;
        return $this;
    }



    /**
     * Get Estado Inventario
     *
     * @return integer
     */
    public function getIdEstadoInventario()
    {
        return $this->idEstadoInventario;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return CatalogoMaterialesRodantes
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
     * Get Valores Propiedad
     */
    public function getValoresPropiedad() {
        return $this->valoresPropiedad;
    }

    /**
     * Set Valores Propiedad
     */
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
     * Set idEstacion
     *
     * @param string $idEstacion
     * @return CatalogoMaterialesRodantes
     */
    public function setIdEstacion(Estacion $idEstacion = null)
    {
        $this->idEstacion = $idEstacion;
        return $this;
    }

    /**
     * Get $idEstacion
     *
     * @return string
     */
    public function getIdEstacion()
    {
        return $this->idEstacion;
    }


    /**
     * Add foto
     */
    public function addFoto(FotoInventario $foto) {
        $foto->setCatalogoMaterialesRodantes($this);
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
