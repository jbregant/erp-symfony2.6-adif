<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of OrdenCompraParametrizacion
 *
 * @author Manuel
 * created 05/11/2014
 * 
 * @ORM\Table(name="orden_compra_parametrizacion")
 * @ORM\Entity
 */
class OrdenCompraParametrizacion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="observacion", type="string", length=4048, nullable=false)
     * @Assert\Length(
     *      max="4048", 
     *      maxMessage="La observación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $observacion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return OrdenCompraParametrizacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

}
