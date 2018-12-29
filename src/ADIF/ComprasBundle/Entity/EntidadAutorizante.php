<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EntidadAutorizante
 *
 * Indica la entidad autorizante. 
 * 
 * Por ejemplo:
 *      Departamento de Compras y Contrataciones.
 *      Sub Gerente.
 *      Gerente.
 *      Presidente.
 *      Directorio
 * 
 *
 * @author Manuel Becerra
 * created 14/07/2014
 * 
 * @ORM\Table(name="entidad_autorizante")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\EntidadAutorizanteRepository")
 * @UniqueEntity(
 *      fields = {"denominacionEntidadAutorizante", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class EntidadAutorizante extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEntidadAutorizante;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_desde", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoDesde;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_hasta", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoHasta;

    /**
     * @ORM\Column(name="id_grupo", type="integer", nullable=false)
     */
    protected $idGrupo;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Grupo
     */
    protected $grupo;

    /**
     * @ORM\OneToMany(targetEntity="SolicitudCompra", mappedBy="entidadAutorizante")
     */
    protected $solicitudes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->solicitudes = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEntidadAutorizante;
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
     * Set denominacionEntidadAutorizante
     *
     * @param string $denominacionEntidadAutorizante
     * @return EntidadAutorizante
     */
    public function setDenominacionEntidadAutorizante($denominacionEntidadAutorizante) {
        $this->denominacionEntidadAutorizante = $denominacionEntidadAutorizante;

        return $this;
    }

    /**
     * Get denominacionEntidadAutorizante
     *
     * @return string 
     */
    public function getDenominacionEntidadAutorizante() {
        return $this->denominacionEntidadAutorizante;
    }

    /**
     * Set montoDesde
     *
     * @param float $montoDesde
     * @return EntidadAutorizante
     */
    public function setMontoDesde($montoDesde) {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return float 
     */
    public function getMontoDesde() {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param float $montoHasta
     * @return EntidadAutorizante
     */
    public function setMontoHasta($montoHasta) {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return float 
     */
    public function getMontoHasta() {
        return $this->montoHasta;
    }

    /**
     * Get idGrupo
     *
     * @return integer 
     */
    public function getIdGrupo() {
        return $this->idGrupo;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Grupo $grupo
     */
    public function setGrupo($grupo) {

        if (null != $grupo) {
            $this->idGrupo = $grupo->getId();
        } //.
        else {
            $this->idGrupo = null;
        }

        $this->grupo = $grupo;
    }

    /**
     * 
     * @return type \ADIF\AutenticacionBundle\Entity\Grupo
     */
    public function getGrupo() {
        return $this->grupo;
    }

    /**
     * Add solicitudes
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes
     * @return EntidadAutorizante
     */
    public function addSolicitud(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes) {
        $this->solicitudes[] = $solicitudes;

        return $this;
    }

    /**
     * Remove solicitudes
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes
     */
    public function removeSolicitud(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudes) {
        $this->solicitudes->removeElement($solicitudes);
    }

    /**
     * Get solicitudes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSolicitudes() {
        return $this->solicitudes;
    }

}
