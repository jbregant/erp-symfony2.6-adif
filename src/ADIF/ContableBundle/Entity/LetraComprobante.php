<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LetraComprobante
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="letra_comprobante")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\LetraComprobanteRepository")
 */
class LetraComprobante extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="letra", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La letra no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $letra;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set letra
     *
     * @param string $letra
     * @return LetraComprobante
     */
    public function setLetra($letra) {
        $this->letra = $letra;

        return $this;
    }

    /**
     * Get letra
     *
     * @return string 
     */
    public function getLetra() {
        return $this->letra;
    }

    public function __toString() {
        return $this->getLetra();
    }

}
