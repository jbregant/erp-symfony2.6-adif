<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * TransporteCamion
 *
 * @ORM\Table(name="transporte_camion")
 * @ORM\Entity
 */
class TransporteCamion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="patente", type="string", length=100, nullable=false)
     */
    private $patente;

    /**
     * @var string
     *
     * @ORM\Column(name="patente_acoplado", type="string", length=100)
     */
    private $patenteAcoplado;

    /**
     * @var string
     *
     * @ORM\Column(name="transportista", type="string", length=100, nullable=false)
     */
    private $transportista;

    /**
     * @var string
     *
     * @ORM\Column(name="chofer", type="string", length=255)
     */
    private $chofer;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_remito", type="string", length=100, nullable=false)
     */
    private $numeroRemito;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
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
     * Set Patente
     *
     * @param string $patente
     * @return TransporteCamion
     */
    public function setPatente($patente)
    {
        $this->patente = $patente;

        return $this;
    }

    /**
     * Get Patente
     *
     * @return string
     */
    public function getPatente()
    {
        return $this->patente;
    }

    /**
     * Set Patente Acoplado
     *
     * @param string $patenteAcoplado
     * @return TransporteCamion
     */
    public function setPatenteAcoplado($patente)
    {
        $this->patente = $patente;
        return $this;
    }

    /**
     * Get Patente Acoplado
     *
     * @return string
     */
    public function getPatenteAcoplado()
    {
        return $this->patente;
    }

    /**
     * Set Transportista
     *
     * @param string $transportista
     * @return TransporteCamion
     */
    public function setTransportista($transportista)
    {
        $this->transportista = $transportista;
        return $this;
    }

    /**
     * Get Transportista
     *
     * @return string
     */
    public function getTransportista()
    {
        return $this->transportista;
    }

    /**s
     * Set Chofer
     *
     * @param string $chofer
     * @return TransporteCamion
     */
    public function setChofer($chofer)
    {
        $this->chofer = $chofer;
        return $this;
    }

    /**
     * Get Chofer
     *
     * @return string
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * Set Numero de Remito
     *
     * @param string $numeroRemito
     * @return TransporteCamion
     */
    public function setNumeroRemito($numeroRemito)
    {
        $this->numeroRemito = $numeroRemito;
        return $this;
    }

    /**
     * Get Numero de Remito
     *
     * @return string
     */
    public function getNumeroRemito()
    {
        return $this->numeroRemito;
    }

    /**
     * Set Empresa
     *
     * @param integer $idEmpresa
     * @return TransporteCamion
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;

        return $this;
    }

    /**
     * Get Empresa
     *
     * @return integer
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }
}
