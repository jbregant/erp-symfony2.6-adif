<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Licitacion
 *
 * @author Manuel Becerra
 * created 25/03/2015
 * 
 * @ORM\Table(name="licitacion")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "licitacion" = "Licitacion",
 *      "licitacion_compra" = "LicitacionCompra",
 *      "licitacion_obra" = "LicitacionObra",
 *      "licitacion_chatarra" = "LicitacionChatarra"
 * })
 */
class Licitacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_tipo_contratacion", type="integer", nullable=true)
     */
    protected $idTipoContratacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoContratacion
     */
    protected $tipoContratacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El numero no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numero;

    /**
     * @var integer
     * @ORM\Column(name="anio", type="integer", nullable=true)
     */
    protected $anio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_apertura", type="datetime", nullable=false)
     */
    protected $fechaApertura;

    /**
     * @var double
     * @ORM\Column(name="importe_pliego", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $importePliego;

    /**
     * @var double
     * @ORM\Column(name="importe_licitacion", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $importeLicitacion;

    /**
     * @ORM\OneToMany(targetEntity="LicitacionArchivo", mappedBy="licitacion", cascade={"persist","remove"})
     */
    protected $archivos;

    /**
     * 
     */
    public function __construct() {

        $this->archivos = new ArrayCollection();
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getAliasCompleto();
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
     * Set idTipoContratacion
     *
     * @param integer $idTipoContratacion
     * @return Licitacion
     */
    public function setIdTipoContratacion($idTipoContratacion) {
        $this->idTipoContratacion = $idTipoContratacion;

        return $this;
    }

    /**
     * Get idTipoContratacion
     *
     * @return integer 
     */
    public function getIdTipoContratacion() {
        return $this->idTipoContratacion;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\TipoContratacion $tipoContratacion
     */
    public function setTipoContratacion($tipoContratacion) {

        if (null != $tipoContratacion) {
            $this->idTipoContratacion = $tipoContratacion->getId();
        } else {
            $this->idTipoContratacion = null;
        }

        $this->tipoContratacion = $tipoContratacion;
    }

    /**
     * 
     * @return type
     */
    public function getTipoContratacion() {
        return $this->tipoContratacion;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Licitacion
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set anio
     *
     * @param string $anio
     * @return Licitacion
     */
    public function setAnio($anio) {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string 
     */
    public function getAnio() {
        return $this->anio;
    }

    /**
     * Set fechaApertura
     *
     * @param \DateTime $fechaApertura
     * @return Licitacion
     */
    public function setFechaApertura($fechaApertura) {
        $this->fechaApertura = $fechaApertura;

        return $this;
    }

    /**
     * Get fechaApertura
     *
     * @return \DateTime 
     */
    public function getFechaApertura() {
        return $this->fechaApertura;
    }

    /**
     * Set importePliego
     *
     * @param string $importePliego
     * @return Licitacion
     */
    public function setImportePliego($importePliego) {
        $this->importePliego = $importePliego;

        return $this;
    }

    /**
     * Get importePliego
     *
     * @return string 
     */
    public function getImportePliego() {
        return $this->importePliego;
    }

    /**
     * Set importeLicitacion
     *
     * @param string $importeLicitacion
     * @return Licitacion
     */
    public function setImporteLicitacion($importeLicitacion) {
        $this->importeLicitacion = $importeLicitacion;

        return $this;
    }

    /**
     * Get importeLicitacion
     *
     * @return string 
     */
    public function getImporteLicitacion() {
        return $this->importeLicitacion;
    }

    /**
     * Add archivo
     *
     * @param LicitacionArchivo $archivo
     * @return Licitacion
     */
    public function addArchivo(LicitacionArchivo $archivo) {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove archivo
     *
     * @param LicitacionArchivo $archivo
     */
    public function removeArchivo(LicitacionArchivo $archivo) {
        $this->archivos->removeElement($archivo);
    }

    /**
     * Get archivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchivos() {
        return $this->archivos;
    }

    /**
     * 
     * @return string
     */
    public function getAliasCompleto() {

        $aliasCompleto = '';

        if ($this->idTipoContratacion != null) {
            $aliasCompleto .= $this->getTipoContratacion()->getAlias() . ' ';
        }

        return $aliasCompleto . str_pad($this->numero, 3, '0', STR_PAD_LEFT) . '/' . $this->anio;
    }

}
