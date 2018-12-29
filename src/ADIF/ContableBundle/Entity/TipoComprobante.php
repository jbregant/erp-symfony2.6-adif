<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoComprobante
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="tipo_comprobante")
 * @ORM\Entity 
 */
class TipoComprobante extends BaseAuditoria implements BaseAuditable {

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
     * 
     * @return type
     */
    public function __toString() {
        return $this->getNombre();
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
        if($this->id == Constantes\ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES){
            return 'Nota d&eacute;bito';
        }
        return $this->nombre;
    }
    
    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombreReal() {
        return $this->nombre;
    }

}
