<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * TransporteTren
 *
 * @ORM\Table(name="transporte_tren")
 * @ORM\Entity
 */
class TransporteTren extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_operativo", type="string", length=100)
     */
    private $numeroOperativo;

    /**
     * @var string
     *
     * @ORM\Column(name="carta_porte", type="string", length=100)
     */
    private $cartaPorte;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_vagon", type="string", length=100)
     */
    private $numeroVagon;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_toneladas", type="string", length=100)
     */
    private $pesoToneladas;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Numero Operativo
     *
     * @param string $numeroOperativo
     * @return TransporteTren
     */
    public function setNumeroOperativo($numeroOperativo)
    {
        $this->numeroOperativo = $numeroOperativo;
        return $this;
    }

    /**
     * Get Numero Operativo
     *
     * @return string
     */
    public function getNumeroOperativo()
    {
        return $this->numeroOperativo;
    }

    /**
     * Set Carta Porte
     *
     * @param string $cartaPorte
     * @return TransporteTren
     */
    public function setCartaPorte($cartaPorte)
    {
        $this->cartaPorte = $cartaPorte;
        return $this;
    }

    /**
     * Get Numero Operativo
     *
     * @return string
     */
    public function getCartaPorte()
    {
        return $this->cartaPorte;
    }

    /**
     * Set numeroVagon
     *
     * @param string $numeroVagon
     * @return TransporteTren
     */
    public function setNumeroVagon($numeroVagon)
    {
        $this->numeroVagon = $numeroVagon;

        return $this;
    }

    /**
     * Get numeroVagon
     *
     * @return string
     */
    public function getNumeroVagon()
    {
        return $this->numeroVagon;
    }

    /**
     * Set Peso Toneladas
     *
     * @param string $pesoToneladas
     * @return TransporteTren
     */
    public function setPesoToneladas($pesoToneladas)
    {
        $this->pesoToneladas = $pesoToneladas;
        return $this;
    }

    /**
     * Get Peso Toneladas
     *
     * @return string
     */
    public function getPesoToneladas()
    {
        return $this->pesoToneladas;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return TransporteTren
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;

        return $this;
    }

    /**
     * Get idEmpresa
     *
     * @return integer
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }
}
