<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Padron
 *
 * @ORM\Table(name="padron")
 * @ORM\Entity
 */
class Padron extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="tipo_impuesto", type="string", length=50)
     */
    private $tipoImpuesto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periodo", type="date")
     */
    private $periodo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_estado_padron", type="integer")
     */
    private $idEstadoPadron;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoPadron
     *
     * @ORM\ManyToOne(targetEntity="EstadoPadron")
     * @ORM\JoinColumn(name="id_estado_padron", referencedColumnName="id", nullable=true)
     * 
     */
    protected $estadoPadron;

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
     * Set tipoImpuesto
     *
     * @param integer $tipoImpuesto
     * @return Padron
     */

    public function setTipoImpuesto($tipoImpuesto)
    {
        $this->tipoImpuesto = $tipoImpuesto;

        return $this;
    }

    /**
     * Get tipoImpuesto
     *
     * @return integer 
     */
    public function getTipoImpuesto()
    {
        return $this->tipoImpuesto;
    }

    /**
     * Set periodo
     *
     * @param \DateTime $periodo
     * @return Padron
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return \DateTime 
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Set idEstadoPadron
     *
     * @param integer $idEstadoPadron
     * @return Padron
     */
    public function setIdEstadoPadron($idEstadoPadron)
    {
        $this->idEstadoPadron = $idEstadoPadron;

        return $this;
    }

    /**
     * Get idEstadoPadron
     *
     * @return integer 
     */
    public function getIdEstadoPadron()
    {
        return $this->idEstadoPadron;
    }

    /**
     * Set estadoPadron
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoPadron $estadoPadron
     * @return Padron
     */
    public function setEstadoPadron(\ADIF\ComprasBundle\Entity\EstadoPadron $estadoPadron) {
        $this->estadoPadron = $estadoPadron;

        return $this;
    }

    /**
     * Get estadoPadron
     *
     * @return \ADIF\ComprasBundle\Entity\estadoPadron
     */
    public function getEstadoPadron() {
        return $this->estadoPadron;
    }

}
