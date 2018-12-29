<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ProveedorUTE
 *
 * @author Manuel Becerra
 * created 31/10/2014
 * 
 * @ORM\Table(name="proveedor_ute")
 * @ORM\Entity
 */
class ProveedorUTE extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="proveedoresUTE")
     * @ORM\JoinColumn(name="id_proveedor_ute", referencedColumnName="id", nullable=false)
     * 
     */
    protected $proveedorUTE;

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $proveedor;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_remuneracion", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El porcentaje de incidencia en remuneraciones debe ser de tipo numérico.")
     */
    protected $porcentajeRemuneracion;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_ganancia", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El porcentaje de incidencia en ganancia debe ser de tipo numérico.")
     */
    protected $porcentajeGanancia;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set proveedorUTE
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedorUTE
     * @return ProveedorUTE
     */
    public function setProveedorUTE(\ADIF\ComprasBundle\Entity\Proveedor $proveedorUTE) {
        $this->proveedorUTE = $proveedorUTE;

        return $this;
    }

    /**
     * Get proveedorUTE
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedorUTE() {
        return $this->proveedorUTE;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return ProveedorUTE
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set porcentajeRemuneracion
     *
     * @param float $porcentajeRemuneracion
     * @return ProveedorUTE
     */
    public function setPorcentajeRemuneracion($porcentajeRemuneracion) {
        $this->porcentajeRemuneracion = $porcentajeRemuneracion;

        return $this;
    }

    /**
     * Get porcentajeRemuneracion
     *
     * @return float 
     */
    public function getPorcentajeRemuneracion() {
        return $this->porcentajeRemuneracion;
    }

    /**
     * Set porcentajeGanancia
     *
     * @param float $porcentajeGanancia
     * @return ProveedorUTE
     */
    public function setPorcentajeGanancia($porcentajeGanancia) {
        $this->porcentajeGanancia = $porcentajeGanancia;

        return $this;
    }

    /**
     * Get porcentajeGanancia
     *
     * @return float 
     */
    public function getPorcentajeGanancia() {
        return $this->porcentajeGanancia;
    }

}
