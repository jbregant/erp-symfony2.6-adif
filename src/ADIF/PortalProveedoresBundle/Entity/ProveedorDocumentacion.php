<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorDocumentacion
 *
 * @ORM\Table(name="proveedor_documentacion")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorDocumentacionRepository")
 */
class ProveedorDocumentacion extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var ADIF\UsuarioBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorDocumentacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * })
     */
    private $usuario;

    /**
     * @var TipoDocumentacion
     *
     * @ORM\ManyToOne(targetEntity="TipoDocumentacion")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="id_tipo_documentacion", referencedColumnName="id")
     * })
     */
    private $idTipoDocumentacion;

    /**
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="documento", fileNameProperty="documentName")
     *
     * @var File
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="public_filename", type="string", length=255, nullable=true)
     */
    private $publicfilename;

    /**
     * @var ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorDocumentacion
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return string
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $document
     */
    public function setFile( $document = null)
    {
        $this->file = $document;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usuario
     *
     * @param $usuario
     *
     * @return ProveedorDocumentacion
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return ADIF\UsuarioBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set idTipoDocumentacion
     *
     * @param TipoDocumentacion $idTipoDocumentacion
     *
     * @return ProveedorDocumentacion
     */
    public function setIdTipoDocumentacion($idTipoDocumentacion)
    {
        $this->idTipoDocumentacion = $idTipoDocumentacion;

        return $this;
    }

    /**
     * Get idTipoDocumentacion
     *
     * @return TipoDocumentacion
     */
    public function getIdTipoDocumentacion()
    {
        return $this->idTipoDocumentacion;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return ProveedorDocumentacion
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/documentacion';
    }

    /**
     * @return string
     */
    public function getPublicfilename()
    {
        return $this->publicfilename;
    }

    /**
     * @param string $publicfilename
     *
     * @return ProveedorDocumentacion
     */
    public function setPublicfilename($publicfilename)
    {
        $this->publicfilename = $publicfilename;
        return $this;
    }
}