<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RenglonDeclaracionJuradaLiquidacion
 *
 * @ORM\Table(name="renglon_declaracion_jurada_liquidacion")
 * @ORM\Entity
 */
class RenglonDeclaracionJuradaLiquidacion extends RenglonDeclaracionJurada {

    /**
     * @ORM\Column(name="id_liquidacion", type="integer", nullable=false)
     */
    protected $idLiquidacion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Liquidacion
     */
    protected $liquidacion;

    /**
     * Set idLiquidacion
     *
     * @param integer $idLiquidacion
     * @return RenglonDeclaracionJuradaLiquidacion
     */
    public function setIdLiquidacion($idLiquidacion) {
        $this->idLiquidacion = $idLiquidacion;

        return $this;
    }

    /**
     * Get idLiquidacion
     *
     * @return integer 
     */
    public function getIdLiquidacion() {
        return $this->idLiquidacion;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Liquidacion $liquidacion
     */
    public function setLiquidacion($liquidacion) {

        if (null != $liquidacion) {
            $this->idLiquidacion = $liquidacion->getId();
        } else {
            $this->idLiquidacion = null;
        }

        $this->liquidacion = $liquidacion;
    }

    /**
     * 
     * @return type
     */
    public function getLiquidacion() {
        return $this->liquidacion;
    }

    public function getRegimen() {
        return '';
    }

    public function getNombreBeneficiario() {

        setlocale(LC_ALL,"es_AR.UTF-8");

        $nombre_liquidacion = ucfirst(strftime("%B %Y", $this->getLiquidacion()->getFechaCierreNovedades()->getTimestamp()));

        return 'Liquidaci&oacute;n ' . $nombre_liquidacion;
    }

    public function getCUITBeneficiario() {

        return $this->getId();
    }
    
    public function getBeneficiario() {
        return new AdifDatos();
    }

}
