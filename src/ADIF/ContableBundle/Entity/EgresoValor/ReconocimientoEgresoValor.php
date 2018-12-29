<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ReconocimientoEgresoValor
 * 
 * @ORM\Table(name="reconocimiento_egreso_valor")
 * @ORM\Entity
 */
class ReconocimientoEgresoValor extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_reconocimiento", type="datetime", nullable=false)
     */
    protected $fechaReconocimiento;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    protected $monto;

    /**
     * @var EgresoValor
     * 
     * @ORM\ManyToOne(targetEntity="EgresoValor", inversedBy="reconocimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    private $egresoValor;

    /**
     * @var EstadoReconocimientoEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="EstadoReconocimientoEgresoValor")
     * @ORM\JoinColumn(name="id_estado_reconocimiento_egreso_valor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoReconocimientoEgresoValor;

    /**
     * @var ResponsableEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="ResponsableEgresoValor", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_responsable_egreso_valor", referencedColumnName="id", nullable=true)
     * })
     */
    protected $responsableEgresoValor;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="OrdenPagoReconocimientoEgresoValor", mappedBy="reconocimientoEgresoValor")
     */
    protected $ordenesPago;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaReconocimiento = new \DateTime();
        $this->monto = 0;
        $this->ordenesPago = new ArrayCollection();
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
     * Set fechaReconocimiento
     *
     * @param \DateTime $fechaReconocimiento
     * @return ReconocimientoEgresoValor
     */
    public function setFechaReconocimiento($fechaReconocimiento) {
        $this->fechaReconocimiento = $fechaReconocimiento;

        return $this;
    }

    /**
     * Get fechaReconocimiento
     *
     * @return \DateTime 
     */
    public function getFechaReconocimiento() {
        return $this->fechaReconocimiento;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return ReconocimientoEgresoValor
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set egresoValor
     *
     * @param EgresoValor $egresoValor
     * @return ReconocimientoEgresoValor
     */
    public function setEgresoValor(EgresoValor $egresoValor) {
        $this->egresoValor = $egresoValor;

        return $this;
    }

    /**
     * Get egresoValor
     *
     * @return EgresoValor 
     */
    public function getEgresoValor() {
        return $this->egresoValor;
    }

    /**
     * Set estadoReconocimientoEgresoValor
     *
     * @param EstadoReconocimientoEgresoValor $estadoReconocimientoEgresoValor
     * @return ReconocimientoEgresoValor
     */
    public function setEstadoReconocimientoEgresoValor(EstadoReconocimientoEgresoValor $estadoReconocimientoEgresoValor) {
        $this->estadoReconocimientoEgresoValor = $estadoReconocimientoEgresoValor;

        return $this;
    }

    /**
     * Get estadoReconocimientoEgresoValor
     *
     * @return EstadoReconocimientoEgresoValor
     */
    public function getEstadoReconocimientoEgresoValor() {
        return $this->estadoReconocimientoEgresoValor;
    }

    /**
     * Set responsableEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor
     * @return ReconocimientoEgresoValor
     */
    public function setResponsableEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor) {
        $this->responsableEgresoValor = $responsableEgresoValor;

        return $this;
    }

    /**
     * Get responsableEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor 
     */
    public function getResponsableEgresoValor() {
        return $this->responsableEgresoValor;
    }

    /**
     * Add ordenPago
     *
     * @param OrdenPagoReconocimientoEgresoValor $ordenPago
     * @return ReconocimientoEgresoValor
     */
    public function addOrdenesPago(OrdenPagoReconocimientoEgresoValor $ordenPago) {
        $this->ordenesPago[] = $ordenPago;

        return $this;
    }

    /**
     * Remove ordenPago
     *
     * @param OrdenPagoReconocimientoEgresoValor $ordenPago
     */
    public function removeOrdenesPago(OrdenPagoReconocimientoEgresoValor $ordenPago) {
        $this->ordenesPago->removeElement($ordenPago);
    }

    /**
     * Get ordenesPago
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesPago() {
        return $this->ordenesPago;
    }

    /**
     * Get estaCreada
     * 
     * @return boolean
     */
    public function getEstaCreada() {

        $estaCreada = false;

        if (!$this->ordenesPago->isEmpty()) {

            /* @var $ordenPago OrdenPagoReconocimientoEgresoValor */
            foreach ($this->ordenesPago as $ordenPago) {
                if (null != $ordenPago->getEstadoOrdenPago()) {
                    if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
                        $estaCreada = true;
                        break;
                    }
                }
            }
        }

        return $estaCreada;
    }

}
