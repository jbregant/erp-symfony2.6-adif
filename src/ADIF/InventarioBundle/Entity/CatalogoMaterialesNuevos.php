<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\GrupoMaterial;
use ADIF\InventarioBundle\Entity\UnidadMedida;
use ADIF\InventarioBundle\Entity\Fabricante;
use ADIF\InventarioBundle\Entity\TipoEnvio;
use ADIF\InventarioBundle\Entity\EstadoInventario;
use ADIF\InventarioBundle\Entity\PropiedadValor;
use ADIF\InventarioBundle\Entity\SetMaterial;
use ADIF\InventarioBundle\Entity\FotoInventario;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevosCompra;
use ADIF\ContableBundle\Entity\TipoImpuesto;


/**
 * CatalogoMaterialesNuevos
 *
 * @ORM\Table(name="catalogo_material_nuevo")
 * @ORM\Entity
 */
class CatalogoMaterialesNuevos extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="num", type="string", length=100, nullable=true)
     */
    private $num;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="GrupoMaterial")
     * @ORM\JoinColumn(name="id_grupo_material", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $grupoMaterial;


    /**
     * @ORM\Column(name="id_tipo_impuesto", type="integer", nullable=true)
     */
    private $idTipoImpuesto;

    /**
     * @var ADIF\ContableBundle\Entity\TipoImpuesto
     */
    protected $tipoImpuesto;


    /**
     * @ORM\OneToMany(targetEntity="FotoInventario", mappedBy="catalogoMaterialesNuevos",
     *                cascade={"persist","remove"})
     */
    protected $fotos;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
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
     * @ORM\Column(name="medida", type="string", length=100, nullable=true)
     */
    private $medida;

    /**
     * @var string
     *
     * @ORM\Column(name="peso", type="string", length=100, nullable=true)
     */
    private $peso;

    /**
     * @var string
     *
     * @ORM\Column(name="volumen", type="string", length=100, nullable=true)
     */
    private $volumen;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=true)
     */
    private $unidadMedida;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", nullable=true)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_valor", type="string", length=100, nullable=true)
     */
    private $tipoValor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_inventario", type="boolean")
     */
    private $participaInventario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_venta", type="boolean", nullable=true)
     */
    private $participaVenta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participa_compra", type="boolean", nullable=true)
     */
    private $participaCompra;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sujeto_a_impuesto", type="boolean", nullable=true)
     */
    private $sujetoAImpuesto;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Fabricante")
     * @ORM\JoinColumn(name="id_fabricante", referencedColumnName="id", nullable=true)
     */
    private $fabricante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoEnvio")
     * @ORM\JoinColumn(name="id_tipo_envio", referencedColumnName="id", nullable=true)
     */
    private $tipoEnvio;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barra", type="string", length=100, nullable=true)
     */
    private $codigoBarra;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_origen", type="decimal", nullable=true)
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
    private $estadoInventario;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;

    /**
     * @var ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevosCompra
     * @ORM\OneToOne(targetEntity="CatalogoMaterialesNuevosCompra", mappedBy="catalogoMaterialesNuevos", cascade={"persist", "remove"})
     *
     */
    private $catalogoMaterialesNuevosCompra;

    /**
     * @var ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido
     *
     */
    private $inventario;

    /**
     * @var ADIF\InventarioBundle\Entity\PropiedadValor
     *
     */
    //private $propiedades;
    /**
     * @var PropiedadValor
     *
     * @ORM\ManyToMany(targetEntity="PropiedadValor", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="catalogo_material_nuevo_propiedad_valor",
     *      joinColumns={@ORM\JoinColumn(name="id_catalogo_material_nuevo", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_valor_propiedad", referencedColumnName="id")}
     * )
     */
    private $valoresPropiedad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_set", type="boolean", nullable=true)
     */
    private $esSet;


    /**
     * @ORM\ManyToMany(targetEntity="SetMaterial", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="catalogo_material_nuevo_componente_set",
     *      joinColumns={@ORM\JoinColumn(name="id_material_nuevo", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_set_componente", referencedColumnName="id")}
     *      )
     */
    protected $setMateriales;


    /**
     * @var boolean
     *
     * @ORM\Column(name="transporte_por_pallet", type="boolean", nullable=true)
     */
    private $transportePallet;

    /**
     * @var boolean
     *
     * @ORM\Column(name="transporte_por_cajas", type="boolean", nullable=true)
     */
    private $transporteCajas;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_unidades_pallet", type="integer", nullable=true)
     */
    private $unidadesPallet;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_unidades_cajas", type="integer", nullable=true)
     */
    private $unidadesCajas;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_valoracion", type="string", length=100, nullable=true)
     */
    private $metodoValoracion;

    /**
     * @var string
     *
     * @ORM\Column(name="cuenta_contable", type="string", length=100, nullable=true)
     */
    private $cuentaContable;

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->getDenominacion();
    }

    public function __construct() {
        $this->grupoMaterial = new ArrayCollection();
        $this->tipoImpuesto = new ArrayCollection();
        $this->unidadMedida = new ArrayCollection();
        $this->fabricante = new ArrayCollection();
        $this->tipoEnvio = new ArrayCollection();
        $this->estadoInventario = new ArrayCollection();
        $this->catalogoMaterialesNuevosCompra = new CatalogoMaterialesNuevosCompra();
//        $this->catalogoMaterialesNuevosCompra = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->catalogoMaterialesNuevosCompra = new ArrayCollection();
        $this->inventario = new InventarioMatNuevoProducido();
//        $this->propiedades = new ArrayCollection();
        $this->valoresPropiedad = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setMateriales = new ArrayCollection();
        $this->fotos = new ArrayCollection();
        $this->participaInventario = true;
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
     * @return CatalogoMaterialesNuevos
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

    /**
     * Set num
     *
     * @param string $num
     * @return CatalogoMaterialesNuevos
     */
    public function setNum($num)
    {
        $this->num = $num;
        return $this;
    }

    /**
     * Get num
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set grupoMaterial
     *
     * @param integer $grupoMaterial
     * @return CatalogoMaterialesNuevos
     */
    public function setGrupoMaterial(GrupoMaterial $grupoMaterial = null)
    {
        $this->grupoMaterial = $grupoMaterial;
        return $this;
    }

    /**
     * Get grupoMaterial
     *
     * @return integer
     */
    public function getGrupoMaterial()
    {
        return $this->grupoMaterial;
    }

    //TipoImpuesto de ContableBundle:

    public function getIdTipoImpuesto()
    {
        return $this->idTipoImpuesto;
    }

    public function setIdTipoImpuesto($idTipoImpuesto)
    {
        $this->idTipoImpuesto = $idTipoImpuesto;

        return $this;
    }

    /**
     * Set tipoImpuesto
     *
     * @param \ADIF\ContableBundle\Entity\TipoImpuesto $tipoImpuesto
     */
    public function setTipoImpuesto($tipoImpuesto)
    {
        if (null != $tipoImpuesto ) {

//            $this->setIdTipoImpuesto($tipoImpuesto->getId());
            $this->idTipoImpuesto = $tipoImpuesto->getId();
        } else {
            $this->idTipoImpuesto = null;
        }

        $this->tipoImpuesto = $tipoImpuesto;
    }

    /**
     * Get tipoImpuesto
     *
     * @return integer
     */
    public function getTipoImpuesto()
    {
        return $this->tipoImpuesto;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return CatalogoMaterialesNuevos
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
     * @return CatalogoMaterialesNuevos
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
     * Set medida
     *
     * @param string $medida
     * @return CatalogoMaterialesNuevos
     */
    public function setMedida($medida)
    {
        $this->medida = $medida;
        return $this;
    }

    /**
     * Get medida
     *
     * @return string
     */
    public function getMedida()
    {
        return $this->medida;
    }

    /**
     * Set peso
     *
     * @param string $peso
     * @return CatalogoMaterialesNuevos
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
        return $this;
    }

    /**
     * Get peso
     *
     * @return string
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set volumen
     *
     * @param string $volumen
     * @return CatalogoMaterialesNuevos
     */
    public function setVolumen($volumen)
    {
        $this->volumen = $volumen;
        return $this;
    }

    /**
     * Get volumen
     *
     * @return string
     */
    public function getVolumen()
    {
        return $this->volumen;
    }

    /**
     * Set unidadMedida
     *
     * @param integer $unidadMedida
     * @return CatalogoMaterialesNuevos
     */
    public function setUnidadMedida(UnidadMedida $unidadMedida = null)
    {
        $this->unidadMedida = $unidadMedida;
        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return integer
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return CatalogoMaterialesNuevos
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set tipoValor
     *
     * @param string $tipoValor
     * @return CatalogoMaterialesNuevos
     */
    public function setTipoValor($tipoValor)
    {
        $this->tipoValor = $tipoValor;
        return $this;
    }

    /**
     * Get tipoValor
     *
     * @return string
     */
    public function getTipoValor()
    {
        return $this->tipoValor;
    }

    /**
     * Set participaInventario
     *
     * @param boolean $participaInventario
     * @return CatalogoMaterialesNuevos
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
     * @return CatalogoMaterialesNuevos
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
     * Set participaCompra
     *
     * @param boolean $participaCompra
     * @return CatalogoMaterialesNuevos
     */
    public function setParticipaCompra($participaCompra)
    {
        $this->participaCompra = $participaCompra;
        return $this;
    }

    /**
     * Get participaCompra
     *
     * @return boolean
     */
    public function getParticipaCompra()
    {
        return $this->participaCompra;
    }

    /**
     * Set sujetoAImpuesto
     *
     * @param boolean $sujetoAImpuesto
     * @return CatalogoMaterialesNuevos
     */
    public function setSujetoAImpuesto($sujetoAImpuesto)
    {
        $this->sujetoAImpuesto = $sujetoAImpuesto;
        return $this;
    }

    /**
     * Get sujetoAImpuesto
     *
     * @return boolean
     */
    public function getSujetoAImpuesto()
    {
        return $this->sujetoAImpuesto;
    }

    /**
     * Set fabricante
     *
     * @param integer $fabricante
     * @return CatalogoMaterialesNuevos
     */
    public function setFabricante(Fabricante $fabricante = null)
    {
        $this->fabricante = $fabricante;
        return $this;
    }

    /**
     * Get fabricante
     *
     * @return integer
     */
    public function getFabricante()
    {
        return $this->fabricante;
    }

    /**
     * Set tipoEnvio
     *
     * @param integer $tipoEnvio
     * @return CatalogoMaterialesNuevos
     */
    public function setTipoEnvio(TipoEnvio $tipoEnvio = null)
    {
        $this->tipoEnvio = $tipoEnvio;
        return $this;
    }

    /**
     * Get tipoEnvio
     *
     * @return integer
     */
    public function getTipoEnvio()
    {
        return $this->tipoEnvio;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return CatalogoMaterialesNuevos
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
     * Set codigoBarra
     *
     * @param string $codigoBarra
     * @return CatalogoMaterialesNuevos
     */
    public function setCodigoBarra($codigoBarra)
    {
        $this->codigoBarra = $codigoBarra;
        return $this;
    }

    /**
     * Get codigoBarra
     *
     * @return string
     */
    public function getCodigoBarra()
    {
        return $this->codigoBarra;
    }

    /**
     * Set valorOrigen
     *
     * @param string $valorOrigen
     * @return CatalogoMaterialesNuevos
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
     * @return CatalogoMaterialesNuevos
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
     * @return CatalogoMaterialesNuevos
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
     * @return CatalogoMaterialesNuevos
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
     * Set estadoInventario
     *
     * @param integer $estadoInventario
     * @return CatalogoMaterialesNuevos
     */
    public function setEstadoInventario(EstadoInventario $estadoInventario = null)
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return CatalogoMaterialesNuevos
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
     * Get catalogoMaterialesNuevosCompras
     *
     * @return integer
     */
    public function getCatalogoMaterialesNuevosCompra()
    {
        return $this->catalogoMaterialesNuevosCompra;
    }

     /**
     * Set catalogoMaterialesNuevosCompra
     *
     * @param \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevosCompra $catalogoMaterialesNuevosCompra
     * @return CatalogoMaterialesNuevos
     */
    public function setCatalogoMaterialesNuevosCompra(CatalogoMaterialesNuevosCompra $catalogoMaterialesNuevosCompra)
    {
        $this->catalogoMaterialesNuevosCompra = $catalogoMaterialesNuevosCompra;
        $catalogoMaterialesNuevosCompra->setCatalogoMaterialesNuevos($this);
        return $this;
    }


     /**
     * Set inventario
     *
     * @param integer $inventario
     * @return CatalogoMaterialesProducidosDeObra
     */
    public function setInventario(InventarioMatNuevoProducido $inventario = null)
    {
        $this->inventario = $inventario;
        return $this;
    }

    /**
     * Get inventario
     *
     * @return integer
     */
    public function getInventario()
    {
        return $this->inventario;
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
     * Add foto
     *
     * @param FotoInventario $foto
     * @return FotoInventario
     */
    public function addFoto(FotoInventario $foto) {
        $foto->setCatalogoMaterialesNuevos($this);
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


    /**
     * Set esSet
     * @param boolean $esSet
     * @return CatalogoMaterialesNuevos
     */
    public function setEsSet($esSet)
    {
        $this->esSet = $esSet;
        return $this;
    }


    /**
     * Get esSet
     * @return boolean
     */
    public function getEsSet()
    {
        return $this->esSet;
    }

    /**
     * Add setComponente
     *
     * @param \ADIF\InventarioBundle\Entity\SetMaterial $setMateriales
     * @return CatalogoMaterialesNuevos
     */
    public function addSetMateriales(\ADIF\InventarioBundle\Entity\SetMaterial $setMateriales) {
        $this->setMateriales[] = $setMateriales;
        return $this;
    }

    /**
     * Remove setComponente
     *
     * @param \ADIF\InventarioBundle\Entity\SetMaterial $setMateriales
     */
    public function removeSetMateriales(\ADIF\InventarioBundle\Entity\SetMaterial $setMateriales) {
        $this->setMateriales->removeElement($setMateriales);
    }

    /**
     * Get setComponente
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSetMateriales() {
        return $this->setMateriales;
    }


    /**
     * Set transportePallet
     * @param boolean $transportePallet
     * @return CatalogoMaterialesNuevos
     */
    public function setTransportePallet($transportePallet)
    {
        $this->transportePallet = $transportePallet;
        return $this;
    }

    /**
     * Get transportePallet
     * @return boolean
     */
    public function getTransportePallet()
    {
        return $this->transportePallet;
    }


    /**
     * Set transporteCajas
     * @param boolean $transporteCajas
     * @return CatalogoMaterialesNuevos
     */
    public function setTransporteCajas($transporteCajas)
    {
        $this->transporteCajas = $transporteCajas;
        return $this;
    }

    /**
     * Get transporteCajas
     * @return boolean
     */
    public function getTransporteCajas()
    {
        return $this->transporteCajas;
    }


    /**
     * Set unidadesPallet
     *
     * @param integer $unidadesPallet
     * @return CatalogoMaterialesNuevos
     */
    public function setUnidadesPallet($unidadesPallet)
    {
        $this->unidadesPallet = $unidadesPallet;
        return $this;
    }

    /**
     * Get unidadesPallet
     *
     * @return integer
     */
    public function getUnidadesPallet()
    {
        return $this->unidadesPallet;
    }

    /**
     * Set unidadesCajas
     *
     * @param integer $unidadesCajas
     * @return CatalogoMaterialesNuevos
     */
    public function setUnidadesCajas($unidadesCajas)
    {
        $this->unidadesCajas = $unidadesCajas;
        return $this;
    }

    /**
     * Get unidadesCajas
     *
     * @return integer
     */
    public function getUnidadesCajas()
    {
        return $this->unidadesCajas;
    }

    /**
     * Set metodoValoracion
     *
     * @param string $metodoValoracion
     * @return CatalogoMaterialesNuevos
     */
    public function setMetodoValoracion($metodoValoracion)
    {
        $this->metodoValoracion = $metodoValoracion;

        return $this;
    }

    /**
     * Get metodoValoracion
     *
     * @return string
     */
    public function getMetodoValoracion()
    {
        return $this->metodoValoracion;
    }


    /**
     * Set cuentaContable
     *
     * @param string $cuentaContable
     * @return CatalogoMaterialesNuevos
     */
    public function setCuentaContable($cuentaContable)
    {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return string
     */
    public function getCuentaContable()
    {
        return $this->cuentaContable;
    }
}
