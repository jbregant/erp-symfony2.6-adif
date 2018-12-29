<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\GrupoRodante;

/**
 * TipoRodante
 *
 * @ORM\Table(name="tipo_rodante")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\TipoRodanteRepository")
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class TipoRodante extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="GrupoRodante")
     * @ORM\JoinColumn(name="id_grupo_rodante", referencedColumnName="id", nullable=false)
     */
    private $grupoRodante;

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
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct() {
        $this->grupoRodante = new ArrayCollection();
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
     * Set grupoRodante
     *
     * @param integer $grupoRodante
     * @return TipoRodante
     */
    public function setGrupoRodante(GrupoRodante $grupoRodante = null)
    {
        $this->grupoRodante = $grupoRodante;

        return $this;
    }

    /**
     * Get grupoRodante
     *
     * @return integer
     */
    public function getGrupoRodante()
    {
        return $this->grupoRodante;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoRodante
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

    public function __toString() {
        return $this->getDenominacion();
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return TipoRodante
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
