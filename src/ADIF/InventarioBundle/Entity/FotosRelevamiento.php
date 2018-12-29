<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\Relevamiento;
use ADIF\InventarioBundle\Entity\InspeccionTecnica;

/**
 * FotosRelevamiento
 *
 * @ORM\Table(name="fotos_relevamiento")
 * @ORM\Entity
 */
class FotosRelevamiento extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="InspeccionTecnica")
     * @ORM\JoinColumn(name="id_inspeccion_tecnica", referencedColumnName="id")
     */
    private $inspeccionTecnica;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Relevamiento")
     * @ORM\JoinColumn(name="id_relevamiento", referencedColumnName="id")
     */
    private $relevamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="string", length=100)
     */
    private $foto;

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
     * Set Id Inspeccion Tecnica
     *
     * @param \ADIF\InventarioBundle\Entity\InspeccionTecnica $inspeccionTecnica
     * @return FotosRelevamiento
     */
    public function setInspeccionTecnica(\ADIF\InventarioBundle\Entity\InspeccionTecnica $id)
    {
        $this->inspeccionTecnica = $id;
        return $this;
    }

    /**
     * Get Id Inspeccion Tecnica
     *
     * @return \ADIF\InventarioBundle\Entity\InspeccionTecnica
     */
    public function getInspeccionTecnica()
    {
        return $this->inspeccionTecnica ;
    }

    /**
     * Set Id Relevamiento
     *
     * @param \ADIF\InventarioBundle\Entity\Relevamiento $relevamiento
     * @return FotosRelevamiento
     */
    public function setRelevamiento(\ADIF\InventarioBundle\Entity\Relevamiento $id)
    {
        $this->relevamiento = $id;
        return $this;
    }

    /**
     * Get Id Relevamiento
     *
     * @return \ADIF\InventarioBundle\Entity\Relevamiento
     */
    public function getRelevamiento()
    {
        return $this->relevamiento ;
    }

    /**
     * Set foto
     *
     * @param string $foto
     * @return FotosRelevamiento
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

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
     * @return FotosRelevamiento
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
