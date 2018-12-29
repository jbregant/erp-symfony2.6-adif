<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\ComprasBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * CotizacionArchivo
 *
 * @author Manuel Becerra
 * 
 * @ORM\Table(name="cotizacion_archivo")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class CotizacionArchivo extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion", inversedBy="archivos", cascade={"persist"})
     * @ORM\JoinColumn(name="id_cotizacion", referencedColumnName="id")
     * 
     */
    protected $cotizacion;

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
     * @Vich\UploadableField(mapping="adjunto_cotizacion", fileNameProperty="nombreArchivo")
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
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set cotizacion
     *
     * @param Cotizacion $cotizacion
     * @return CotizacionArchivo
     */
    public function setCotizacion(Cotizacion $cotizacion = null) {
        $this->cotizacion = $cotizacion;

        return $this;
    }

    /**
     * Get cotizacion
     *
     * @return Cotizacion
     */
    public function getCotizacion() {
        return $this->cotizacion;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return CotizacionArchivo
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
     * @return CotizacionArchivo
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

}
