<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReposicionEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;

/**
 * EgresoValor
 * 
 * @ORM\Table(name="egreso_valor")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\EgresoValorRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "egreso_valor_general" = "EgresoValor",
 *      "caja_chica" = "CajaChica",
 *      "cargo_a_rendir" = "CargoARendir",
 *      "viaticos" = "Viaticos",
 *      "combustible" = "Combustible",
 *      "fondo_fijo_servicios" = "FondoFijoServicios"
 * })
 * )
 */
class EgresoValor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var EstadoEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="EstadoEgresoValor")
     * @ORM\JoinColumn(name="id_estado_egreso_valor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoEgresoValor;

    /**
     * @var string
     *
     * @ORM\Column(name="carpeta", type="string", length=55, nullable=false)
     */
    protected $carpeta;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=1000, nullable=true)
     */
    protected $observaciones;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoEgresoValor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoEgresoValor;

    /**
     * @ORM\Column(name="id_gerencia", type="integer", nullable=true)
     */
    protected $idGerencia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Gerencia
     */
    protected $gerencia;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    protected $importe;

    /**
     * @var integer
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
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor", mappedBy="egresoValor", cascade={"all"})
     */
    protected $reposiciones;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor", mappedBy="egresoValor", cascade={"all"})
     */
    protected $rendiciones;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor", mappedBy="egresoValor")
     */
    protected $ordenesPago;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor", mappedBy="egresoValor")
     */
    protected $reconocimientos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    protected $fechaCierre;

    /**
     * Constructor
     */
    public function __construct() {
        $this->rendiciones = new ArrayCollection();
        $this->reposiciones = new ArrayCollection();
        $this->ordenesPago = new ArrayCollection();
        $this->reconocimientos = new ArrayCollection();
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
     * Set carpeta
     *
     * @param string $carpeta
     * @return EgresoValor
     */
    public function setCarpeta($carpeta) {
        $this->carpeta = $carpeta;

        return $this;
    }

    /**
     * Get carpeta
     *
     * @return string 
     */
    public function getCarpeta() {
        return $this->carpeta;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return EgresoValor
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Set idGerencia
     *
     * @param integer $idGerencia
     * @return EgresoValor
     */
    public function setIdGerencia($idGerencia) {
        $this->idGerencia = $idGerencia;

        return $this;
    }

    /**
     * Get idGerencia
     *
     * @return integer 
     */
    public function getIdGerencia() {
        return $this->idGerencia;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia
     */
    public function setGerencia($gerencia) {

        if (null != $gerencia) {
            $this->idGerencia = $gerencia->getId();
        } else {
            $this->idGerencia = null;
        }

        $this->gerencia = $gerencia;
    }

    /**
     * 
     * @return type
     */
    public function getGerencia() {
        return $this->gerencia;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return EgresoValor
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string 
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * Set tipoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor $tipoEgresoValor
     * @return EgresoValor
     */
    public function setTipoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor $tipoEgresoValor) {
        $this->tipoEgresoValor = $tipoEgresoValor;

        return $this;
    }

    /**
     * Get tipoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor 
     */
    public function getTipoEgresoValor() {
        return $this->tipoEgresoValor;
    }

    /**
     * Set responsableEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor
     * @return EgresoValor
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
     * Get rendiciones
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getRendiciones() {
        return $this->rendiciones;
    }

    /**
     * Add rendiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor
     * @return EgresoValor
     */
    public function addRendicion(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor) {
        $this->rendiciones[] = $rendicionEgresoValor;
        $rendicionEgresoValor->setEgresoValor($this);
        return $this;
    }

    /**
     * Remove rendiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor
     */
    public function removeRendicion(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor) {
        $this->rendiciones->removeElement($rendicionEgresoValor);
        $rendicionEgresoValor->setEgresoValor(null);
    }

    /**
     * Get reposiciones
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getReposiciones() {
        return $this->reposiciones;
    }

    /**
     * Add reposiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor
     * @return EgresoValor
     */
    public function addReposicion(\ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor) {
        $this->reposiciones[] = $reposicionEgresoValor;
        $reposicionEgresoValor->setEgresoValor($this);
        return $this;
    }

    /**
     * Remove reposiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor
     */
    public function removeReposicion(\ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposicionEgresoValor) {
        $this->reposiciones->removeElement($reposicionEgresoValor);
        $reposicionEgresoValor->setEgresoValor(null);
    }

    /**
     * Add ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenesPago
     * @return EgresoValor
     */
    public function addOrdenesPago(\ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenesPago
     */
    public function removeOrdenesPago(\ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenesPago) {
        $this->ordenesPago->removeElement($ordenesPago);
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

            /* @var $ordenPago OrdenPagoEgresoValor */
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

    /**
     * 
     * @return float
     */
    public function getSaldo() {
        return $this->importe - $this->getImporteRendido();
    }

    /**
     * 
     * @return float
     */
    public function getImporteAReponer() {
        return $this->importe - $this->getSaldo();
    }

    /**
     * 
     * @return float
     */
    public function getImporteRendido() {

        $importeTotal = 0;

        $ultimaReposicion = $this->getUltimaReposicionPagada();

        if ($ultimaReposicion != null) {

            foreach ($this->rendiciones as $rendicion) {

                $codigoEstadoRendicion = $rendicion->getEstadoRendicionEgresoValor()->getCodigo();

                if ($codigoEstadoRendicion == ConstanteEstadoRendicionEgresoValor::ESTADO_GENERADA //
                        && $rendicion->getFechaRendicion() >= $ultimaReposicion->getFechaReposicion()) {
                    $importeTotal += $rendicion->getImporteRendido();
                }
            }

            foreach ($this->reconocimientos as $reconocimiento) {

                /* @var $reconocimiento \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor */

                $codigoEstadoReconocimiento = $reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo();

                if ($codigoEstadoReconocimiento == ConstanteEstadoReconocimientoEgresoValor::ESTADO_NO_RECONOCIDO || $reconocimiento->getEstaCreada()) {

                    if ((($codigoEstadoReconocimiento == ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO) //
                            || ($codigoEstadoReconocimiento == ConstanteEstadoReconocimientoEgresoValor::ESTADO_NO_RECONOCIDO)) //
                            && $reconocimiento->getFechaReconocimiento() >= $ultimaReposicion->getFechaReposicion()) {

                        $importeTotal -= $reconocimiento->getMonto();
                    }
                }
            }
        } else {
            $importeTotal = $this->importe;
        }


        return $importeTotal;
    }

    /**
     * Set estadoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor $estadoEgresoValor
     * @return EgresoValor
     */
    public function setEstadoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor $estadoEgresoValor) {
        $this->estadoEgresoValor = $estadoEgresoValor;

        return $this;
    }

    /**
     * Get estadoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor 
     */
    public function getEstadoEgresoValor() {
        return $this->estadoEgresoValor;
    }

    /**
     * Add reposiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposiciones
     * @return EgresoValor
     */
    public function addReposicione(\ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposiciones) {
        $this->reposiciones[] = $reposiciones;

        $reposiciones->setEgresoValor($this);

        return $this;
    }

    /**
     * Remove reposiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposiciones
     */
    public function removeReposicione(\ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor $reposiciones) {
        $this->reposiciones->removeElement($reposiciones);
    }

    /**
     * Add rendiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendiciones
     * @return EgresoValor
     */
    public function addRendicione(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendiciones) {
        $this->rendiciones[] = $rendiciones;

        return $this;
    }

    /**
     * Remove rendiciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendiciones
     */
    public function removeRendicione(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendiciones) {
        $this->rendiciones->removeElement($rendiciones);
    }

    /**
     * 
     * @return float
     */
    public function getPorcentajeRendido() {
        if ($this->estadoEgresoValor->getCodigo() == ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE) {
            return 0;
        } else {
            return ($this->getImporte() > 0 ) //
                    ? round(($this->getImporteRendido() * 100 / $this->getImporte()), 2) //
                    : 0;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function getTieneRendicionEgresoValor() {

        $tieneRendicion = false;

        if (!empty($this->rendiciones)) {

            // Por cada rendicion asociada al EgresoValor
            foreach ($this->rendiciones as $rendicion) {

                // Si hay una rendición en estado "Borrador"
                if ($rendicion->getEstadoRendicionEgresoValor()->getCodigo() == ConstanteEstadoRendicionEgresoValor::ESTADO_BORRADOR) {
                    $tieneRendicion = true;
                    break;
                }
            }
        }

        return $tieneRendicion;
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor
     */
    public function getRendicionEgresoValor() {

        $rendicionEgresoValor = null;

        // Si el EgresoValor NO tiene rendiciones cargadas
        if (empty($this->rendiciones)) {
            $rendicionEgresoValor = new RendicionEgresoValor();
            $rendicionEgresoValor->setEgresoValor($this);
            $rendicionEgresoValor->setResponsableEgresoValor($this->responsableEgresoValor);
        } else {

            // Por cada rendicion asociada al EgresoValor
            foreach ($this->rendiciones as $rendicion) {

                // Si hay una rendición en estado "Borrador"
                if ($rendicion->getEstadoRendicionEgresoValor()->getCodigo() == ConstanteEstadoRendicionEgresoValor::ESTADO_BORRADOR) {
                    $rendicionEgresoValor = $rendicion;
                    break;
                }
            }

            // Si no hay rendiciones en estado "Borrador", creo una nueva
            if ($rendicionEgresoValor == null) {
                $rendicionEgresoValor = new RendicionEgresoValor();
                $rendicionEgresoValor->setEgresoValor($this);
                $rendicionEgresoValor->setResponsableEgresoValor($this->responsableEgresoValor);
            }
        }

        return $rendicionEgresoValor;
    }

    /**
     * Add reconocimientos
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientos
     * @return EgresoValor
     */
    public function addReconocimiento(\ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientos) {
        $this->reconocimientos[] = $reconocimientos;

        return $this;
    }

    /**
     * Remove reconocimientos
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientos
     */
    public function removeReconocimiento(\ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor $reconocimientos) {
        $this->reconocimientos->removeElement($reconocimientos);
    }

    /**
     * Get reconocimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReconocimientos() {
        return $this->reconocimientos;
    }

    /**
     * 
     * @return float
     */
    public function getUltimaReposicionPagada() {

        $ultimaFecha = new \DateTime('1900-01-01');
        $ultimaReposicion = null;

        if (!$this->reposiciones->isEmpty()) {


            foreach ($this->reposiciones as $reposicion) {

                /* @var $reposicion \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor */

                $codigoEstadoReposicion = $reposicion->getEstadoReposicionEgresoValor()->getCodigo();

                if ($codigoEstadoReposicion == ConstanteEstadoReposicionEgresoValor::ESTADO_PAGADA //
                        && $reposicion->getFechaReposicion() >= $ultimaFecha) {
                    $ultimaReposicion = $reposicion;
                    $ultimaFecha = $reposicion->getFechaReposicion();
                }
            }
        }


        return $ultimaReposicion;
    }

    /**
     * Set fechaCierre
     *
     * @param \DateTime $fechaCierre
     * @return EgresoValor
     */
    public function setFechaCierre($fechaCierre) {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre
     *
     * @return \DateTime 
     */
    public function getFechaCierre() {
        return $this->fechaCierre;
    }

}
