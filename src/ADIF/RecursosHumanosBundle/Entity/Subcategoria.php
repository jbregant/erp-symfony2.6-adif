<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subcategoria
 *
 * @ORM\Table(name="subcategoria", indexes={@ORM\Index(name="fk_puesto_categoria_1", columns={"id_categoria"})})
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\SubcategoriaRepository")
 */
class Subcategoria extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_basico", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $montoBasico;
    
    /**
     * @var string
     *
     * @ORM\Column(name="categoria_recibo", type="string", length=200, nullable=true)
     */
    private $categoriaRecibo;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="es_categoria_02", type="boolean", nullable=true)
     */
    protected $esCategoria02;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Categoria", inversedBy="subcategorias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id")
     * })
     */
    private $idCategoria;

    /**
     * @ORM\OneToMany(targetEntity="Empleado", mappedBy="idSubcategoria")
     * */
    private $empleados;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_tiempo_completo", type="boolean", nullable=false)
     */
    protected $esTiempoCompleto;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sirhu_grado", type="integer", nullable=true)
     */
    private $sirhuGrado;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="sirhu_escalafon", type="integer", nullable=true)
     */
    private $sirhuEscalafon;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->empleados = new ArrayCollection();
        $this->esTiempoCompleto = true;
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNombre();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Subcategoria
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set montoBasico
     *
     * @param string $montoBasico
     * @return Subcategoria
     */
    public function setMontoBasico($montoBasico) {
        $this->montoBasico = $montoBasico;

        return $this;
    }

    /**
     * Get montoBasico
     *
     * @return string 
     */
    public function getMontoBasico() {
        return $this->montoBasico;
    }

    /**
     * Set idCategoria
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Categoria $idCategoria
     * @return Subcategoria
     */
    public function setIdCategoria(\ADIF\RecursosHumanosBundle\Entity\Categoria $idCategoria = null) {
        $this->idCategoria = $idCategoria;

        return $this;
    }

    /**
     * Get idCategoria
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Categoria 
     */
    public function getIdCategoria() {
        return $this->idCategoria;
    }

    /**
     * 
     * @return type
     */
    public function getCategoria() {
        return $this->getIdCategoria();
    }

    /**
     * Add empleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleados
     * @return Subcategoria
     */
    public function addEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleados) {
        $this->empleados[] = $empleados;

        return $this;
    }

    /**
     * Remove empleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleados
     */
    public function removeEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleados) {
        $this->empleados->removeElement($empleados);
    }

    /**
     * Get empleados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmpleados() {
        return $this->empleados;
    }
    
    public function setCategoriaRecibo($categoriaRecibo) 
    {
        $this->categoriaRecibo = $categoriaRecibo;
    }
    
    public function getCategoriaRecibo()
    {
        return $this->categoriaRecibo;
    }
    
    public function setEsCategoria02($esCategoria02) 
    {
        $this->esCategoria02 = $esCategoria02;
    }
    
    public function getEsCategoria02()
    {
        return $this->esCategoria02;
    }
    
    public function setEsTiempoCompleto($esTiempoCompleto)
    {
        $this->esTiempoCompleto = $esTiempoCompleto;
        
        return $this;
    }
    
    public function getEsTiempoCompleto()
    {
        return $this->esTiempoCompleto;
    }
    
    public function setSirhuGrado($sirhuGrado)
    {
        $this->sirhuGrado = $sirhuGrado;
        
        return $this;
    }
    
    public function getSirhuGrado()
    {   
        return $this->sirhuGrado;
    }
    
    public function setSirhuEscalafon($sirhuEscalafon)
    {
        $this->sirhuEscalafon = $sirhuEscalafon;
        
        return $this;
    }
    
    public function getSirhuEscalafon()
    {
        return $this->sirhuEscalafon;
    }
}
