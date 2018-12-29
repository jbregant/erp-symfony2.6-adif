<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Justificacion
 *
 * @ORM\Table(name="justificacion", indexes={@ORM\Index(name="fk_justificacion_tipo_justificacion_1", columns={"id_tipo_justificacion"})})
 * @ORM\Entity
 */
class Justificacion
{
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
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoJustificacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoJustificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_justificacion", referencedColumnName="id")
     * })
     */
    private $idTipoJustificacion;



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
     * Set observacion
     *
     * @param string $observacion
     * @return Justificacion
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
     * Set idTipoJustificacion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoJustificacion $idTipoJustificacion
     * @return Justificacion
     */
    public function setIdTipoJustificacion(\ADIF\RecursosHumanosBundle\Entity\TipoJustificacion $idTipoJustificacion = null)
    {
        $this->idTipoJustificacion = $idTipoJustificacion;

        return $this;
    }

    /**
     * Get idTipoJustificacion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoJustificacion 
     */
    public function getIdTipoJustificacion()
    {
        return $this->idTipoJustificacion;
    }
}
