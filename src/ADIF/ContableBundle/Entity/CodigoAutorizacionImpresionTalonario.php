<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ComprasBundle\Entity\CodigoAutorizacionImpresion;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CodigoAutorizacionImpresionTalonario
 *
 * @author Darío Rapetti
 * created 29/01/2015
 * 
 * @ORM\Table(name="codigo_autorizacion_impresion_talonario")
 * @ORM\Entity
 */
class CodigoAutorizacionImpresionTalonario extends CodigoAutorizacionImpresion {

    /**
     * @var Talonario
     *
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\Talonario", mappedBy="codigoAutorizacionImpresionTalonario")
     * 
     */
    protected $talonario;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set talonario
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\Talonario $talonario
     * @return CodigoAutorizacionImpresionTalonario
     */
    public function setTalonario(\ADIF\ContableBundle\Entity\Facturacion\Talonario $talonario = null) {
        $this->talonario = $talonario;

        return $this;
    }

    /**
     * Get talonario
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\Talonario 
     */
    public function getTalonario() {
        return $this->talonario;
    }

}
