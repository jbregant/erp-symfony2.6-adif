<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

use ADIF\InventarioBundle\Entity\Almacen;

/**
 * TransporteMaritimo
 *
 * @ORM\Table(name="transporte_maritimo")
 * @ORM\Entity
 */
class TransporteMaritimo extends BaseAuditoria implements BaseAuditable
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
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Almacen")
     * @ORM\JoinColumn(name="id_buque", referencedColumnName="id", nullable=false)
     */
    private $buque;

    /**
     * @var string
     *
     * @ORM\Column(name="guia_removido", type="string", length=100)
     */
    private $guiaRemovido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_embarcacion", type="string", length=100)
     */
    private $nombreEmbarcacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio_descarga", type="datetime")
     */
    private $fechaInicioDescarga;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin_descarga", type="datetime")
     */
    private $fechaFinDescarga;

    /**
     * @var string
     *
     * @ORM\Column(name="tonelada_bruta", type="decimal")
     */
    private $toneladaBruta;

    /**
     * @var string
     *
     * @ORM\Column(name="tonelada_neta", type="decimal")
     */
    private $toneladaNeta;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_promedio_bruto", type="decimal")
     */
    private $pesoPromedioBruto;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_promedio_neto", type="decimal")
     */
    private $pesoPromedioNeto;

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
     * Set Id Buque
     *
     * @param \ADIF\InventarioBundle\Entity\Almacen $buque
     * @return TransporteMaritimo
     */
    public function setBuque(\ADIF\InventarioBundle\Entity\Almacen $id)
    {
        $this->buque = $id;
        return $this;
    }

    /**
     * Get Id Buque
     *
     * @return \ADIF\InventarioBundle\Entity\Almacen
     */
    public function getBuque()
    {
        return $this->buque;
    }

    /**
     * Set Guia Removido
     *
     * @param string $guiaRemovido
     * @return TransporteMaritimo
     */
    public function setGuiaRemovido($guia)
    {
        $this->guiaRemovido = $guia;
        return $this;
    }

    /**
     * Get Guia Removido
     *
     * @return string
     */
    public function getGuiaRemovido()
    {
        return $this->guiaRemovido;
    }

    /**
     * Set Nombre Embarcacion
     *
     * @param string $nombreEmbarcacion
     * @return TransporteMaritimo
     */
    public function setNombreEmbarcacion($nombre)
    {
        $this->nombreEmbarcacion = $nombre;
        return $this;
    }

    /**
     * Get Nombre Embarcacion
     *
     * @return string
     */
    public function getNombreEmbarcacion()
    {
        return $this->nombreEmbarcacion;
    }

    /**
     * Set fechaInicioDescarga
     *
     * @param \DateTime $fechaInicioDescarga
     * @return TransporteMaritimo
     */
    public function setFechaInicioDescarga($fechaInicioDescarga)
    {
        $this->fechaInicioDescarga = $fechaInicioDescarga;

        return $this;
    }

    /**
     * Get fechaInicioDescarga
     *
     * @return \DateTime
     */
    public function getFechaInicioDescarga()
    {
        return $this->fechaInicioDescarga;
    }

    /**
     * Set fechaFinDescarga
     *
     * @param \DateTime $fechaFinDescarga
     * @return TransporteMaritimo
     */
    public function setFechaFinDescarga($fechaFinDescarga)
    {
        $this->fechaFinDescarga = $fechaFinDescarga;

        return $this;
    }

    /**
     * Get fechaFinDescarga
     *
     * @return \DateTime
     */
    public function getFechaFinDescarga()
    {
        return $this->fechaFinDescarga;
    }

    /**
     * Set toneladaBruta
     *
     * @param string $toneladaBruta
     * @return TransporteMaritimo
     */
    public function setToneladaBruta($toneladaBruta)
    {
        $this->toneladaBruta = $toneladaBruta;

        return $this;
    }

    /**
     * Get toneladaBruta
     *
     * @return string
     */
    public function getToneladaBruta()
    {
        return $this->toneladaBruta;
    }

    /**
     * Set toneladaNeta
     *
     * @param string $toneladaNeta
     * @return TransporteMaritimo
     */
    public function setToneladaNeta($toneladaNeta)
    {
        $this->toneladaNeta = $toneladaNeta;

        return $this;
    }

    /**
     * Get toneladaNeta
     *
     * @return string
     */
    public function getToneladaNeta()
    {
        return $this->toneladaNeta;
    }

    /**
     * Set pesoPromedioBruto
     *
     * @param string $pesoPromedioBruto
     * @return TransporteMaritimo
     */
    public function setPesoPromedioBruto($pesoPromedioBruto)
    {
        $this->pesoPromedioBruto = $pesoPromedioBruto;

        return $this;
    }

    /**
     * Get pesoPromedioBruto
     *
     * @return string
     */
    public function getPesoPromedioBruto()
    {
        return $this->pesoPromedioBruto;
    }

    /**
     * Set pesoPromedioNeto
     *
     * @param string $pesoPromedioNeto
     * @return TransporteMaritimo
     */
    public function setPesoPromedioNeto($pesoPromedioNeto)
    {
        $this->pesoPromedioNeto = $pesoPromedioNeto;

        return $this;
    }

    /**
     * Get pesoPromedioNeto
     *
     * @return string
     */
    public function getPesoPromedioNeto()
    {
        return $this->pesoPromedioNeto;
    }

    /**
     * Set Empresa
     *
     * @param integer $idEmpresa
     * @return TransporteMaritimo
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
