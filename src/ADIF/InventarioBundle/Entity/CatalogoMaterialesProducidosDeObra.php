<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\GrupoMaterial;
use ADIF\InventarioBundle\Entity\UnidadMedida;
use ADIF\InventarioBundle\Entity\TipoEnvio;
use ADIF\InventarioBundle\Entity\EstadoInventario;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;
use ADIF\InventarioBundle\Entity\PropiedadValor;
use ADIF\InventarioBundle\Entity\FotoInventario;


/**
 * CatalogoMaterialesProducidosDeObra
 *
 * @ORM\Table(name="catalogo_material_producido_obra")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\CatalogoMaterialesProducidosDeObraRepository")
 * @UniqueEntity(
 *     fields={"denominacion", "grupoMaterial"},
 *     message="Los datos ingresados para este material ya ha sido utilizados."
 * )
 */
class CatalogoMaterialesProducidosDeObra extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="num", type="string", length=9, nullable=true)
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
     * @ORM\OneToMany(targetEntity="FotoInventario", mappedBy="catalogoMaterialesProducidosDeObra",
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
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id")
     */
    private $unidadMedida;

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
     * @var string
     *
     * @ORM\Column(name="codigo_barra", type="string", length=100, nullable=true)
     */
    private $codigoBarra;

    /**
     * @var ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido
     *
     */
    private $inventario;

    /**
     * @var PropiedadValor
     *
     * @ORM\ManyToMany(targetEntity="PropiedadValor")
     * @ORM\JoinTable(name="material_producido_obra_propiedad_valor",
     *      joinColumns={@ORM\JoinColumn(name="id_material_producido_obra", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_valor_propiedad", referencedColumnName="id")}
     * )
     */
    private $valoresPropiedad;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct() {
        $this->grupoMaterial = new ArrayCollection();
        $this->unidadMedida = new ArrayCollection();
        $this->tipoEnvio = new ArrayCollection();
        $this->estadoInventario = new ArrayCollection();
        $this->inventario = new InventarioMatNuevoProducido();
        $this->fotos = new ArrayCollection();
        $this->valoresPropiedad = new ArrayCollection();
        $this->participaInventario = true;
    }

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
     * Set numeroInterno
     *
     * @param integer $numeroInterno
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * Set participaInventario
     *
     * @param boolean $participaInventario
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * Set tipoEnvio
     *
     * @param integer $tipoEnvio
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * @return CatalogoMaterialesProducidosDeObra
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return CatalogoMaterialesProducidosDeObra
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

    /**
     * Get Propiedades
     */
    public function getValoresPropiedad() {
        return $this->valoresPropiedad;
    }

    /**
     * Set Propiedades
     */
    public function setValoresPropiedad($valoresPropiedad) {
        $this->getValoresPropiedad()->clear();

        foreach($valoresPropiedad as $valorPropiedad) {
            $this->addValoresPropiedad($valorPropiedad);
        }
        return $this;
    }

    /**
     * Add Propiedades
     */
    public function addValoresPropiedad(PropiedadValor $valorPropiedad){
        $this->valoresPropiedad[] = $valorPropiedad;
    }

    /**
     * Remove Propiedades
     */
    public function removeValoresPropiedad(PropiedadValor $valorPropiedad){
        $this->fotos->removeElement($valorPropiedad);
    }

    /**
     * Add foto
     */
    public function addFoto(FotoInventario $foto) {
        $foto->setCatalogoMaterialesProducidosDeObra($this);
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
