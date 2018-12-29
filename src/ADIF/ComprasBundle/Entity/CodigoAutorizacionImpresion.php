<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CodigoAutorizacionImpresion
 *
 * @author Manuel Becerra
 * created 21/10/2014
 * 
 * @ORM\Table(name="codigo_autorizacion_impresion")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "consultor" = "ADIF\RecursosHumanosBundle\Entity\Consultoria\CodigoAutorizacionImpresionConsultor",
 *      "proveedor" = "ADIF\ComprasBundle\Entity\CodigoAutorizacionImpresionProveedor",
 *      "talonario" = "ADIF\ContableBundle\Entity\CodigoAutorizacionImpresionTalonario"
 * })
 */
abstract class CodigoAutorizacionImpresion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \string
     *
     * @ORM\Column(name="numero", type="string", length=14, nullable=false)
     */
    protected $numero;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=false)
     */
    protected $fechaVencimiento;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return CodigoAutorizacionImpresion
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     * @return CodigoAutorizacionImpresion
     */
    public function setFechaVencimiento($fechaVencimiento) {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaVencimiento() {
        return $this->fechaVencimiento;
    }

}
