<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ComprobanteRetencionImpuestoObras
 *
 * @author Darío Rapetti
 * created 15/04/2015
 * 
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteRetencionImpuestoObrasRepository")
 */
class ComprobanteRetencionImpuestoObras extends ComprobanteRetencionImpuesto {

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return ComprobanteRetencionImpuestoObras
     */
    public function setIdProveedor($idProveedor) {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor() {
        return $this->idProveedor;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {
        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getPath() {
        return 'comprobanteRetencionImpuestoObras';
    }

    /**
     * 
     * @return string
     */
    public function getTipoComprobanteRetencion() {
        return 'OBRAS';
    }

}
