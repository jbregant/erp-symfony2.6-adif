<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ArchivoEmpleado
 *
 * @ORM\Table(name="empleado_archivo", indexes={@ORM\Index(name="fk_empleado_archivo_empleado_1", columns={"id_empleado"})})
 * @ORM\Entity
 */
class EmpleadoArchivo extends BaseEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fechaAlta", type="date", nullable=true)
     */
    private $fechaAlta;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="archivos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * })
     */
    private $idEmpleado;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255, nullable=false)
     */
    private $archivo;

    /**
     *
     * @var type 
     */
    private $file;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return ArchivoEmpleado
     */
    public function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta() {
        return $this->fechaAlta;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Concepto
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Categoria
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
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return ArchivoEmpleado
     */
    public function setIdEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado = null) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Empleado
     */
    public function setArchivo($archivo) {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get archivo
     *
     * @return string
     */
    public function getArchivo() {
        return $this->archivo;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    public function getAbsolutePath() {
        return null === $this->archivo ? null : $this->getUploadRootDir() . '/' . $this->archivo;
    }

    public function getWebPath() {
        return null === $this->archivo ? null : $this->getUploadDir() . '/' . $this->archivo;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/empleados/archivos/' . $this->getIdEmpleado()->getId();
    }

    public function upload() {
        
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }
       
        $fs = new FileSystem();
        
        $fileExtension = $this->getFile()->guessExtension();
        $newFileName = str_replace('.'.$fileExtension, '', $this->getFile()->getClientOriginalName());
        $actualFileName = $newFileName;
        $i = 1;
        while ($fs->exists($this->getUploadRootDir().'/'.$actualFileName.'.'.$fileExtension)){
            $actualFileName = $newFileName.'_'.($i++);
        };
        
        // use the original file name here but you should
        // sanitize it at least to avoid any security issues
        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(), $actualFileName.'.'.$fileExtension
        );

        // set the path property to the filename where you've saved the file
        $this->archivo = $actualFileName.'.'.$fileExtension;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

}
