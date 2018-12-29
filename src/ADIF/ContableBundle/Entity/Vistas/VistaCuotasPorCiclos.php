<?php

namespace ADIF\ContableBundle\Entity\Vistas;

use Doctrine\ORM\Mapping as ORM;

/**
 * VistaCuotasPorCiclos 
 * 
 * @ORM\Table(name="vistacuotasporciclo")
 * @ORM\Entity
 */
class VistaCuotasPorCiclos {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     * 
     * @ORM\Column(name="numero_cuota", type="integer", nullable=true)
     */
    protected $numeroCuota;

    /**
     * @var integer
     * 
     * @ORM\Column(name="id_ciclo", type="integer", nullable=true)
     */
    protected $idCiclo;

    /**
     * Set id
     *
     * @param integer $id
     * @return VistaCuotasPorCiclos
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set numeroCuota
     *
     * @param integer $numeroCuota
     * @return VistaCuotasPorCiclos
     */
    public function setNumeroCuota($numeroCuota)
    {
        $this->numeroCuota = $numeroCuota;

        return $this;
    }

    /**
     * Get numeroCuota
     *
     * @return integer 
     */
    public function getNumeroCuota()
    {
        return $this->numeroCuota;
    }

    /**
     * Set idCiclo
     *
     * @param integer $idCiclo
     * @return VistaCuotasPorCiclos
     */
    public function setIdCiclo($idCiclo)
    {
        $this->idCiclo = $idCiclo;

        return $this;
    }

    /**
     * Get idCiclo
     *
     * @return integer 
     */
    public function getIdCiclo()
    {
        return $this->idCiclo;
    }
}
