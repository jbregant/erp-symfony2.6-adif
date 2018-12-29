<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use ADIF\RecursosHumanosBundle\Repository\EmpleadoRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * Empleado
 *
 * @ORM\Table(name="empleado", indexes={
 *      @ORM\Index(name="persona", columns={"id_persona"}), 
 *      @ORM\Index(name="cuenta", columns={"id_cuenta"}), 
 *      @ORM\Index(name="gerencia", columns={"id_gerencia"}), 
 *      @ORM\Index(name="subcategoria", columns={"id_subcategoria"}), 
 *      @ORM\Index(name="sector", columns={"id_sector"}), 
 *      @ORM\Index(name="subgerencia_1", columns={"id_subgerencia"}), 
 *      @ORM\Index(name="area_1", columns={"id_area"})
 *  })
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\EmpleadoRepository")
 * @UniqueEntity(fields={"nroLegajo","fechaBaja"}, ignoreNull=false, message="El número de legajo ya se encuentra en uso.")
 */
class Empleado extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="foto", type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_legajo", type="integer", unique=true, nullable=false)
     */
    private $nroLegajo;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_inicio_antiguedad", type="date", nullable=false)
     */
    private $fechaInicioAntiguedad;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_area", referencedColumnName="id", nullable=false)
     * })
     */
    private $idArea;

    /**
     * @var CuentaBancaria
     *
     * @ORM\ManyToOne(targetEntity="CuentaBancariaPersona", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cuenta", referencedColumnName="id", nullable=true)
     * })
     */
    private $idCuenta;

    /**
     * @var Gerencia
     *
     * @ORM\ManyToOne(targetEntity="Gerencia", inversedBy="empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_gerencia", referencedColumnName="id", nullable=true)
     * })
     */
    private $idGerencia;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_persona", referencedColumnName="id", nullable=false)
     * })
     */
    private $persona;

    /**
     * @var Sector
     *
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sector", referencedColumnName="id")
     * })
     */
    private $idSector;

    /**
     * @var Subcategoria
     *
     * @ORM\ManyToOne(targetEntity="Subcategoria", inversedBy="empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_subcategoria", referencedColumnName="id", nullable=false)
     * })
     */
    private $idSubcategoria;

    /**
     * @var Subgerencia
     *
     * @ORM\ManyToOne(targetEntity="Subgerencia", inversedBy="empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_subgerencia", referencedColumnName="id", nullable=true)
     * })
     */
    private $idSubgerencia;

    /**
     *
     * @var EstudioEmpleado 
     * 
     * @ORM\OneToMany(targetEntity="EstudioEmpleado", mappedBy="idEmpleado", cascade={"persist"})
     */
    private $estudios;

    /**
     *
     * @var Familiar 
     * 
     * @ORM\OneToMany(targetEntity="Familiar", mappedBy="idEmpleado", cascade={"persist"})
     */
    private $familiares;

    /**
     *
     * @var ContactoEmergencia 
     * 
     * @ORM\OneToMany(targetEntity="ContactoEmergencia", mappedBy="idEmpleado", cascade={"persist"})
     */
    private $contactosEmergencia;

    /**
     *
     * @var EmpleadoArchivo 
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoArchivo", mappedBy="idEmpleado", cascade={"persist"})
     */
    private $archivos;

    /**
     *
     * @var Concepto
     * 
     * @ORM\ManyToMany(targetEntity="Concepto", cascade={"persist"})
     */
    private $conceptos;

    /**
     *
     * @var EmpleadoNovedad
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoNovedad", mappedBy="idEmpleado", cascade={"persist"})
     */
    private $novedades;

    /**
     *
     * @ORM\Column(name="acdt", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $acdt;

    /**
     * @var RangoRemuneracion
     *
     * @ORM\ManyToOne(targetEntity="RangoRemuneracion")
     * @ORM\JoinColumn(name="id_rango_remuneracion", referencedColumnName="id", nullable=true)
     * 
     */
    private $rangoRemuneracion;
    
    /**
     * @var RangoRemuneracion
     *
     * @ORM\OneToMany(targetEntity="EmpleadoHistoricoRangoRemuneracion", mappedBy="empleado")
     */
    private $rangoRemuneracionHistorica;

    /**
     * @ORM\OneToMany(targetEntity="Formulario572", mappedBy="empleado", cascade="all")
     */
    private $formularios572;

    /**
     * @ORM\OneToOne(targetEntity="Formulario649", mappedBy="empleado", cascade="all")
     */
    private $formulario649;

    /**
     * @var Condicion
     *
     * @ORM\ManyToOne(targetEntity="Condicion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_condicion", referencedColumnName="id", nullable=true)
     * })
     */
    private $condicion;

    /**
     *
     * @var EmpleadoTipoContrato
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoTipoContrato", mappedBy="empleado", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $tiposContrato;

    /**
     *
     * @var EmpleadoTipoLicencia
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoTipoLicencia", mappedBy="empleado", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $tiposLicencia;

    /**
     *
     * @var EmpleadoObraSocial
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoObraSocial", mappedBy="empleado", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $obrasSociales;

    /**
     *
     * @var EmpleadoSectorHistorico
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoSectorHistorico", mappedBy="empleado")
     */
    private $puestosHistoricos;

    /**
     *
     * @var EmpleadoSubcategoriaHistorico
     * 
     * @ORM\OneToMany(targetEntity="EmpleadoSubcategoriaHistorico", mappedBy="empleado")
     * @ORM\OrderBy({"fechaDesde" = "ASC"})
     */
    private $subcategoriasHistoricas;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_egreso", type="date", nullable=true)
     */
    private $fechaEgreso;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\MotivoEgreso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_motivo_egreso", referencedColumnName="id", nullable=true)
     * })
     */
    private $motivoEgreso;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default" = 1})
     */
    private $activo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_escala_diciembre", type="boolean", nullable=false, options={"default" = 0})
     */
    private $aplicaEscalaDiciembre = false;
	
	
	/**
     * @var puesto
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Puesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_puesto", referencedColumnName="id", nullable=true)
     * })
     */
	private $puesto;
	
	 /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Empleado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_superior", referencedColumnName="id", nullable=true)
     * })
     */
    private $superior;
	
	 /**
     * @var NivelOrganizacional
     *
     * @ORM\ManyToOne(targetEntity="NivelOrganizacional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_nivel_organizacional", referencedColumnName="id", nullable=true)
     * })
     */
	private $nivelOrganizacional;
    
    /**
     *
     * @var LiquidacionEmpleado
     * 
     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado", mappedBy="empleado")
     */
    private $liquidacionEmpleados;
	

    public function __construct() {
        $this->estudios = new ArrayCollection();
        $this->familiares = new ArrayCollection();
        $this->contactosEmergencia = new ArrayCollection();
        $this->conceptos = new ArrayCollection();
        $this->novedades = new ArrayCollection();
        $this->archivos = new ArrayCollection();
        $this->tiposContrato = new ArrayCollection();
        $this->tiposLicencia = new ArrayCollection();
        $this->obrasSociales = new ArrayCollection();
        $this->puestosHistoricos = new ArrayCollection();
        $this->subcategoriasHistoricas = new ArrayCollection();
        $this->formularios572 = new ArrayCollection();
        $this->liquidacionEmpleados = new ArrayCollection();
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
     * Set foto
     *
     * @param string $foto
     * @return Empleado
     */
    public function setFoto($foto) {
		
        $this->foto = $foto;
		
        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto() {
        return $this->foto;
    }

    /**
     * Set nroLegajo
     *
     * @param integer $nroLegajo
     * @return Empleado
     */
    public function setNroLegajo($nroLegajo) {
        $this->nroLegajo = $nroLegajo;

        return $this;
    }

    /**
     * Get nroLegajo
     *
     * @return integer 
     */
    public function getNroLegajo() {
        return $this->nroLegajo;
    }

    /**
     * Set fechaInicioAntiguedad
     *
     * @param DateTime $fechaInicioAntiguedad
     * @return Empleado
     */
    public function setFechaInicioAntiguedad($fechaInicioAntiguedad) {
        $this->fechaInicioAntiguedad = $fechaInicioAntiguedad;

        return $this;
    }

    /**
     * Get fechaInicioAntiguedad
     *
     * @return DateTime 
     */
    public function getFechaInicioAntiguedad() {
        return $this->fechaInicioAntiguedad;
    }

    /**
     * Set idArea
     *
     * @param Area $idArea
     * @return Empleado
     */
    public function setIdArea(Area $idArea = null) {
        $this->idArea = $idArea;

        return $this;
    }

    /**
     * Get idArea
     *
     * @return Area 
     */
    public function getIdArea() {
        return $this->idArea;
    }

    /**
     * Set idCuenta
     *
     * @param CuentaBancariaPersona $idCuenta
     * @return Empleado
     */
    public function setIdCuenta(CuentaBancariaPersona $idCuenta = null) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * Get idCuenta
     *
     * @return CuentaBancariaPersona 
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * Set idGerencia
     *
     * @param Gerencia $idGerencia
     * @return Empleado
     */
    public function setIdGerencia(Gerencia $idGerencia = null) {
        $this->idGerencia = $idGerencia;

        return $this;
    }

    /**
     * Get idGerencia
     *
     * @return Gerencia 
     */
    public function getIdGerencia() {
        return $this->idGerencia;
    }

    /**
     * Set persona
     *
     * @param Persona $persona
     * @return Empleado
     */
    public function setPersona(Persona $persona = null) {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return Persona 
     */
    public function getPersona() {
        return $this->persona;
    }

    /**
     * Set idSector
     *
     * @param Sector $idSector
     * @return Empleado
     */
    public function setIdSector(Sector $idSector = null) {
        $this->idSector = $idSector;

        return $this;
    }

    /**
     * Get idSector
     *
     * @return Sector 
     */
    public function getIdSector() {
        return $this->idSector;
    }

    /**
     * Set idSubcategoria
     *
     * @param Subcategoria $idSubcategoria
     * @return Empleado
     */
    public function setIdSubcategoria(Subcategoria $idSubcategoria = null) {
        $this->idSubcategoria = $idSubcategoria;

        return $this;
    }

    /**
     * Get idSubcategoria
     *
     * @return Subcategoria 
     */
    public function getIdSubcategoria() {
        return $this->idSubcategoria;
    }

    /**
     * Set idSubgerencia
     *
     * @param Subgerencia $idSubgerencia
     * @return Empleado
     */
    public function setIdSubgerencia(Subgerencia $idSubgerencia = null) {
        $this->idSubgerencia = $idSubgerencia;

        return $this;
    }

    /**
     * Get idSubgerencia
     *
     * @return Subgerencia 
     */
    public function getIdSubgerencia() {
        return $this->idSubgerencia;
    }

    /**
     * Get cuenta
     * 
     * @return CuentaBancaria
     */
    public function getCuenta() {
        return $this->getIdCuenta();
    }

    /**
     * Get Categoria
     *
     * @return Categoria 
     */
    public function getCategoria() {
        return $this->getIdSubcategoria() ? $this->getIdSubcategoria()->getIdCategoria() : null;
    }

    /**
     * Get Convenio
     *
     * @return Convenio 
     */
    public function getConvenio() {
        return $this->getIdSubcategoria() ? ($this->getIdSubcategoria()->getIdCategoria() ? $this->getIdSubcategoria()->getIdCategoria()->getIdConvenio() : null) : null;
    }

    /**
     * Get estudios
     *
     * @return EstudioEmpleado 
     */
    public function getEstudios() {
        return $this->estudios;
    }

    /**
     * Set estudios
     *
     * @param ArrayCollection 
     */
    public function setEstudios(ArrayCollection $estudios) {
        $this->estudios = $estudios;
        return $this;
    }

    /**
     * Get familiares
     *
     * @return Familiar
     */
    public function getFamiliares() {
        return $this->familiares;
    }

    /**
     * Set familiares
     *
     * @param ArrayCollection 
     */
    public function setFamiliares(ArrayCollection $familiares) {
        $this->familiares = $familiares;
        return $this;
    }

    /**
     * Get contactosEmergencia
     *
     * @return ContactoEmergencia
     */
    public function getContactosEmergencia() {
        return $this->contactosEmergencia;
    }

    /**
     * Set contactosEmergencia
     *
     * @param ArrayCollection 
     */
    public function setContactosEmergencia(ArrayCollection $contactosEmergencia) {
        $this->contactosEmergencia = $contactosEmergencia;
        return $this;
    }

    /**
     * Get archivos
     *
     * @return EmpleadoArchivo
     */
    public function getArchivos() {
        return $this->archivos;
    }

    /**
     * Set archivos
     *
     * @param ArrayCollection 
     */
    public function setArchivos(ArrayCollection $archivos) {
        $this->archivos = $archivos;
        return $this;
    }

    /**
     * To string
     * 
     * @return string
     */
    public function __toString() {
        return $this->getPersona()->__toString();
    }

    /**
     * Add estudios
     *
     * @param EstudioEmpleado $estudios
     * @return Empleado
     */
    public function addEstudio(EstudioEmpleado $estudios) {
        $this->estudios[] = $estudios;

        return $this;
    }

    /**
     * Remove estudios
     *
     * @param EstudioEmpleado $estudios
     */
    public function removeEstudio(EstudioEmpleado $estudios) {
        $this->estudios->removeElement($estudios);
    }

    /**
     * Add familiares
     *
     * @param Familiar $familiares
     * @return Empleado
     */
    public function addFamiliar(Familiar $familiares) {
        $this->familiares[] = $familiares;

        return $this;
    }

    /**
     * Remove familiares
     *
     * @param Familiar $familiares
     */
    public function removeFamiliar(Familiar $familiares) {
        $this->familiares->removeElement($familiares);
    }

    /**
     * Add contactosEmergencia
     *
     * @param ContactoEmergencia $contactosEmergencia
     * @return Empleado
     */
    public function addContactoEmergencia(ContactoEmergencia $contactosEmergencia) {
        $this->contactosEmergencia[] = $contactosEmergencia;

        return $this;
    }

    /**
     * Remove contactosEmergencia
     *
     * @param ContactoEmergencia $contactosEmergencia
     */
    public function removeContactoEmergencia(ContactoEmergencia $contactosEmergencia) {
        $this->contactosEmergencia->removeElement($contactosEmergencia);
    }

    /**
     * Add conceptos
     *
     * @param Concepto $conceptos
     * @return Empleado
     */
    public function addConcepto(Concepto $conceptos) {
        $this->conceptos[] = $conceptos;

        return $this;
    }

    /**
     * Remove conceptos
     *
     * @param Concepto $conceptos
     */
    public function removeConcepto(Concepto $conceptos) {
        $this->conceptos->removeElement($conceptos);
    }

    /**
     * Get conceptos
     *
     * @return Collection 
     */
    public function getConceptos() {
        return $this->conceptos;
    }

    /**
     * Add archivos
     *
     * @param EmpleadoArchivo $archivo
     * @return Empleado
     */
    public function addArchivo(EmpleadoArchivo $archivo) {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove archivo
     *
     * @param EmpleadoArchivo $archivo
     */
    public function removeArchivo(EmpleadoArchivo $archivo) {
        $this->archivos->removeElement($archivo);
    }

    /**
     * Add novedades
     *
     * @param EmpleadoNovedad $novedades
     * @return Empleado
     */
    public function addNovedad(EmpleadoNovedad $novedades) {
        $this->novedades[] = $novedades;

        return $this;
    }

    /**
     * Remove novedades
     *
     * @param EmpleadoNovedad $novedades
     */
    public function removeNovedad(EmpleadoNovedad $novedades) {
        $this->novedades->removeElement($novedades);
    }

    /**
     * Get novedades
     *
     * @return Collection 
     */
    public function getNovedades() {
        return $this->novedades;
    }

    /**
     * Set novedades
     *
     * @param Collection 
     */
    public function setNovedades(ArrayCollection $novedades) {
        $this->novedades = $novedades;
        return $this;
    }

    /**
     * Set subcategoria
     *
     * @param Subcategoria $subcategoria
     * @return Empleado
     */
    public function setSubcategoria(Subcategoria $subcategoria = null) {
        return $this->setSubcategoria($subcategoria);
    }

    /**
     * Get subcategoria
     *
     * @return Subcategoria 
     */
    public function getSubcategoria() {
        return $this->getIdSubcategoria();
    }

    /**
     * Set acdt
     *
     * @param string $acdt
     * @return Concepto
     */
    public function setAcdt($acdt) {
        $this->acdt = $acdt;

        return $this;
    }

    /**
     * Get acdt
     *
     * @return string 
     */
    public function getAcdt() {
        return $this->acdt;
    }

    public function getConceptosActivos() {
        return $this->conceptos->filter(
                        function($entry) {
                    return in_array($entry->getActivo(), array(true));
                }
        );
    }

    /**
     * Busca un concepto con codigo $codigo
     * 
     * @return Concepto
     */
    public function getConceptoCodigo($codigo, $activo = true) {
        $result = $this->conceptos->filter(
                function($entry) use ($codigo, $activo) {
            return in_array($entry->getCodigo(), array($codigo)) && in_array($entry->getActivo(), array($activo));
        }
        );
        return (!$result->isEmpty() ? $result->first() : null);
    }

    /**
     * Busca una novedad con codigo $codigo entre dos fechas
     * 
     * @param string $codigo código de concepto de la novedad
     * @param DateTime $fechaInicio fecha inicio de rango
     * @param DateTime $fechaFin fecha de fin de rango
     * @return NovedadEmpleado
     */
    public function getNovedadCodigo($codigo, $fechaInicio, $fechaFin) {
        $novedad = null;
        foreach ($this->novedades as $novedadEmpleado) {
            /* @var $novedadEmpleado EmpleadoNovedad */
            if ($novedadEmpleado->getIdConcepto()->getCodigo() == $codigo &&
                    $novedadEmpleado->getIdConcepto()->getActivo() &&
                    $novedadEmpleado->getFechaAlta() >= $fechaInicio &&
                    $novedadEmpleado->getFechaAlta() <= $fechaFin &&
                    $novedadEmpleado->getFechaBaja() == null) {
                $novedad = $novedadEmpleado;
            }
        }
        return $novedad;
    }

    /**
     * Busca una lista de novedades con codigo $codigo entre dos fechas
     * 
     * @param string $codigo código de concepto de la novedad
     * @param DateTime $fechaInicio fecha inicio de rango
     * @param DateTime $fechaFin fecha de fin de rango
     * @return NovedadEmpleado
     */
    public function getNovedadesCodigo($codigo, $fechaInicio, $fechaFin) {
        $novedades = new ArrayCollection();
        foreach ($this->novedades as $novedadEmpleado) {
            /* @var $novedadEmpleado EmpleadoNovedad */
            if ($novedadEmpleado->getIdConcepto()->getCodigo() == $codigo &&
                    $novedadEmpleado->getIdConcepto()->getActivo() &&
                    $novedadEmpleado->getFechaAlta() >= $fechaInicio &&
                    $novedadEmpleado->getFechaAlta() <= $fechaFin &&
                    $novedadEmpleado->getFechaBaja() == null) {
                $novedades->add($novedadEmpleado);
            }
        }
        return $novedades->isEmpty() ? null : $novedades;
    }

    /**
     * 
     * Cantidad de años de antigüedad del empleado
     * 
     * @return int
     */
    public function getAniosAntiguedad($fechaCierreNovedades) {
        // tener en cuenta ultimo dia habil
        // se calcula en base a la fecha de ingreso
        // SI es menor a 1 año => 0
        $fecha_clon = clone($fechaCierreNovedades);
        $ultimo_dia_mes = new DateTime(date('Y-m-t', strtotime($fecha_clon->format('Y-m-d'))));
        $difAntiguedad = date_diff($ultimo_dia_mes, $this->getFechaIngreso());
        return $difAntiguedad->y > 0 ? $difAntiguedad->y : 0;
    }

    /**
     * 
     * Cantidad de años de antigüedad del empleado
     * 
     * @return int
     */
    public function getAniosAntiguedadIndemnizacion($fechaCierreNovedades) {
        $fecha_clon = clone($fechaCierreNovedades);
        $ultimo_dia_mes = new DateTime(date('Y-m-t', strtotime($fecha_clon->format('Y-m-d'))));
        $difAntiguedad = date_diff($ultimo_dia_mes, $this->getFechaInicioAntiguedad());
        return $difAntiguedad->y + ($difAntiguedad->m > 3 ? 1 : 0);
    }

    /**
     * Set formulario649
     *
     * @param Formulario649 $formulario649
     * @return Empleado
     */
    public function setFormulario649(Formulario649 $formulario649 = null) {
        $this->formulario649 = $formulario649;

        return $this;
    }

    /**
     * Get formulario649
     *
     * @return Formulario649
     */
    public function getFormulario649() {
        return $this->formulario649;
    }

    /**
     * Set rangoRemuneracion
     *
     * @param RangoRemuneracion $rangoRemuneracion
     * @return Empleado
     */
    public function setRangoRemuneracion(RangoRemuneracion $rangoRemuneracion = null) {
        $this->rangoRemuneracion = $rangoRemuneracion;

        return $this;
    }

    /**
     * Get rangoRemuneracion
     *
     * @return RangoRemuneracion 
     */
    public function getRangoRemuneracion() {
        return $this->rangoRemuneracion;
    }

    /**
     * Tiene Conyuge
     *
     * @return int
     */
    public function tieneConyuge() {
        $result = $this->familiares->filter(
                function($entry) {
            return in_array($entry->getIdTipoRelacion()->getId(), array(TipoRelacion::__CONYUGE));
        }
        );
        return $result->isEmpty() ? 0 : 1;
    }

    /**
     * Get cantidad de hijos
     *
     * @return int
     */
    public function getCantidadHijos() {
        $result = $this->familiares->filter(
                function($entry) {
            return in_array($entry->getIdTipoRelacion()->getId(), array(TipoRelacion::__HIJO));
        }
        );
        return $result->count();
    }

    /**
     * Get cantidad de hijos
     *
     * @return int
     */
    public function getCantidadHijosEnGuarderia() {
        $result = $this->familiares->filter(
                function($entry) {
            return in_array($entry->getIdTipoRelacion()->getId(), array(TipoRelacion::__HIJO)) &&
                    in_array($entry->getEnGuarderia(), array(true));
        }
        );
        return $result->count();
    }

    /**
     * Get cantidad de hijos
     *
     * @return int
     */
    public function getFamiliaresACargoOS() {
        $result = $this->familiares->filter(
                function($entry) {
            return in_array($entry->getACargoOS(), array(true));
        }
        );
        return $result->count();
    }

    /**
     * Set condicion
     *
     * @param Condicion $condicion
     * @return CuentaBancaria
     */
    public function setCondicion(Condicion $condicion = null) {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return Condicion 
     */
    public function getCondicion() {
        return $this->condicion;
    }

    /**
     * @return EmpleadoTipoContrato
     */
    public function getTipoContratacionActual() {
        $max = null;
        $tipoContratoActual = null;
        foreach ($this->tiposContrato as $tipoContrato) {
            /* @var $tipoContrato EmpleadoTipoContrato */
            if (!$tipoContrato->getFechaBaja()) {
                if (!$max) {
                    $max = $tipoContrato->getFechaDesde();
                    $tipoContratoActual = $tipoContrato;
                } else {
                    if ($tipoContrato->getFechaDesde() >= $max) {
                        $max = $tipoContrato->getFechaDesde();
                        $tipoContratoActual = $tipoContrato;
                    }
                }
            }
        }
        return $tipoContratoActual;
    }

    /**
     * @return EmpleadoObraSocial
     */
    public function getObraSocialActual() {
        $max = null;
        $obraSocialActual = null;
        foreach ($this->obrasSociales as $obraSocial) {
            /* @var $obraSocial EmpleadoObraSocial */
            if (!$max) {
                $max = $obraSocial->getFechaDesde();
                $obraSocialActual = $obraSocial;
            } else {
                if ($obraSocial->getFechaDesde() >= $max) {
                    $max = $obraSocial->getFechaDesde();
                    $obraSocialActual = $obraSocial;
                }
            }
        }
        return $obraSocialActual;
    }

    /**
     * Get licencias del mes
     *
     * @param DateTime $fechaLiquidacion
     * @return Empleado
     */
    public function getLicenciasFechas($fechaInicio, $fechaFin) {
        $result = $this->tiposLicencia->filter(
                function($tipoLicencia) use ($fechaInicio, $fechaFin) {
            return ($tipoLicencia->getFechaDesde() < $fechaInicio && $tipoLicencia->getFechaHasta() > $fechaInicio) || ($fechaInicio <= $tipoLicencia->getFechaDesde() && $tipoLicencia->getFechaDesde() <= $fechaFin);
        }
        );

        return $result;
    }

    /**
     * Add tiposContrato
     *
     * @param EmpleadoTipoContrato $tiposContrato
     * @return Empleado
     */
    public function addTiposContrato(EmpleadoTipoContrato $tiposContrato) {
        $this->tiposContrato[] = $tiposContrato;
        $tiposContrato->setEmpleado($this);
        return $this;
    }

    /**
     * Remove tiposContrato
     *
     * @param EmpleadoTipoContrato $tiposContrato
     */
    public function removeTiposContrato(EmpleadoTipoContrato $tiposContrato) {
        $this->tiposContrato->removeElement($tiposContrato);
        $tiposContrato->setEmpleado(null);
    }

    /**
     * Get tiposContrato
     *
     * @return Collection 
     */
    public function getTiposContrato() {
        return $this->tiposContrato;
    }

    /**
     * Set tiposContrato
     *
     * @return Empleado 
     */
    public function setTiposContrato(ArrayCollection $tiposContrato) {
        $this->tiposContrato = $tiposContrato;
        return $this;
    }

    /**
     * Add tiposLicencia
     *
     * @param EmpleadoTipoLicencia $tiposLicencia
     * @return Empleado
     */
    public function addTiposLicencium(EmpleadoTipoLicencia $tiposLicencia) {
        $this->tiposLicencia[] = $tiposLicencia;
        $tiposLicencia->setEmpleado($this);
        return $this;
    }

    /**
     * Remove tiposLicencia
     *
     * @param EmpleadoTipoLicencia $tiposLicencia
     */
    public function removeTiposLicencium(EmpleadoTipoLicencia $tiposLicencia) {
        $this->tiposLicencia->removeElement($tiposLicencia);
        $tiposLicencia->setEmpleado(null);
    }

    /**
     * Get tiposLicencia
     *
     * @return Collection 
     */
    public function getTiposLicencia() {
        return $this->tiposLicencia;
    }

    /**
     * Set tiposLicencia
     *
     * @return Empleado 
     */
    public function setTiposLicencia(ArrayCollection $tiposLicencia) {
        $this->tiposLicencia = $tiposLicencia;
        return $this;
    }

    /**
     * Add obrasSociales
     *
     * @param EmpleadoObraSocial $obrasSocial
     * @return Empleado
     */
    public function addObrasSociale(EmpleadoObraSocial $obrasSocial) {
        $this->obrasSociales[] = $obrasSocial;
        $obrasSocial->setEmpleado($this);
        return $this;
    }

    /**
     * Remove obrasSociales
     *
     * @param EmpleadoObraSocial $obrasSocial
     */
    public function removeObrasSociale(EmpleadoObraSocial $obrasSocial) {
        $this->obrasSociales->removeElement($obrasSocial);
        $obrasSocial->setEmpleado(null);
    }

    /**
     * Get obrasSociales
     *
     * @return Collection 
     */
    public function getObrasSociales() {
        return $this->obrasSociales;
    }

    /**
     * Add puestosHistoricos
     *
     * @param EmpleadoSectorHistorico $puestosHistoricos
     * @return Empleado
     */
    public function addPuestosHistorico(EmpleadoSectorHistorico $puestosHistoricos) {
        $this->puestosHistoricos[] = $puestosHistoricos;

        return $this;
    }

    /**
     * Remove puestosHistoricos
     *
     * @param EmpleadoSectorHistorico $puestosHistoricos
     */
    public function removePuestosHistorico(EmpleadoSectorHistorico $puestosHistoricos) {
        $this->puestosHistoricos->removeElement($puestosHistoricos);
    }

    /**
     * Get puestosHistoricos
     *
     * @return Collection 
     */
    public function getPuestosHistoricos() {
        return $this->puestosHistoricos;
    }

    /**
     * Add subcategoriasHistoricas
     *
     * @param EmpleadoSubcategoriaHistorico $subcategoriasHistoricas
     * @return Empleado
     */
    public function addSubcategoriasHistorica(EmpleadoSubcategoriaHistorico $subcategoriasHistoricas) {
        $this->subcategoriasHistoricas[] = $subcategoriasHistoricas;

        return $this;
    }

    /**
     * Remove subcategoriasHistoricas
     *
     * @param EmpleadoSubcategoriaHistorico $subcategoriasHistoricas
     */
    public function removeSubcategoriasHistorica(EmpleadoSubcategoriaHistorico $subcategoriasHistoricas) {
        $this->subcategoriasHistoricas->removeElement($subcategoriasHistoricas);
    }

    /**
     * Get subcategoriasHistoricas
     *
     * @return Collection 
     */
    public function getSubcategoriasHistoricas() {
        return $this->subcategoriasHistoricas;
    }

    public function getGerencia() {
        return $this->getIdGerencia();
    }

    public function getSubgerencia() {
        return $this->getIdSubgerencia();
    }

    public function getArea() {
        return $this->getIdArea();
    }

    public function getSector() {
        return $this->getIdSector();
    }

    /**
     * Get fechaIngreso
     *
     * @return DateTime 
     */
    public function getFechaIngreso() 
    {
        if ($this->nroLegajo == 2067) {
            // Fix para que este legajo unicamente a pedido de rrhh - 19/06/2018 - jira #492
            return $this->fechaInicioAntiguedad;
        }
        
        $maxFechaContrato = null;
        foreach ($this->tiposContrato as $tipoContrato) {
            /* @var $tipoContrato EmpleadoTipoContrato */
            if (!$maxFechaContrato) {
                $maxFechaContrato = $tipoContrato->getFechaDesde();
            } else {
                if ($tipoContrato->getFechaDesde() >= $maxFechaContrato) {
                    $maxFechaContrato = $tipoContrato->getFechaDesde();
                }
            }
        }
        return $maxFechaContrato;
    }

    /**
     * Set fechaEgreso
     *
     * @param DateTime $fechaEgreso
     * @return Empleado
     */
    public function setFechaEgreso($fechaEgreso) {
        $this->fechaEgreso = $fechaEgreso;

        return $this;
    }

    /**
     * Get fechaEgreso
     *
     * @return DateTime 
     */
    public function getFechaEgreso() {
        return $this->fechaEgreso;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Empleado
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * Set motivoEgreso
     *
     * @param MotivoEgreso $motivoEgreso
     * @return Empleado
     */
    public function setMotivoEgreso(MotivoEgreso $motivoEgreso = null) {
        $this->motivoEgreso = $motivoEgreso;

        return $this;
    }

    /**
     * Get motivoEgreso
     *
     * @return MotivoEgreso
     */
    public function getMotivoEgreso() {
        return $this->motivoEgreso;
    }

    /**
     * Retorna la cantidad de días trabajados en el mes actual
     * Tiene en cuenta la fecha de ingreso y egreso del empleado
     * @return integer
     */
    public function getDiasTrabajados($fechaCierreNovedades) {
        $fechaIngreso = $this->getFechaIngreso();

        if ($fechaIngreso !== null && ($fechaIngreso->format('Y-m') == $fechaCierreNovedades->format('Y-m'))) {
            $ingreso = $fechaIngreso->format('d');
        } else if ($fechaIngreso > new DateTime(date($fechaCierreNovedades->format('Y-m-t')))) {
//          FECHA INGRESO MAYOR A MES ACTUAL
            return 0;
        } else {
            $ingreso = 1;
        }

        if ($this->fechaEgreso !== null && ($this->fechaEgreso->format('Y-m') == $fechaCierreNovedades->format('Y-m'))) {
            // FECHA EGRESO DENTRO DE RANGO
            $egreso = $this->fechaEgreso->format('d') + 1;
        } else if ($this->fechaEgreso !== null && ($this->fechaEgreso < new DateTime(date($fechaCierreNovedades->format('Y-m') . '-01')))) {
            // FECHA EGRESO MENOR A MES ACTUAL
            return 0;
        } else {
            $egreso = $fechaCierreNovedades->format('t') + 1;
        }

        return ($egreso - $ingreso);
    }

    /**
     * Retorna la cantidad de días de licencia en el mes actual
     * Tiene en cuenta la fecha de ingreso y egreso del empleado
     * @return integer
     */
    public function getDiasLicencia($fechaCierreNovedades, $licencias) {
        $fechaIngreso = $this->getFechaIngreso();

        $diasLicencia = 0;

        if ($fechaIngreso !== null && ($fechaIngreso->format('Y-m') == $fechaCierreNovedades->format('Y-m'))) {
            $ingreso = $fechaIngreso->format('d');
        } else if ($fechaIngreso > new DateTime(date($fechaCierreNovedades->format('Y-m-t')))) {
//          FECHA INGRESO MAYOR A MES ACTUAL
            return 0;
        } else {
            $ingreso = 1;
        }

        if ($this->fechaEgreso !== null && ($this->fechaEgreso->format('Y-m') == $fechaCierreNovedades->format('Y-m'))) {
            // FECHA EGRESO DENTRO DE RANGO
            $egreso = $this->fechaEgreso->format('d') + 1;
        } else if ($this->fechaEgreso !== null && ($this->fechaEgreso < new DateTime(date($fechaCierreNovedades->format('Y-m') . '-01')))) {
            // FECHA EGRESO MENOR A MES ACTUAL
            return 0;
        } else {
            $egreso = $fechaCierreNovedades->format('t') + 1;
        }

        $situacion1 = array(
            'codigo' => substr($licencias, 0, 2),
            'inicio' => substr($licencias, 2, 2)
        );
        $situacion2 = array(
            'codigo' => substr($licencias, 4, 2),
            'inicio' => substr($licencias, 6, 2)
        );
        $situacion3 = array(
            'codigo' => substr($licencias, 8, 2),
            'inicio' => substr($licencias, 10, 2)
        );

        $codigos_licencias = array('05', '10', '13', '14');

        if ($situacion1['codigo'] != '00') {
            if (in_array($situacion1['codigo'], $codigos_licencias)) {
                // La licencia 1 es de los tipos que descuentan
                if ($situacion2['codigo'] != '00') {
                    $diasLicencia += min($situacion2['inicio'], $egreso) - max($situacion1['inicio'], $ingreso);
                } else {
                    $diasLicencia += $egreso - max($situacion1['inicio'], $ingreso);
                }
            }

            if (in_array($situacion2['codigo'], $codigos_licencias)) {
                // La licencia 2 es de los tipos que descuentan
                if ($situacion3['codigo'] != '00') {
                    $diasLicencia += min($situacion3['inicio'], $egreso) - max($situacion2['inicio'], $ingreso);
                } else {
                    $diasLicencia += $egreso - max($situacion2['inicio'], $ingreso);
                }
            }

            if (in_array($situacion3['codigo'], $codigos_licencias)) {
                $diasLicencia += $egreso - max($situacion3['inicio'], $ingreso);
            }
        }

        return $diasLicencia;
    }

    /**
     * Retorna la cantidad de días trabajados en el semestre
     * Tiene en cuenta la fecha de ingreso y egreso del empleado
     * @return integer
     */
    public function getDiasTrabajadosSemestre($fechaInicio, $fechaFin) {
		
        $fin = $fechaFin;
        if ($this->getFechaIngreso() < $fechaInicio) {
            // Ingresó antes del inicio del semestre
            $f_i = clone($fechaInicio);
            $inicio = $fechaInicio;
        } else {
            if ($this->getFechaIngreso() < $fechaFin) {
                // Ingresó ese semestre
                $f_i = clone($this->getFechaIngreso());
            } else {
                return 0;
            }
        }
        $inicio = $f_i->sub(new DateInterval('P1D'));

        if ($this->getFechaEgreso() && $this->getFechaEgreso() < $fechaInicio) {
            return 0;
        } else {
            if ($this->getFechaEgreso() && $this->getFechaEgreso() < $fechaFin) {
                // Egresó este mes
                $fin = $this->getFechaEgreso();
            }
        }
        
        return $fin->diff($inicio)->days;
    }

    /**
     * @return EmpleadoObraSocial
     */
    public function getObraSocialFecha($fecha) {
        $obraSocialActual = null;
        foreach ($this->obrasSociales as $obraSocial) {
            /* @var $obraSocial EmpleadoObraSocial */
            if ($obraSocial->getFechaDesde() <= $fecha && (!$obraSocial->getFechaHasta() || $obraSocial->getFechaHasta() >= $fecha)) {
                $obraSocialActual = $obraSocial;
            }
        }
        return $obraSocialActual;
    }

    /**
     * Set aplicaEscalaDiciembre
     *
     * @param boolean $aplicaEscalaDiciembre
     * @return Empleado
     */
    public function setAplicaEscalaDiciembre($aplicaEscalaDiciembre) {
        $this->aplicaEscalaDiciembre = $aplicaEscalaDiciembre;

        return $this;
    }

    /**
     * Get aplicaEscalaDiciembre
     *
     * @return boolean 
     */
    public function getAplicaEscalaDiciembre() {
        return $this->aplicaEscalaDiciembre;
    }

    /**
     * Add formularios572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Formulario572 $formularios572
     * @return Empleado
     */
    public function addFormularios572(\ADIF\RecursosHumanosBundle\Entity\Formulario572 $formularios572) {
        $this->formularios572[] = $formularios572;

        return $this;
    }

    /**
     * Remove formularios572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Formulario572 $formularios572
     */
    public function removeFormularios572(\ADIF\RecursosHumanosBundle\Entity\Formulario572 $formularios572) {
        $this->formularios572->removeElement($formularios572);
    }

    /**
     * Get formularios572
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormularios572() {
        return $this->formularios572;
    }

    /**
     * Get formulario572
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormulario572($anio = null) {
        $formulario572Actual = null;
        $anioActual = date("Y");
        if (($anio != null) && ($anio <= $anioActual)) {
            foreach ($this->formularios572 as $formulario572) {
                /* @var $formulario572 Formulario572 */
                if ($formulario572->getAnio() == $anio) {
                    $formulario572Actual = $formulario572;
                }
            }
        } else {
            $maximo = 0;
            foreach ($this->formularios572 as $formulario572) {
                /* @var $formulario572 Formulario572 */
                if ($formulario572->getAnio() >= $maximo) {
                    $formulario572Actual = $formulario572;
                    $maximo = $formulario572->getAnio();
                }
            }
            if ($maximo < $anioActual) {
                $formulario572Actual = null;
            }
        }

        return $formulario572Actual;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial() {
        return $this->getPersona()->getNombreCompleto();
    }

    /**
     * Get tipoDocumento
     * 
     * @return type
     */
    public function getTipoDocumento() {
        return 'CUIT';
    }

    /**
     * Get nroDocumento
     * 
     * @return type
     */
    public function getNroDocumento() {
        return $this->getPersona()->getCuil();
    }

    /**
     * Get domicilio
     * 
     * @return type
     */
    public function getDomicilio() {
        return $this->getPersona()->getIdDomicilio();
    }

    /**
     * Get localidad
     * 
     * @return type
     */
    public function getLocalidad() {
        return $this->getPersona()->getIdDomicilio()->getLocalidad();
    }
    
    /**
     * Set rangoRemuneracion
     *
     * @param RangoRemuneracion $rangoRemuneracion
     * @return Empleado
     */
    public function setRangoRemuneracionHistorica(EmpleadoHistoricoRangoRemuneracion $rangoRemuneracionHistorica = null) {
        $this->rangoRemuneracionHistorica = $rangoRemuneracionHistorica;
        
        return $this;
    }

    /**
     * Get rangoRemuneracion
     *
     * @return RangoRemuneracion 
     */
    public function getRangoRemuneracionHistorica() {
        return $this->rangoRemuneracionHistorica;
    }
	
	public function setPuesto($puesto)
	{
		$this->puesto = $puesto;
		
		return $this;
	}
	
	public function getPuesto()
	{
		return $this->puesto;
	}
	
	public function setSuperior($superior)
	{
		$this->superior = $superior;
		
		return $this;
	}
	
	public function getSuperior()
	{
		return $this->superior;
	}
	
	public function setNivelOrganizacional($nivelOrganizacional)
	{
		$this->nivelOrganizacional = $nivelOrganizacional;
		
		return $this;
	}
	
	public function getNivelOrganizacional()
	{
		return $this->nivelOrganizacional;
	}
    
    /**
     * Set liquidacionEmpleados
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return Liquidacion
     */
    public function setLiquidacionEmpleados(ArrayCollection $liquidacionEmpleados) 
    {
        $this->liquidacionEmpleados = $liquidacionEmpleados;

        return $this;
    }

    /**
     * Get liquidacionEmpleados
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getLiquidacionEmpleados() 
    {
        return $this->liquidacionEmpleados;
    }
    
    public function tieneLiquidacionCerrada()
    {
        return (!$this->liquidacionEmpleados->isEmpty());
    }
	
}
