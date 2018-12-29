<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\HojaRuta;
use ADIF\InventarioBundle\Entity\TipoMaterial;
use ADIF\InventarioBundle\Entity\EstadoHojaRuta;

/**
 * HojaRuta
 *
 * @ORM\Table(name="hoja_ruta")
 * @ORM\Entity
 */
class HojaRuta extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     */
    private $denominacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoMaterial")
     * @ORM\JoinColumn(name="id_tipo_material", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    private $tipoMaterial;

    /**
     * @ORM\Column(name="id_usuario_asignado", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $idUsuarioAsignado;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuarioAsignado;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoHojaRuta")
     * @ORM\JoinColumn(name="id_estado_hoja_ruta", referencedColumnName="id", nullable=false)
     *
     */
    private $estadoHojaRuta;

    /**
     * @var integer
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual("today")
     */
    private $fechaVencimiento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_inspeccion_tecnica", type="boolean", nullable=false)
     *
     */
    private $esInspeccionTecnica;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoRelevamiento")
     * @ORM\JoinColumn(name="id_tipo_relevamiento", referencedColumnName="id", nullable=true)
     */
    private $tipoRelevamiento;

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\InventarioBundle\Entity\HojaRuta", cascade={"all"})
     * @ORM\JoinColumn(name="id_levantamiento", referencedColumnName="id", nullable=true)
     */
    protected $levantamiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;

    /**
     * @ORM\OneToMany(targetEntity="ItemHojaRutaActivoLineal", mappedBy="hojaRuta", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $itemsHojaRutaActivoLineal;

    /**
     * @ORM\OneToMany(targetEntity="ItemHojaRutaNuevoProducido", mappedBy="hojaRuta", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $itemsHojaRutaNuevoProducido;

    /**
     * @ORM\OneToMany(targetEntity="ItemHojaRutaMaterialRodante", mappedBy="hojaRuta", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $itemsHojaRutaMaterialRodante;



    public function __construct() {
        $this->itemsHojaRutaActivoLineal = new ArrayCollection();
        $this->itemsHojaRutaNuevoProducido = new ArrayCollection();
        $this->itemsHojaRutaMaterialRodante = new ArrayCollection();
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
     * @return string
     */
    public function __toString() {
        return $this->getDenominacion();
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return HojaRuta
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
     * Set tipoMaterial
     *
     * @param integer $tipoMaterial
     * @return HojaRuta
     */
    public function setTipoMaterial($tipoMaterial)
    {
        $this->tipoMaterial = $tipoMaterial;

        return $this;
    }

    /**
     * Get tipoMaterial
     *
     * @return integer
     */
    public function getTipoMaterial()
    {
        return $this->tipoMaterial;
    }

    //Usuario de AutenticationBundle:

    public function getIdUsuarioAsignado()
    {
        return $this->idUsuarioAsignado;
    }

    public function setIdUsuarioAsignado($idUsuarioAsignado)
    {
        $this->idUsuarioAsignado = $idUsuarioAsignado;

        return $this;
    }

    /**
     * Set usuarioAsignado
     *
     * @param \ADIF\AutenticationBundle\Entity\Usuario $usuarioAsignado
     */
    public function setUsuarioAsignado($usuarioAsignado)
    {
        if (null != $usuarioAsignado) {
            $this->idUsuarioAsignado = $usuarioAsignado->getId();
        } else {
            $this->idUsuarioAsignado = null;
        }

        $this->usuarioAsignado = $usuarioAsignado;
    }

    /**
     * Get usuarioAsignado
     *
     * @return integer
     */
    public function getUsuarioAsignado()
    {
        return $this->usuarioAsignado;
    }

    /**
     * Set estadoHojaRuta
     *
     * @param integer $estadoHojaRuta
     * @return HojaRuta
     */
    public function setEstadoHojaRuta($estadoHojaRuta)
    {
        $this->estadoHojaRuta = $estadoHojaRuta;

        return $this;
    }

    /**
     * Get estadoHojaRuta
     *
     * @return integer
     */
    public function getEstadoHojaRuta()
    {
        return $this->estadoHojaRuta;
    }

    /**
     * Set fechaVencimiento
     *
     * @param integer $fechaVencimiento
     * @return HojaRuta
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return integer
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set esInspeccionTecnica
     *
     * @param boolean $esInspeccionTecnica
     * @return HojaRuta
     */
    public function setEsInspeccionTecnica($esInspeccionTecnica)
    {
        $this->esInspeccionTecnica = $esInspeccionTecnica;

        return $this;
    }

    /**
     * Get esInspeccionTecnica
     *
     * @return boolean
     */
    public function getEsInspeccionTecnica()
    {
        return $this->esInspeccionTecnica;
    }

    /**
     * Set tipoRelevamiento
     *
     * @param integer $tipoRelevamiento
     * @return HojaRuta
     */
    public function setTipoRelevamiento(TipoRelevamiento $tipoRelevamiento)
    {
        $this->tipoRelevamiento = $tipoRelevamiento;

        return $this;
    }

    /**
     * Get tipoRelevamiento
     *
     * @return integer
     */
    public function getTipoRelevamiento()
    {
        return $this->tipoRelevamiento;
    }

    /**
     * Set levantamiento
     *
     * @param \ADIF\InventarioBundle\Entity\HojaRuta $levantamiento
     * @return HojaRuta
     */
    public function setLevantamiento(\ADIF\InventarioBundle\Entity\HojaRuta $levantamiento = null) {
        $this->levantamiento = $levantamiento;
        return $this;
    }

    /**
     * Get levantamiento
     *
     * @return \ADIF\InventarioBundle\Entity\HojaRuta
     */
    public function getLevantamiento() {
        return $this->levantamiento;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return HojaRuta
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

    public function getItemsHojaRutaActivoLineal() {
        return $this->itemsHojaRutaActivoLineal;
    }

    public function setItemsHojaRutaActivoLineal($itemsHojaRutaActivoLineal) {
        $this->itemsHojaRutaActivoLineal = $itemsHojaRutaActivoLineal;

        return $this;
    }

    public function addItemsHojaRutaActivoLineal($itemsHojaRutaActivoLineal){
        $itemsHojaRutaActivoLineal->setHojaRuta($this);
        $this->itemsHojaRutaActivoLineal->add($itemsHojaRutaActivoLineal);
    }

    public function removeItemsHojaRutaActivoLineal($itemsHojaRutaActivoLineal){
        $this->itemsHojaRutaActivoLineal->removeElement($itemsHojaRutaActivoLineal);
    }

    public function getItemsHojaRutaMaterialRodante() {
        return $this->itemsHojaRutaMaterialRodante;
    }

    public function setItemsHojaRutaMaterialRodante($itemsHojaRutaMaterialRodante) {
        $this->itemsHojaRutaMaterialRodante = $itemsHojaRutaMaterialRodante;

        return $this;
    }

    public function addItemsHojaRutaMaterialRodante($itemsHojaRutaMaterialRodante){
        $itemsHojaRutaMaterialRodante->setHojaRuta($this);
        $this->itemsHojaRutaMaterialRodante->add($itemsHojaRutaMaterialRodante);
    }

    public function removeItemsHojaRutaMaterialRodante($itemsHojaRutaMaterialRodante){
        $this->itemsHojaRutaMaterialRodante->removeElement($itemsHojaRutaMaterialRodante);
    }

    public function getItemsHojaRutaNuevoProducido() {
        return $this->itemsHojaRutaNuevoProducido;
    }

    public function setItemsHojaRutaNuevoProducido($itemsHojaRutaNuevoProducido) {
        $this->itemsHojaRutaNuevoProducido = $itemsHojaRutaNuevoProducido;

        return $this;
    }

    public function addItemsHojaRutaNuevoProducido($itemsHojaRutaNuevoProducido){
        $itemsHojaRutaNuevoProducido->setHojaRuta($this);
        $this->itemsHojaRutaNuevoProducido->add($itemsHojaRutaNuevoProducido);
    }

    public function removeItemsHojaRutaNuevoProducido($itemsHojaRutaNuevoProducido){
        $this->itemsHojaRutaNuevoProducido->removeElement($itemsHojaRutaNuevoProducido);
    }

    public function getItemsHojaRutaMaterialProducidoDeObra() {
        return $this->itemsHojaRutaMaterialProducidoDeObra;
    }

    public function setItemsHojaRutaMaterialProducidoDeObra($itemsHojaRutaMaterialProducidoDeObra) {
        $this->itemsHojaRutaMaterialProducidoDeObra = $itemsHojaRutaMaterialProducidoDeObra;

        return $this;
    }

    public function addItemsHojaRutaMaterialProducidoDeObra($itemsHojaRutaMaterialProducidoDeObra){
        $itemsHojaRutaMaterialProducidoDeObra->setHojaRuta($this);
//        $itemsHojaRutaMaterialProducidoDeObra->setHojaRuta($this->getId()); // Prueba para ver si el problema estaba en como trae el ID
        $this->itemsHojaRutaMaterialProducidoDeObra->add($itemsHojaRutaMaterialProducidoDeObra);
    }

    public function removeItemsHojaRutaMaterialProducidoDeObra($itemsHojaRutaMaterialProducidoDeObra){
        $this->itemsHojaRutaMaterialProducidoDeObra->removeElement($itemsHojaRutaMaterialProducidoDeObra);
    }

}
