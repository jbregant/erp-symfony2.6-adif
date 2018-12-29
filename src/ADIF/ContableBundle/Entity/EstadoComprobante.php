<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoComprobante
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="estado_comprobante")
 * @ORM\Entity 
 */
class EstadoComprobante extends BaseAuditoria implements BaseAuditable {

    /**
     * __ESTADO_INGRESADO
     */
    const __ESTADO_INGRESADO = 1; // Generado

    /**
     * __ESTADO_CANCELADO
     */
    const __ESTADO_CANCELADO = 2; // Cobrado

    /**
     * __ESTADO_ANULADO
     */
    const __ESTADO_ANULADO = 3;

    /**
     * __ESTADO_CANCELADO_NC
     */
    const __ESTADO_CANCELADO_NC = 4;

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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El nombre no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $nombre;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return TipoComprobante
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

}
