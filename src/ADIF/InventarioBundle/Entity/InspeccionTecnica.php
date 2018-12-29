<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\PlanillaInspeccion;
use ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante;

/**
 * InspeccionTecnica
 *
 * @ORM\Table(name="inspeccion_tecnica")
 * @ORM\Entity
 */
class InspeccionTecnica extends BaseAuditoria implements BaseAuditable
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
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="PlanillaInspeccion")
     * @ORM\JoinColumn(name="id_planilla_inspeccion", referencedColumnName="id", nullable=false)
     */
    private $planillaInspeccion;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="ItemHojaRutaMaterialRodante")
     * @ORM\JoinColumn(name="id_item_hoja_ruta_material_rodante", referencedColumnName="id", nullable=false)
     */
    private $itemHojaRutaMaterialRodante;

    /**
     * @var string
     *
     * @ORM\Column(name="latitud", type="string", length=100)
     */
    private $latitud;

    /**
     * @var string
     *
     * @ORM\Column(name="longitud", type="string", length=100)
     */
    private $longitud;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=100)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="trocha_actual", type="string", length=100)
     */
    private $trochaActual;

    /**
     * @var string
     *
     * @ORM\Column(name="tara_teorica", type="decimal")
     */
    private $taraTeorica;

    /**
     * @var string
     *
     * @ORM\Column(name="tara_actual", type="decimal")
     */
    private $taraActual;

    /**
     * @var string
     *
     * @ORM\Column(name="capacidad_carga", type="decimal")
     */
    private $capacidadCarga;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_mecanica", type="string", length=100)
     */
    private $codigoMecanica;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


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
     * Set Id Planilla Inspeccion
     *
     * @param \ADIF\InventarioBundle\Entity\PlanillaInspeccion $planillaInspeccion
     * @return InspeccionTecnica
     */
    public function setPlanillaInspeccion(\ADIF\InventarioBundle\Entity\PlanillaInspeccion $id)
    {
        $this->planillaInspeccion = $id;
        return $this;
    }

    /**
     * Get Id Planilla Inspeccion
     *
     * @return \ADIF\InventarioBundle\Entity\PlanillaInspeccion
     */
    public function getPlanillaInspeccion()
    {
        return $this->planillaInspeccion;
    }

    /**
     * Set Id Item Hoja de Ruta Material Rodante
     *
     * @param \ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante $itemHojaRutaMaterialRodante
     * @return InspeccionTecnica
     */
    public function setItemHojaRutaMaterialRodante(\ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante $id)
    {
        $this->itemHojaRutaMaterialRodante = $id;
        return $this;
    }

    /**
     * Get Id Item Hoja de Ruta Material Rodanten
     *
     * @return \ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante
     */
    public function getItemHojaRutaMaterialRodante()
    {
        return $this->itemHojaRutaMaterialRodante;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     * @return InspeccionTecnica
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
     * @return InspeccionTecnica
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return InspeccionTecnica
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return InspeccionTecnica
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
     * Set trochaActual
     *
     * @param string $trochaActual
     * @return InspeccionTecnica
     */
    public function setTrochaActual($trochaActual)
    {
        $this->trochaActual = $trochaActual;

        return $this;
    }

    /**
     * Get trochaActual
     *
     * @return string
     */
    public function getTrochaActual()
    {
        return $this->trochaActual;
    }

    /**
     * Set taraTeorica
     *
     * @param string $taraTeorica
     * @return InspeccionTecnica
     */
    public function setTaraTeorica($taraTeorica)
    {
        $this->taraTeorica = $taraTeorica;

        return $this;
    }

    /**
     * Get taraTeorica
     *
     * @return string
     */
    public function getTaraTeorica()
    {
        return $this->taraTeorica;
    }

    /**
     * Set taraActual
     *
     * @param string $taraActual
     * @return InspeccionTecnica
     */
    public function setTaraActual($taraActual)
    {
        $this->taraActual = $taraActual;

        return $this;
    }

    /**
     * Get taraActual
     *
     * @return string
     */
    public function getTaraActual()
    {
        return $this->taraActual;
    }

    /**
     * Set capacidadCarga
     *
     * @param string $capacidadCarga
     * @return InspeccionTecnica
     */
    public function setCapacidadCarga($capacidadCarga)
    {
        $this->capacidadCarga = $capacidadCarga;

        return $this;
    }

    /**
     * Get capacidadCarga
     *
     * @return string
     */
    public function getCapacidadCarga()
    {
        return $this->capacidadCarga;
    }

    /**
     * Set codigoMecanica
     *
     * @param string $codigoMecanica
     * @return InspeccionTecnica
     */
    public function setCodigoMecanica($codigoMecanica)
    {
        $this->codigoMecanica = $codigoMecanica;

        return $this;
    }

    /**
     * Get codigoMecanica
     *
     * @return string
     */
    public function getCodigoMecanica()
    {
        return $this->codigoMecanica;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return InspeccionTecnica
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;
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
