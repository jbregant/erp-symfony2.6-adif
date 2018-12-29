<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * ImportacionConciliacion
 *
 * @author Darío Rapetti
 * created 09/01/2015
 * 
 * @ORM\Table(name="conciliacion_bancaria_importacion_conciliacion")
 * @ORM\Entity
 */
class ImportacionConciliacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max = "255",
     *      maxMessage = "El nombre no puede superar los {{ limit }} caracteres.")
     */
    protected $nombre;

    /**
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"}
     * )
     * @Vich\UploadableField(mapping="archivo_importacion_conciliacion", fileNameProperty="nombreArchivo")
     *
     * @var File $archivo`
     */
    protected $archivo;

    /**
     * @ORM\Column(name="nombre_archivo", type="string", length=255, nullable=true)
     *
     * @var string $nombreArchivo
     */
    protected $nombreArchivo;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    protected $observacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_importacion", type="datetime", nullable=false)
     */
    protected $fechaImportacion;

    /**
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion", inversedBy="importacionesConciliacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_conciliacion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $conciliacion;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion", mappedBy="importacionConciliacion", cascade={"all"})
     */
    protected $renglonesConciliacion;
    
    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=15, scale=2, nullable=false, options={"default": 1})
     * 
     */    
    protected $tipoCambio;  

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaImportacion = new \DateTime();
        $this->renglonesConciliacion = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoCambio = 1;
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
     * Set nombre
     *
     * @param string $nombre
     * @return ImportacionConciliacion
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
    
    /**
     * Set archivo
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $archivo
     */
    public function setArchivo(File $archivo = null) {

        $this->archivo = $archivo;

        if ($archivo instanceof File) {
            $this->setFechaUltimaActualizacion(new \DateTime());
        }
    }

    /**
     * Get archivo
     * 
     */
    public function getArchivo() {
        return $this->archivo;
    }

    /**
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return ImportacionConciliacion
     */
    public function setNombreArchivo($nombreArchivo) {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    /**
     * Get nombreArchivo
     *
     * @return string 
     */
    public function getNombreArchivo() {
        return $this->nombreArchivo;
    }
    
    /**
     * Get nombreArchivo
     *
     * @return string 
     */
    public function getNombreArchivoClear() {
        return AdifApi::stringCleaner($this->nombreArchivo);
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ImportacionConciliacion
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

    /**
     * Set fechaImportacion
     *
     * @param \DateTime $fechaImportacion
     * @return ImportacionConciliacion
     */
    public function setFechaImportacion($fechaImportacion) {
        $this->fechaImportacion = $fechaImportacion;

        return $this;
    }

    /**
     * Get fechaImportacion
     *
     * @return \DateTime 
     */
    public function getFechaImportacion() {
        return $this->fechaImportacion;
    }

    /**
     * Set conciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion
     * @return ImportacionConciliacion
     */
    public function setConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion) {
        $this->conciliacion = $conciliacion;

        return $this;
    }

    /**
     * Get conciliacion
     *
     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion 
     */
    public function getConciliacion() {
        return $this->conciliacion;
    }

    /**
     * Add renglonesConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonesConciliacion
     * @return ImportacionConciliacion
     */
    public function addRenglonesConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonesConciliacion) {
        $this->renglonesConciliacion[] = $renglonesConciliacion;
        $renglonesConciliacion->setImportacionConciliacion($this);
        return $this;
    }

    /**
     * Remove renglonesConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonesConciliacion
     */
    public function removeRenglonesConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonesConciliacion) {
        $this->renglonesConciliacion->removeElement($renglonesConciliacion);
        $renglonesConciliacion->setImportacionConciliacion(null);
    }

    /**
     * Get renglonesConciliacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesConciliacion() {
        return $this->renglonesConciliacion;
    }
    
    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return Conciliacion
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    } 

}
