<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * JustificacionSolicitudCompra
 *
 * 
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="justificacion_solicitud_compra")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class JustificacionSolicitudCompra extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\SolicitudCompra
     *
     * @ORM\OneToOne(targetEntity="SolicitudCompra", inversedBy="justificacion")
     * @ORM\JoinColumn(name="id_solicitud_compra", referencedColumnName="id")
     * 
     */
    protected $solicitudCompra;

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
     *     mimeTypes={"application/pdf", "application/x-pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="justificacion_solicitud_compra", fileNameProperty="nombreArchivo")
     *
     * @var File $archivo`
     */
    protected $archivo;

    /**
     * @ORM\Column(name="nombre_archivo", type="string", length=255, nullable=true)
     *
     * @var string $nombrearchivo
     */
    protected $nombreArchivo;

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
     * @return JustificacionSolicitudCompra
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
     * @return JustificacionSolicitudCompra
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
     * Set solicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra
     * @return JustificacionSolicitudCompra
     */
    public function setSolicitudCompra(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra = null) {
        $this->solicitudCompra = $solicitudCompra;

        return $this;
    }

    /**
     * Get solicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\SolicitudCompra 
     */
    public function getSolicitudCompra() {
        return $this->solicitudCompra;
    }

}
