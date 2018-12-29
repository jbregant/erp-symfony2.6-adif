<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_2265B05D92FC23A8", columns={"username_canonical"}), 
 * @ORM\UniqueConstraint(name="UNIQ_2265B05DA0D96FBF", columns={"email_canonical"}), 
 * @ORM\UniqueConstraint(name="UNIQ_2265B05DC05FB297", columns={"confirmation_token"})}, indexes={
 * @ORM\Index(name="IDX_2265B05D916B4D1A", columns={"id_usuario_creacion"}), 
 * @ORM\Index(name="IDX_2265B05D6A3D61BD", columns={"id_usuario_ultima_modificacion"})})
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\UsuarioRepository")
 */
class Usuario
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=180, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="username_canonical", type="string", length=180, nullable=false)
     */
    private $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="email_canonical", type="string", length=180, nullable=false)
     */
    private $emailCanonical;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=180, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    private $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=false)
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_modificacion", type="datetime", nullable=false)
     */
    private $fechaModificacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_usuario_ultima_modificacion", type="integer", nullable=true)
     *
     */
    private $idUsuarioUltimaModificacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_usuario_creacion", type="integer", nullable=true)
     * 
     */
    private $idUsuarioCreacion;
    
    /**
     * @ORM\OneToMany(targetEntity="ProveedorDatoContacto", mappedBy="usuario")
     */
    private $proveedorDatoContacto;

    /**
     * Muchos usuarios tienen muchos Proveedor Dato Personal
     * @ManyToMany(targetEntity="ProveedorDatoPersonal", mappedBy="usuario")
     */
    protected $proveedorDatoPersonal;
   
    /**
     * @ORM\OneToMany(targetEntity="ProveedorActividad", mappedBy="usuario")
     */
    private $proveedorActividad;
    
    /**
     * @ORM\OneToMany(targetEntity="ProveedorDomicilio", mappedBy="usuario")
     */
    private $proveedorDomicilio;
    
    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoImpositivo", mappedBy="usuario")
     */
    private $proveedorDatoImpositivo;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRubro", mappedBy="usuario")
     */
    private $proveedorRubro;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorEvaluacion", mappedBy="usuario")
     */
    private $proveedorEvaluacion;
    
    /**
     * @ORM\OneToOne(targetEntity="ProveedorUte", mappedBy="usuario")
     */
    private $proveedorUte;
    
    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoBancario", mappedBy="usuario")
     */
    private $proveedorDatoBancario;    
    
    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoGcshm", mappedBy="usuario")
     */
    private $proveedorDatoGcshm;    
    
    /**
     * @ORM\OneToMany(targetEntity="ProveedorRepresentanteApoderado", mappedBy="usuario")
     */
    private $proveedorRepresentanteApoderado;
    
    /**
     * @ORM\OneToMany(targetEntity="ProveedorDocumentacion", mappedBy="usuario")
     */
    private $proveedorDocumentacion; 

    /**
     * @ORM\OneToMany(targetEntity="NotificacionUsuario", mappedBy="usuarioIdusuario")
     */
    private $notificacionUsuario;

    public function __construct()
    {
        parent::__construct();
        $this->proveedorDatoPersonal = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getUsernameCanonical() {
        return $this->usernameCanonical;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getEmailCanonical() {
        return $this->emailCanonical;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getLastLogin() {
        return $this->lastLogin;
    }

    public function getConfirmationToken() {
        return $this->confirmationToken;
    }

    public function getPasswordRequestedAt() {
        return $this->passwordRequestedAt;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getFechaBaja() {
        return $this->fechaBaja;
    }

    public function getFechaAlta() {
        return $this->fechaAlta;
    }

    public function getFechaModificacion() {
        return $this->fechaModificacion;
    }

    public function getIdUsuarioUltimaModificacion() {
        return $this->idUsuarioUltimaModificacion;
    }

    public function getIdUsuarioCreacion() {
        return $this->idUsuarioCreacion;
    }

    public function getProveedorDatoContacto() {
        return $this->proveedorDatoContacto;
    }

    public function getProveedorDatoPersonal() {
        return $this->proveedorDatoPersonal;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setUsernameCanonical($usernameCanonical) {
        $this->usernameCanonical = $usernameCanonical;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setEmailCanonical($emailCanonical) {
        $this->emailCanonical = $emailCanonical;
        return $this;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
        return $this;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function setLastLogin(\DateTime $lastLogin) {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function setConfirmationToken($confirmationToken) {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt) {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
        return $this;
    }

    public function setFechaBaja(\DateTime $fechaBaja) {
        $this->fechaBaja = $fechaBaja;
        return $this;
    }

    public function setFechaAlta(\DateTime $fechaAlta) {
        $this->fechaAlta = $fechaAlta;
        return $this;
    }

    public function setFechaModificacion(\DateTime $fechaModificacion) {
        $this->fechaModificacion = $fechaModificacion;
        return $this;
    }

    public function setIdUsuarioUltimaModificacion($idUsuarioUltimaModificacion) {
        $this->idUsuarioUltimaModificacion = $idUsuarioUltimaModificacion;
        return $this;
    }

    public function setIdUsuarioCreacion($idUsuarioCreacion) {
        $this->idUsuarioCreacion = $idUsuarioCreacion;
        return $this;
    }

    public function setProveedorDatoContacto($proveedorDatoContacto) {
        $this->proveedorDatoContacto = $proveedorDatoContacto;
        return $this;
    }

    public function setProveedorDatoPersonal($proveedorDatoPersonal) {
        $this->proveedorDatoPersonal = $proveedorDatoPersonal;
        return $this;
    }
    public function getDatoUsuario() {
        return $this->datoUsuario;
    }

    public function setDatoUsuario($datoUsuario) {
        $this->datoUsuario = $datoUsuario;
        return $this;
    }
    public function getProveedorActividad() {
        return $this->proveedorActividad;
    }

    public function setProveedorActividad($proveedorActividad) {
        $this->proveedorActividad = $proveedorActividad;
        return $this;
    }
    public function getProveedorDomicilio() {
        return $this->proveedorDomicilio;
    }

    public function setProveedorDomicilio($proveedorDomicilio) {
        $this->proveedorDomicilio = $proveedorDomicilio;
        return $this;
    }
    
    public function getProveedorDatoImpositivo()
    {
        return $this->proveedorDatoImpositivo;
    }

    public function setProveedorDatoImpositivo($proveedorDatoImpositivo)
    {
        $this->proveedorDatoImpositivo = $proveedorDatoImpositivo;
    }

    public function getProveedorRubro()
    {
        return $this->proveedorRubro;
    }

    public function setProveedorRubro($proveedorRubro)
    {
        $this->proveedorRubro = $proveedorRubro;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacion()
    {
        return $this->proveedorEvaluacion;
    }

    /**
     * @param mixed $proveedorEvaluacion
     *
     * @return self
     */
    public function setProveedorEvaluacion($proveedorEvaluacion)
    {
        $this->proveedorEvaluacion = $proveedorEvaluacion;

        return $this;
    }
        public function getProveedorUte()
    {
        return $this->proveedorUte;
    }

    public function setProveedorUte($proveedorUte)
    {
        $this->proveedorUte = $proveedorUte;
    }

    public function esUte(){
        return !$this->proveedorUte;
    }
    
    public function getProveedorDatoBancario()
    {
        return $this->proveedorDatoBancario;
    }

    public function setProveedorDatoBancario($proveedorDatoBancario)
    {
        $this->proveedorDatoBancario = $proveedorDatoBancario;
    }
    public function getProveedorDatoGcshm()
    {
        return $this->proveedorDatoGcshm;
    }

    public function setProveedorDatoGcshm($proveedorDatoGcshm)
    {
        $this->proveedorDatoGcshm = $proveedorDatoGcshm;
    }
    public function getProveedorRepresentanteApoderado()
    {
        return $this->proveedorRepresentanteApoderado;
    }

    public function setProveedorRepresentanteApoderado($proveedorRepresentanteApoderado)
    {
        $this->proveedorRepresentanteApoderado = $proveedorRepresentanteApoderado;
    }
    public function getProveedorDocumentacion()
    {
        return $this->proveedorDocumentacion;
    }

    public function setProveedorDocumentacion($proveedorDocumentacion)
    {
        $this->proveedorDocumentacion = $proveedorDocumentacion;
    }

    /**
     * @return mixed
     */
    public function getNotificacionUsuario()
    {
        return $this->notificacionUsuario;
    }

    /**
     * @param mixed $proveedorEvaluacion
     *
     * @return self
     */
    public function setNotificacionUsuario($notificacionUsuario)
    {
        $this->notificacionUsuario = $notificacionUsuario;
    }

    /**
     * @param mixed $proveedorDatoPersonal
     *
     * @return self
     */
    public function addProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $proveedorDatoPersonal->addIdUsuario($this);
        $this->proveedorDatoPersonal[] = $proveedorDatoPersonal;

        return $this;
    }
}
