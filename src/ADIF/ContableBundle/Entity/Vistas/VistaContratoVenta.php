<?php

namespace ADIF\ContableBundle\Entity\Vistas;

use Doctrine\ORM\Mapping as ORM;

/**
 * VistaContratoVenta 
 * 
 * @ORM\Table(name="vistacontratoventa")
 * @ORM\Entity
 */
class VistaContratoVenta {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="claseContrato", type="string", nullable=true)
     */
    protected $claseContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoTipoMoneda", type="string", nullable=true)
     */
    protected $codigoTipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="simboloTipoMoneda", type="string", nullable=true)
     */
    protected $simboloTipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroContrato", type="string", nullable=true)
     */
    protected $numeroContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroCarpeta", type="string", nullable=true)
     */
    protected $numeroCarpeta;

    /**
     * @var string
     *
     * @ORM\Column(name="cliente", type="string", nullable=true)
     */
    protected $cliente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="datetime", nullable=true)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="datetime", nullable=true)
     */
    protected $fechaFin;

    /**
     * @var double
     * @ORM\Column(name="saldo", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $saldo;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoContrato", type="string", nullable=true)
     */
    protected $estadoContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="aliasTipoImportancia", type="string", nullable=true)
     */
    protected $aliasTipoImportancia;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoContratoCodigo", type="string", nullable=true)
     */
    protected $estadoContratoCodigo;

    /**
     * @var string
     *
     * @ORM\Column(name="esContratoAlquiler", type="string", nullable=true)
     */
    protected $esContratoAlquiler;

    /**
     * @var string
     *
     * @ORM\Column(name="esContratoVentaPlazo", type="string", nullable=true)
     */
    protected $esContratoVentaPlazo;

    /**
     * @var integer
     * 
     * @ORM\Column(name="idTipoMoneda", type="integer", nullable=true)
     */
    protected $idTipoMoneda;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDesocupacion", type="date", nullable=true)
     */
    protected $fechaDesocupacion; 
    
    /**
     * Set id
     *
     * @param integer $id
     * @return VistaContratoVenta
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set claseContrato
     *
     * @param string $claseContrato
     * @return VistaContratoVenta
     */
    public function setClaseContrato($claseContrato) {
        $this->claseContrato = $claseContrato;

        return $this;
    }

    /**
     * Get claseContrato
     *
     * @return string 
     */
    public function getClaseContrato() {
        return $this->claseContrato;
    }

    /**
     * Set codigoTipoMoneda
     *
     * @param string $codigoTipoMoneda
     * @return VistaContratoVenta
     */
    public function setCodigoTipoMoneda($codigoTipoMoneda) {
        $this->codigoTipoMoneda = $codigoTipoMoneda;

        return $this;
    }

    /**
     * Get codigoTipoMoneda
     *
     * @return string 
     */
    public function getCodigoTipoMoneda() {
        return $this->codigoTipoMoneda;
    }

    /**
     * Set simboloTipoMoneda
     *
     * @param string $simboloTipoMoneda
     * @return VistaContratoVenta
     */
    public function setSimboloTipoMoneda($simboloTipoMoneda) {
        $this->simboloTipoMoneda = $simboloTipoMoneda;

        return $this;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {
        return $this->simboloTipoMoneda;
    }

    /**
     * Set numeroContrato
     *
     * @param string $numeroContrato
     * @return VistaContratoVenta
     */
    public function setNumeroContrato($numeroContrato) {
        $this->numeroContrato = $numeroContrato;

        return $this;
    }

    /**
     * Get numeroContrato
     *
     * @return string 
     */
    public function getNumeroContrato() {
        return $this->numeroContrato;
    }

    /**
     * Set numeroCarpeta
     *
     * @param string $numeroCarpeta
     * @return VistaContratoVenta
     */
    public function setNumeroCarpeta($numeroCarpeta) {
        $this->numeroCarpeta = $numeroCarpeta;

        return $this;
    }

    /**
     * Get numeroCarpeta
     *
     * @return string 
     */
    public function getNumeroCarpeta() {
        return $this->numeroCarpeta;
    }

    /**
     * Set cliente
     *
     * @param string $cliente
     * @return VistaContratoVenta
     */
    public function setCliente($cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return string 
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return VistaContratoVenta
     */
    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return VistaContratoVenta
     */
    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin() {
        return $this->fechaFin;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     * @return VistaContratoVenta
     */
    public function setSaldo($saldo) {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string 
     */
    public function getSaldo() {
        return $this->saldo;
    }

    /**
     * Set estadoContrato
     *
     * @param string $estadoContrato
     * @return VistaContratoVenta
     */
    public function setEstadoContrato($estadoContrato) {
        $this->estadoContrato = $estadoContrato;

        return $this;
    }

    /**
     * Get estadoContrato
     *
     * @return string 
     */
    public function getEstadoContrato() {
        return $this->estadoContrato;
    }

    /**
     * Set aliasTipoImportancia
     *
     * @param string $aliasTipoImportancia
     * @return VistaContratoVenta
     */
    public function setAliasTipoImportancia($aliasTipoImportancia) {
        $this->aliasTipoImportancia = $aliasTipoImportancia;

        return $this;
    }

    /**
     * Get aliasTipoImportancia
     *
     * @return string 
     */
    public function getAliasTipoImportancia() {
        return $this->aliasTipoImportancia;
    }

    /**
     * Set estadoContratoCodigo
     *
     * @param string $estadoContratoCodigo
     * @return VistaContratoVenta
     */
    public function setEstadoContratoCodigo($estadoContratoCodigo) {
        $this->estadoContratoCodigo = $estadoContratoCodigo;

        return $this;
    }

    /**
     * Get estadoContratoCodigo
     *
     * @return string 
     */
    public function getEstadoContratoCodigo() {
        return $this->estadoContratoCodigo;
    }

    /**
     * Set esContratoAlquiler
     *
     * @param string $esContratoAlquiler
     * @return VistaContratoVenta
     */
    public function setEsContratoAlquiler($esContratoAlquiler) {
        $this->esContratoAlquiler = $esContratoAlquiler;

        return $this;
    }

    /**
     * Get esContratoAlquiler
     *
     * @return string 
     */
    public function getEsContratoAlquiler() {
        return $this->esContratoAlquiler;
    }

    /**
     * Set esContratoVentaPlazo
     *
     * @param string $esContratoVentaPlazo
     * @return VistaContratoVenta
     */
    public function setEsContratoVentaPlazo($esContratoVentaPlazo) {
        $this->esContratoVentaPlazo = $esContratoVentaPlazo;

        return $this;
    }

    /**
     * Get esContratoVentaPlazo
     *
     * @return string 
     */
    public function getEsContratoVentaPlazo() {
        return $this->esContratoVentaPlazo;
    }

    /**
     * Set idTipoMoneda
     *
     * @param integer $idTipoMoneda
     * @return VistaContratoVenta
     */
    public function setIdTipoMoneda($idTipoMoneda) {
        $this->idTipoMoneda = $idTipoMoneda;

        return $this;
    }

    /**
     * Get idTipoMoneda
     *
     * @return integer 
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }
    
    /**
     * Set fechaDesocupacion
     *
     * @param \DateTime $fechaDesocupacion
     * @return Contrato
     */
    public function setFechaDesocupacion($fechaDesocupacion) {
        $this->fechaDesocupacion = $fechaDesocupacion;

        return $this;
    }

    /**
     * Get fechaDesocupacion
     *
     * @return \DateTime 
     */
    public function getFechaDesocupacion() {
        return $this->fechaDesocupacion;
    }    

}
