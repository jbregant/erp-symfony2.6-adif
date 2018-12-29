<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;



/**
 * ItemHojaRutaMaterialRodante
 *
 * @ORM\Table(name="item_hoja_ruta_rodante")
 * @ORM\Entity
 */
class ItemHojaRutaMaterialRodante extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="HojaRuta")
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
     * @ORM\ManyToOne(targetEntity="Estacion")
     * @ORM\JoinColumn(name="id_estacion", referencedColumnName="id", nullable=false)
     */
    private $estacion;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="GrupoRodante")
     * @ORM\JoinColumn(name="id_grupo_rodante", referencedColumnName="id", nullable=true)
     */
    private $grupoRodante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoRodante")
     * @ORM\JoinColumn(name="id_tipo_rodante", referencedColumnName="id", nullable=true)
     */
    private $tipoRodante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesRodantes", inversedBy="itemsHojaRutaMaterialRodante")
     * @ORM\JoinColumn(name="id_material_rodante", referencedColumnName="id", nullable=true)
     */
    private $materialRodante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="item_relevado", type="boolean", nullable=false)
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
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
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
     * Set idHojaRuta
     *
     * @param integer $idHojaRuta
     * @return ItemHojaRutaMaterialRodante
     */
    public function setHojaRuta($hojaRuta)
    {
        $this->hojaRuta = $hojaRuta;

        return $this;
    }

    /**
     * Get HojaRuta
     *
     * @return integer
     */
    public function getHojaRuta()
    {
        return $this->hojaRuta;
    }

    /**
     * Set Linea
     *
     * @param integer $idLinea
     * @return ItemHojaRutaMaterialRodante
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;

        return $this;
    }

    /**
     * Get Linea
     *
     * @return integer
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set Operador
     *
     * @param integer $idOperador
     * @return ItemHojaRutaMaterialRodante
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;

        return $this;
    }

    /**
     * Get Operador
     *
     * @return integer
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * Set Estacion
     *
     * @param integer $idEstacion
     * @return ItemHojaRutaMaterialRodante
     */
    public function setEstacion($estacion)
    {
        $this->estacion = $estacion;

        return $this;
    }

    /**
     * Get Estacion
     *
     * @return integer
     */
    public function getEstacion()
    {
        return $this->estacion;
    }

    /**
     * Set GrupoRodante
     *
     * @param integer $idGrupoRodante
     * @return ItemHojaRutaMaterialRodante
     */
    public function setGrupoRodante($grupoRodante)
    {
        $this->grupoRodante = $grupoRodante;

        return $this;
    }

    /**
     * Get GrupoRodante
     *
     * @return integer
     */
    public function getGrupoRodante()
    {
        return $this->grupoRodante;
    }

    /**
     * Set TipoRodante
     *
     * @param integer $idTipoRodante
     * @return ItemHojaRutaMaterialRodante
     */
    public function setTipoRodante($tipoRodante)
    {
        $this->tipoRodante = $tipoRodante;

        return $this;
    }

    /**
     * Get TipoRodante
     *
     * @return integer
     */
    public function getTipoRodante()
    {
        return $this->tipoRodante;
    }

    /**
     * Set MaterialRodante
     *
     * @param integer $idMaterialRodante
     * @return ItemHojaRutaMaterialRodante
     */
    public function setMaterialRodante($materialRodante)
    {
        $this->materialRodante = $materialRodante;

        return $this;
    }

    /**
     * Get MaterialRodante
     *
     * @return integer
     */
    public function getMaterialRodante()
    {
        return $this->materialRodante;
    }

    /**
     * Set itemRelevado
     *
     * @param boolean $itemRelevado
     * @return ItemHojaRutaMaterialRodante
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
     * @return ItemHojaRutaMaterialRodante
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
     * @return ItemHojaRutaMaterialRodante
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
