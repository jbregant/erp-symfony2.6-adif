<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Indica el Tipo de ConceptoGanancia. 
 * 
 * Por Ejemplo: 
 *      > Deducciones Generales.
 *      > Resultado Neto.
 *      > Diferencia.
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_tipo_concepto_ganancia")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\TipoConceptoGananciaRepository")
 */
class TipoConceptoGanancia {

    const __PERCEPCIONES = '14';
    const __OTROS_INGRESOS = '13';
    
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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del tipo de concepto no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var integer
     * 
     * @ORM\Column(name="orden_aplicacion", type="integer", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El órden de aplicación debe ser de tipo numérico.")
     */
    protected $ordenAplicacion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoConceptoGanancia
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set ordenAplicacion
     *
     * @param integer $ordenAplicacion
     * @return TipoConceptoGanancia
     */
    public function setOrdenAplicacion($ordenAplicacion) {
        $this->ordenAplicacion = $ordenAplicacion;

        return $this;
    }

    /**
     * Get ordenAplicacion
     *
     * @return integer 
     */
    public function getOrdenAplicacion() {
        return $this->ordenAplicacion;
    }
	
	public function __toString()
	{
		return $this->denominacion;
	}

}
