<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluacionAspectoProveedor
 *
 * @ORM\Table(name="evaluacion_aspecto_proveedor")
 * @ORM\Entity
 */
class EvaluacionAspectoProveedor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\EvaluacionProveedor
     *
     * @ORM\ManyToOne(targetEntity="EvaluacionProveedor", inversedBy="evaluacionesAspectos")
     * @ORM\JoinColumn(name="id_evaluacion_proveedor", referencedColumnName="id")
     * 
     */
    protected $evaluacionProveedor;

    /**
     * @var \ADIF\ComprasBundle\Entity\AspectoEvaluacion
     *
     * @ORM\ManyToOne(targetEntity="AspectoEvaluacion", inversedBy="evaluaciones")
     * @ORM\JoinColumn(name="id_aspecto_evaluacion", referencedColumnName="id")
     * 
     */
    protected $aspectoEvaluacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor_alcanzado", type="float", nullable=false)
     */
    protected $valorAlcanzado;

    /**
     * Constructor
     */
    public function __construct() {
        $this->valorAlcanzado = 0;
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
     * Set evaluacionProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\EvaluacionProveedor $evaluacionProveedor
     * @return EvaluacionAspectoProveedor
     */
    public function setEvaluacionProveedor(\ADIF\ComprasBundle\Entity\EvaluacionProveedor $evaluacionProveedor = null) {
        $this->evaluacionProveedor = $evaluacionProveedor;

        return $this;
    }

    /**
     * Get evaluacionProveedor
     *
     * @return \ADIF\ComprasBundle\Entity\EvaluacionProveedor 
     */
    public function getEvaluacionProveedor() {
        return $this->evaluacionProveedor;
    }

    /**
     * Set aspectoEvaluacion
     *
     * @param \ADIF\ComprasBundle\Entity\AspectoEvaluacion $aspectoEvaluacion
     * @return EvaluacionAspectoProveedor
     */
    public function setAspectoEvaluacion(\ADIF\ComprasBundle\Entity\AspectoEvaluacion $aspectoEvaluacion = null) {
        $this->aspectoEvaluacion = $aspectoEvaluacion;

        return $this;
    }

    /**
     * Get aspectoEvaluacion
     *
     * @return \ADIF\ComprasBundle\Entity\AspectoEvaluacion 
     */
    public function getAspectoEvaluacion() {
        return $this->aspectoEvaluacion;
    }

    /**
     * Set valorAlcanzado
     *
     * @param integer $valorAlcanzado
     * @return EvaluacionAspectoProveedor
     */
    public function setValorAlcanzado($valorAlcanzado) {
        $this->valorAlcanzado = $valorAlcanzado;

        return $this;
    }

    /**
     * Get valorAlcanzado
     *
     * @return integer 
     */
    public function getValorAlcanzado() {
        return $this->valorAlcanzado;
    }

}
