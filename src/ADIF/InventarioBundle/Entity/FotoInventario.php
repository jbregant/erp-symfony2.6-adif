<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra;


/**
 * FotoInventario
 *
 * @ORM\Table(name="foto_inventario")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class FotoInventario extends BaseAuditoria
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
      * @var \ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes
      *
      * @ORM\ManyToOne(targetEntity="CatalogoMaterialesRodantes")
      * @ORM\JoinColumn(name="id_catalogo_materiales_rodantes", referencedColumnName="id", nullable=true)
      *
      */
    private $catalogoMaterialesRodantes;

     /**
      * @var \ADIF\InventarioBundle\Entity\ActivoLineal
      *
      * @ORM\ManyToOne(targetEntity="ActivoLineal", inversedBy="fotos", cascade={"persist"})
      * @ORM\JoinColumn(name="id_activo_lineal", referencedColumnName="id")
      *
      */
    private $activoLineal;

     /**
      * @var \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos
      *
      * @ORM\ManyToOne(targetEntity="CatalogoMaterialesNuevos", inversedBy="fotos", cascade={"persist"})
      * @ORM\JoinColumn(name="id_catalogo_materiales_nuevos", referencedColumnName="id")
      *
      */
    private $catalogoMaterialesNuevos;

    /**
     * @var \ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra
     *
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesProducidosDeObra", inversedBy="fotos", cascade={"persist"})
     * @ORM\JoinColumn(name="id_catalogo_materiales_producidos_de_obra", referencedColumnName="id")
     *
     */
    private $catalogoMaterialesProducidosDeObra;

    /**
     * @var string
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"})
     * @Vich\UploadableField(mapping="foto_inventario", fileNameProperty="nombreFoto")
     *
     * @var File $foto
     */
    private $foto;

    /**
     * @ORM\Column(name="nombre_foto", type="string", length=255, nullable=true)
     *
     * @var string $nombreFoto
     */
    protected $nombreFoto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct() {
        $this->setIdEmpresa(1);
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
     * Set catalogoMaterialesRodantes
     *
     * @param integer $catalogoMaterialesRodantes
     * @return FotoInventario
     */
    public function setCatalogoMaterialesRodantes($catalogoMaterialesRodantes)
    {
        $this->catalogoMaterialesRodantes = $catalogoMaterialesRodantes;

        return $this;
    }

    /**
     * Get catalogoMaterialesRodantes
     *
     * @return integer
     */
    public function getCatalogoMaterialesRodantes()
    {
        return $this->catalogoMaterialesRodantes;
    }

    /**
     * Set activoLineal
     *
     * @param integer $activoLineal
     * @return FotoInventario
     */
    public function setActivoLineal($activoLineal)
    {
        $this->activoLineal = $activoLineal;

        return $this;
    }

    /**
     * Get activoLineal
     *
     * @return integer
     */
    public function getActivoLineal()
    {
        return $this->activoLineal;
    }

    /**
     * Set catalogoMaterialesNuevos
     *
     * @param integer $catalogoMaterialesNuevos
     * @return FotoInventario
     */
    public function setCatalogoMaterialesNuevos($catalogoMaterialesNuevos)
    {
        $this->catalogoMaterialesNuevos = $catalogoMaterialesNuevos;

        return $this;
    }

    /**
     * Get catalogoMaterialesNuevos
     *
     * @return integer
     */
    public function getCatalogoMaterialesNuevos()
    {
        return $this->catalogoMaterialesNuevos;
    }

    /**
     * Set catalogoMaterialesProducidosDeObra
     *
     * @param integer $catalogoMaterialesProducidosDeObra
     * @return FotoInventario
     */
    public function setCatalogoMaterialesProducidosDeObra(CatalogoMaterialesProducidosDeObra $catalogoMaterialesProducidosDeObra)
    {
        $this->catalogoMaterialesProducidosDeObra = $catalogoMaterialesProducidosDeObra;

        return $this;
    }

    /**
     * Get catalogoMaterialesProducidosDeObra
     *
     * @return integer
     */
    public function getCatalogoMaterialesProducidosDeObra()
    {
        return $this->catalogoMaterialesProducidosDeObra;
    }

    /**
     * Set foto
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $foto
     * @return FotoInventario
     *
     */
    public function setFoto(File $foto = null)
    {
        $this->foto = $foto;
        if ($foto instanceof File) {
            $this->setFechaUltimaActualizacion(new \DateTime());
        }

        return $this;
    }

    /**
     * Get foto
     *
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return FotoInventario
     */
    public function setIdEmpresa($idEmpresa = 1)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Set nombreFoto
     *
     * @param string $nombreFoto
     * @return ClienteProveedorFoto
     */
    public function setNombreFoto($nombreFoto) {
        $this->nombreFoto = $nombreFoto;

        return $this;
    }

    /**
     * Get nombreFoto
     *
     * @return string
     */
    public function getNombreFoto() {
        return $this->nombreFoto;
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
     * Get nombreArchivo
     *
     * @return string
     */
    public function getNombreArchivoClear() {
        return AdifApi::stringCleaner($this->nombreFoto);
    }
}
