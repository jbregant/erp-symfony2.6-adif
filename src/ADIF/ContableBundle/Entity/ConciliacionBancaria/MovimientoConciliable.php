<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoConciliable
 *
 * @author Darío Rapetti
 * created 21/01/2015
 * 
 * @ORM\Table(name="movimiento_conciliable")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "cheque" = "ADIF\ContableBundle\Entity\Cheque",
 *      "transferencia" = "ADIF\ContableBundle\Entity\TransferenciaBancaria",
 *      "movimiento_bancario" = "ADIF\ContableBundle\Entity\MovimientoBancario",
 *      "movimiento_ministerial" = "ADIF\ContableBundle\Entity\MovimientoMinisterial",
 *      "netcash" = "ADIF\ContableBundle\Entity\NetCash",
 *      "gasto_bancario" = "ADIF\ContableBundle\Entity\ConciliacionBancaria\GastoBancario",
 *      "devolucion_dinero" = "ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero",
 *      "renglon_cobranza" = "ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza",
 *      "renglon_cobranza_banco" = "ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco",
 *      "renglon_cobranza_cheque" = "ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque"
 * })
 */
abstract class MovimientoConciliable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_contable", type="datetime", nullable=true)
     */
    protected $fechaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion", inversedBy="movimientosConciliables")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_conciliacion", referencedColumnName="id", nullable=true)
     * })
     */
    protected $conciliacion;

    /**
     * @ORM\ManyToMany(targetEntity="Conciliacion", mappedBy="partidasConciliatoriasMayor")
     * */
    protected $conciliaciones;

    /**
     * Constructor
     */
    public function __construct() {

        $this->fechaContable = new \DateTime();

        $this->conciliaciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fechaContable
     *
     * @param \DateTime $fechaContable
     * @return MovimientoConciliable
     */
    public function setFechaContable($fechaContable) {
        $this->fechaContable = $fechaContable;

        return $this;
    }

    /**
     * Get fechaContable
     *
     * @return \DateTime 
     */
    public function getFechaContable() {
        return $this->fechaContable;
    }

    /**
     * Set conciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion
     * @return MovimientoConciliable
     */
    public function setConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion = null) {
        $this->conciliacion = $conciliacion;

        return $this;
    }

    /**
     * Get conciliacion
     *
     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion 
     */
    public function getConciliacion() {
        return $this->conciliacion;
    }

    public function getEsContabilizable() {
        return true;
    }

    /**
     * Add conciliaciones
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones
     * @return MovimientoConciliable
     */
    public function addConciliacione(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones) {
        $this->conciliaciones[] = $conciliaciones;

        return $this;
    }

    /**
     * Remove conciliaciones
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones
     */
    public function removeConciliacione(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliaciones) {
        $this->conciliaciones->removeElement($conciliaciones);
    }

    /**
     * Get conciliaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConciliaciones() {
        return $this->conciliaciones;
    }

    public function getCodigoConcepto() {
        return 0;
    }
    
    public function getFechaParaMayor() {
        return $this->getFecha();
    }
    
    public function estaConciliado() {
        return ($this->getConciliacion() != null || sizeof($this->getConciliaciones()) != 0);
   
    }

}
