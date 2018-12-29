<?php

namespace ADIF\InventarioBundle\Entity;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoTransporte
 *
 * @ORM\Table(name="tipo_transporte")
 * @ORM\Entity
 */
class TipoTransporte extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

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
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoTransporte
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return TipoTransporte
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
