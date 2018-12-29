<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Entity\Divisiones;
use ADIF\InventarioBundle\Entity\TipoActivo;

/**
 * ItemHojaRutaActivoLineal
 *
 * @ORM\Table("item_hoja_ruta_activo_lineal")
 * @ORM\Entity
 */
class ItemHojaRutaActivoLineal extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="HojaRuta", inversedBy="itemsHojaRutaActivoLineal")
     * @ORM\JoinColumn(name="id_hoja_ruta", referencedColumnName="id", nullable=false)
     */
    private $hojaRuta;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $linea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Operador")
     * @ORM\JoinColumn(name="id_operador", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $operador;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Divisiones")
     * @ORM\JoinColumn(name="id_division", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $division;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoActivo")
     * @ORM\JoinColumn(name="id_tipo_activo", referencedColumnName="id", nullable=true)
     */
    private $tipoActivo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ActivoLineal", inversedBy="itemsHojaRutaActivoLineal")
     * @ORM\JoinColumn(name="id_activo_lineal", referencedColumnName="id", nullable=true)
     */
    private $activoLineal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="itemRelevado", type="boolean", nullable=true)
     */
    private $itemRelevado;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="idEmpresa", type="integer")
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
     * Set hojaRuta
     *
     * @param integer $hojaRuta
     * @return ItemHojaRutaActivoLineal
     */
    public function setHojaRuta($hojaRuta)
    {
        $this->hojaRuta = $hojaRuta;

        return $this;
    }

    /**
     * Get hojaRuta
     *
     * @return integer
     */
    public function getHojaRuta()
    {
        return $this->hojaRuta;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     * @return ItemHojaRutaActivoLineal
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
     * Set operador
     *
     * @param integer $operador
     * @return ItemHojaRutaActivoLineal
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
     * Set division
     *
     * @param integer $division
     * @return ItemHojaRutaActivoLineal
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
     * Set tipoActivo
     *
     * @param integer $tipoActivo
     * @return ItemHojaRutaActivoLineal
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
     * Set activoLineal
     *
     * @param integer $activoLineal
     * @return ItemHojaRutaActivoLineal
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
     * Set itemRelevado
     *
     * @param boolean $itemRelevado
     * @return ItemHojaRutaActivoLineal
     */
    public function setItemRelevado($itemRelevado)
    {
        $this->itemRelevado = $itemRelevado;

        return $this;
    }

    /**
     * Get itemRelevado
     *
     * @return boolean
     */
    public function getItemRelevado()
    {
        return $this->itemRelevado;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ItemHojaRutaActivoLineal
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return ItemHojaRutaActivoLineal
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
