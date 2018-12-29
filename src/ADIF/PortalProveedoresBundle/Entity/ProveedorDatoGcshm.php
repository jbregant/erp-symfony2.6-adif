<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorDatoGcshm
 *
 * @ORM\Table("proveedor_dato_gcshm")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ProveedorDatoGcshmRepository")
 */
class ProveedorDatoGcshm extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="proveedorDatoGcshm")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)     *  
     * @Assert\NotBlank()
     */
    private $usuario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="certificacion_iso9001", type="boolean")
     */
    private $certificacionIso9001;

    /**
     * @var boolean
     *
     * @ORM\Column(name="certificacion_iso14001", type="boolean")
     */
    private $certificacionIso14001;

    /**
     * @var boolean
     *
     * @ORM\Column(name="certificacion_osha18001", type="boolean")
     */
    private $certificacionOsha18001;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permisos_ambientales", type="boolean")
     */
    private $permisosAmbientales;

    /**
     * @var boolean
     *
     * @ORM\Column(name="documentacion_evaluacion", type="boolean")
     */
    private $documentacionEvaluacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="organigrama_institucional_obra", type="boolean")
     */
    private $organigramaInstitucionalObra;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pese", type="boolean")
     */
    private $pese;

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
     * @return ProveedorDatoGcshm
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usuario
     *
     * @param integer $usuario
     * @return ProveedorDatoGcshm
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set certificacionIso9001
     *
     * @param boolean $certificacionIso9001
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionIso9001($certificacionIso9001)
    {
        $this->certificacionIso9001 = $certificacionIso9001;

        return $this;
    }

    /**
     * Get certificacionIso9001
     *
     * @return boolean 
     */
    public function getCertificacionIso9001()
    {
        return $this->certificacionIso9001;
    }

    /**
     * Set certificacionIso14001
     *
     * @param boolean $certificacionIso14001
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionIso14001($certificacionIso14001)
    {
        $this->certificacionIso14001 = $certificacionIso14001;

        return $this;
    }

    /**
     * Get certificacionIso14001
     *
     * @return boolean 
     */
    public function getCertificacionIso14001()
    {
        return $this->certificacionIso14001;
    }

    /**
     * Set certificacionIso18001
     *
     * @param boolean $certificacionIso18001
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionOsha18001($certificacionOsha18001)
    {
        $this->certificacionOsha18001 = $certificacionOsha18001;

        return $this;
    }

    /**
     * Get certificacionIso18001
     *
     * @return boolean 
     */
    public function getCertificacionOsha18001()
    {
        return $this->certificacionOsha18001;
    }

    /**
     * Set permisosAmbientales
     *
     * @param boolean $permisosAmbientales
     * @return ProveedorDatoGcshm
     */
    public function setPermisosAmbientales($permisosAmbientales)
    {
        $this->permisosAmbientales = $permisosAmbientales;

        return $this;
    }

    /**
     * Get permisosAmbientales
     *
     * @return boolean 
     */
    public function getPermisosAmbientales()
    {
        return $this->permisosAmbientales;
    }

    /**
     * Set documentacionEvaluacion
     *
     * @param boolean $documentacionEvaluacion
     * @return ProveedorDatoGcshm
     */
    public function setDocumentacionEvaluacion($documentacionEvaluacion)
    {
        $this->documentacionEvaluacion = $documentacionEvaluacion;

        return $this;
    }

    /**
     * Get documentacionEvaluacion
     *
     * @return boolean 
     */
    public function getDocumentacionEvaluacion()
    {
        return $this->documentacionEvaluacion;
    }

    /**
     * Set organigramaInstitucionalObra
     *
     * @param boolean $organigramaInstitucionalObra
     * @return ProveedorDatoGcshm
     */
    public function setOrganigramaInstitucionalObra($organigramaInstitucionalObra)
    {
        $this->organigramaInstitucionalObra = $organigramaInstitucionalObra;

        return $this;
    }

    /**
     * Get organigramaInstitucionalObra
     *
     * @return boolean 
     */
    public function getOrganigramaInstitucionalObra()
    {
        return $this->organigramaInstitucionalObra;
    }

    /**
     * Set pese
     *
     * @param boolean $pese
     * @return ProveedorDatoGcshm
     */
    public function setPese($pese)
    {
        $this->pese = $pese;

        return $this;
    }

    /**
     * Get pese
     *
     * @return boolean 
     */
    public function getPese()
    {
        return $this->pese;
    }
}
