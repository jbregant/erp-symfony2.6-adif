<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * JustificacionPedidoInterno
 *
 * 
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="justificacion_pedido_interno")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class JustificacionPedidoInterno extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\PedidoInterno
     *
     * @ORM\OneToOne(targetEntity="PedidoInterno", inversedBy="justificacion")
     * @ORM\JoinColumn(name="id_pedido_interno", referencedColumnName="id")
     * 
     */
    protected $pedidoInterno;

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
     * @Vich\UploadableField(mapping="justificacion_pedido_interno", fileNameProperty="nombreArchivo")
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
     * Set pedidoInterno
     *
     * @param \ADIF\ComprasBundle\Entity\PedidoInterno $pedidoInterno
     * @return JustificacionPedidoInterno
     */
    public function setPedidoInterno(\ADIF\ComprasBundle\Entity\PedidoInterno $pedidoInterno = null) {
        $this->pedidoInterno = $pedidoInterno;

        return $this;
    }

    /**
     * Get pedidoInterno
     *
     * @return \ADIF\ComprasBundle\Entity\PedidoInterno 
     */
    public function getPedidoInterno() {
        return $this->pedidoInterno;
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

}
