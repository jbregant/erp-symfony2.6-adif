<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CuentaPresupuestariaEconomica    
 *
 * @author Manuel Becerra
 * created 01/10/2014
 * 
 * @ORM\Table(name="cuenta_presupuestaria_economica")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuentaPresupuestariaEconomicaRepository")
 * @UniqueEntity(
 *      fields = {"codigo", "fechaBaja"}, 
 *      ignoreNull = false, 
 *      message="El código ingresado ya se encuentra en uso."
 * )
 */
class CuentaPresupuestariaEconomica extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código de la cuenta contable no puede superar los {{ limit }} caracteres.")
     * )
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_interno", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código interno no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoInterno;

    /**
     * @ORM\OneToMany(targetEntity="CuentaPresupuestariaObjetoGasto", mappedBy="cuentaPresupuestariaEconomica")
     * 
     */
    protected $cuentasPresupuestariasObjetoGasto;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica", inversedBy="cuentasPresupuestariasEconomicasHijas" )
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica_padre", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"codigo" = "ASC"})
     */
    protected $cuentaPresupuestariaEconomicaPadre;

    /**
     * @ORM\OneToMany(targetEntity="CuentaPresupuestariaEconomica", mappedBy="cuentaPresupuestariaEconomicaPadre")
     */
    protected $cuentasPresupuestariasEconomicasHijas;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esImputable", type="boolean", nullable=false)
     */
    protected $esImputable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suma", type="boolean", nullable=false)
     */
    protected $suma;

    /**
     * @var \ADIF\ContableBundle\Entity\CategoriaCuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CategoriaCuentaPresupuestariaEconomica")
     * @ORM\JoinColumn(name="id_categoria_cuenta_presupuestaria_economica", referencedColumnName="id")
     * 
     */
    protected $categoriaCuentaPresupuestariaEconomica;

    /**
     * @ORM\OneToMany(targetEntity="Provisorio", mappedBy="cuentaPresupuestariaEconomica")
     */
    protected $provisorios;

    /**
     * @ORM\OneToMany(targetEntity="Definitivo", mappedBy="cuentaPresupuestariaEconomica")
     */
    protected $definitivos;

    /**
     * @ORM\OneToMany(targetEntity="Devengado", mappedBy="cuentaPresupuestariaEconomica")
     */
    protected $devengados;

    /**
     * @ORM\OneToMany(targetEntity="Ejecutado", mappedBy="cuentaPresupuestariaEconomica")
     */
    protected $ejecutados;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esImputable = false;
        $this->suma = true;
        $this->cuentasPresupuestariasObjetoGasto = new ArrayCollection();
        $this->cuentasPresupuestariasEconomicasHijas = new ArrayCollection();
        $this->provisorios = new ArrayCollection();
        $this->definitivos = new ArrayCollection();
        $this->devengados = new ArrayCollection();
        $this->ejecutados = new ArrayCollection();
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
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->codigo . ' - ' . $this->denominacion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return CuentaPresupuestariaEconomica
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return CuentaPresupuestariaEconomica
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return CuentaPresupuestariaEconomica
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
     * Set codigoInterno
     *
     * @param string $codigoInterno
     * @return CuentaPresupuestariaEconomica
     */
    public function setCodigoInterno($codigoInterno) {
        $this->codigoInterno = $codigoInterno;

        return $this;
    }

    /**
     * Get codigoInterno
     *
     * @return string 
     */
    public function getCodigoInterno() {
        return $this->codigoInterno;
    }

    /**
     * Add cuentasPresupuestariasObjetoGasto
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentasPresupuestariasObjetoGasto
     * @return CuentaPresupuestariaEconomica
     */
    public function addCuentasPresupuestariasObjetoGasto(\ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentasPresupuestariasObjetoGasto) {
        $this->cuentasPresupuestariasObjetoGasto[] = $cuentasPresupuestariasObjetoGasto;

        return $this;
    }

    /**
     * Remove cuentasPresupuestariasObjetoGasto
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentasPresupuestariasObjetoGasto
     */
    public function removeCuentasPresupuestariasObjetoGasto(\ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto $cuentasPresupuestariasObjetoGasto) {
        $this->cuentasPresupuestariasObjetoGasto->removeElement($cuentasPresupuestariasObjetoGasto);
    }

    /**
     * Get cuentasPresupuestariasObjetoGasto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasPresupuestariasObjetoGasto() {
        return $this->cuentasPresupuestariasObjetoGasto;
    }

    /**
     * Set cuentaPresupuestariaEconomicaPadre
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return CuentaPresupuestariaEconomica
     */
    public function setCuentaPresupuestariaEconomicaPadre(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica = null) {
        $this->cuentaPresupuestariaEconomicaPadre = $cuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaEconomicaPadre
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica 
     */
    public function getCuentaPresupuestariaEconomicaPadre() {
        return $this->cuentaPresupuestariaEconomicaPadre;
    }

    /**
     * Add cuentasPresupuestariasEconomicasHijas
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return CuentaPresupuestariaEconomica
     */
    public function addCuentaPresupuestariaEconomicaHija(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica) {
        $this->cuentasPresupuestariasEconomicasHijas[] = $cuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Remove cuentasPresupuestariasEconomicasHijas
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     */
    public function removeCuentaPresupuestariaEconomicaHija(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica) {
        $this->cuentasPresupuestariasEconomicasHijas->removeElement($cuentaPresupuestariaEconomica);
    }

    /**
     * Get cuentasPresupuestariasEconomicasHijas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasPresupuestariasEconomicasHijas() {
        return $this->cuentasPresupuestariasEconomicasHijas;
    }

    /**
     * Set esImputable
     *
     * @param boolean $esImputable
     * @return CuentaPresupuestariaEconomica
     */
    public function setEsImputable($esImputable) {
        $this->esImputable = $esImputable;

        return $this;
    }

    /**
     * Get esImputable
     *
     * @return boolean 
     */
    public function getEsImputable() {
        return $this->esImputable;
    }

    /**
     * Set suma
     *
     * @param boolean $suma
     * @return CuentaPresupuestariaEconomica
     */
    public function setSuma($suma) {
        $this->suma = $suma;

        return $this;
    }

    /**
     * Get suma
     *
     * @return boolean 
     */
    public function getSuma() {
        return $this->suma;
    }

    /**
     * Set categoriaCuentaPresupuestariaEconomica
     *
     * @param boolean $categoriaCuentaPresupuestariaEconomica
     * @return CuentaPresupuestariaEconomica
     */
    public function setCategoriaCuentaPresupuestariaEconomica($categoriaCuentaPresupuestariaEconomica) {
        $this->categoriaCuentaPresupuestariaEconomica = $categoriaCuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get categoriaCuentaPresupuestariaEconomica
     *
     * @return boolean 
     */
    public function getCategoriaCuentaPresupuestariaEconomica() {
        return $this->categoriaCuentaPresupuestariaEconomica;
    }

    /**
     * Add provisorios
     *
     * @param \ADIF\ContableBundle\Entity\Provisorio $provisorios
     * @return CuentaPresupuestariaEconomica
     */
    public function addProvisorio(\ADIF\ContableBundle\Entity\Provisorio $provisorios) {
        $this->provisorios[] = $provisorios;

        return $this;
    }

    /**
     * Remove provisorios
     *
     * @param \ADIF\ContableBundle\Entity\Provisorio $provisorios
     */
    public function removeProvisorio(\ADIF\ContableBundle\Entity\Provisorio $provisorios) {
        $this->provisorios->removeElement($provisorios);
    }

    /**
     * Get provisorios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProvisorios() {
        return $this->provisorios;
    }

    /**
     * Add definitivos
     *
     * @param \ADIF\ContableBundle\Entity\Definitivo $definitivos
     * @return CuentaPresupuestariaEconomica
     */
    public function addDefinitivo(\ADIF\ContableBundle\Entity\Definitivo $definitivos) {
        $this->definitivos[] = $definitivos;

        return $this;
    }

    /**
     * Remove definitivos
     *
     * @param \ADIF\ContableBundle\Entity\Definitivo $definitivos
     */
    public function removeDefinitivo(\ADIF\ContableBundle\Entity\Definitivo $definitivos) {
        $this->definitivos->removeElement($definitivos);
    }

    /**
     * Get definitivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefinitivos() {
        return $this->definitivos;
    }

    /**
     * Add devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     * @return CuentaPresupuestariaEconomica
     */
    public function addDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados[] = $devengados;

        return $this;
    }

    /**
     * Remove devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     */
    public function removeDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados->removeElement($devengados);
    }

    /**
     * Get devengados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevengados() {
        return $this->devengados;
    }

    /**
     * Add ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     * @return CuentaPresupuestariaEconomica
     */
    public function addEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados[] = $ejecutados;

        return $this;
    }

    /**
     * Remove ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     */
    public function removeEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados->removeElement($ejecutados);
    }

    /**
     * Get ejecutados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEjecutados() {
        return $this->ejecutados;
    }

    /**
     * Retorna true si la CuentaPresupuestariaEconomica es raiz. Caso contrario
     * retorna false.
     * 
     * @return type boolean
     */
    public function getEsRaiz() {
        return null == $this->cuentaPresupuestariaEconomicaPadre;
    }

    /**
     * Retorna el nivel de la CuentaPresupuestariaEconomica.
     * 
     * @return int
     */
    public function getNivel() {

        if (null != $this->cuentaPresupuestariaEconomicaPadre) {
            return $this->cuentaPresupuestariaEconomicaPadre->getNivel() + 1;
        } //.
        else {
            return 1;
        }
    }

    /**
     * Retorna el código inicial de la Cuenta. Por ejemplo:
     * 
     * Si la CuentaPresupuestariaEconomica es de nivel 3 y su código es 1.1.2.0.0.0
     * El método retorna 1.1.2
     * 
     * @return string
     */
    public function getCodigoInicial() {

        $codigoInicial = "";

        $index = 0;

        $segmentoArray = explode('.', $this->getCodigo());

        foreach ($segmentoArray as $caracter) {
            $index++;
            if (intval($caracter) != 0) {
                $nivel = $index;
            }
        }

        for ($i = 0; $i < $nivel; $i++) {
            $codigoInicial .= $segmentoArray[$i] . '.';
        }

        return $codigoInicial;
    }

    /**
     * Retorna en un array, todos los id de las CuentaPresupuestariaEconomica padres, llegando 
     * hasta la respectiva CuentaPresupuestariaEconomica raiz.
     * 
     * @return array
     */
    public function getCuentaPresupuestariaEconomicaPadreIds() {

        $cuentasPadresIds = array();

        if (!$this->getEsRaiz()) {
            $cuentasPadresIds = array_merge_recursive(
                    $this->cuentaPresupuestariaEconomicaPadre->getCuentaPresupuestariaEconomicaPadreIds(), //
                    array($this->cuentaPresupuestariaEconomicaPadre->getId()));
        }

        return $cuentasPadresIds;
    }

    /**
     * 
     * @return int
     */
    public function getMontoSectorPublico($mesInicio, $mesFin, $ejercicio) {

        $fechaInicio = new \DateTime(
                date('Y-m-d', strtotime(
                                (new \DateTime($ejercicio . '-' . $mesInicio . '-01'))
                                        ->format("Y-m-d"))
                )
        );

        // Si el mes es mayor a Enero
        if ($mesFin > 1) {
            $fechaFinMesAnterior = new \DateTime(
                    date('Y-m-d H:i:s', strtotime(
                                    (new \DateTime($ejercicio . '-' . ($mesFin - 1) . '-01 23:59:59'))
                                            ->format("Y-m-t H:i:s"))
                    )
            );
        } else {
            $fechaFinMesAnterior = new \DateTime(
                    date('Y-m-d H:i:s', strtotime(
                                    (new \DateTime($ejercicio . '-' . $mesFin . '-01 23:59:59'))
                                            ->format("Y-m-t H:i:s"))
                    )
            );
        }

        return $this->getMontoTotalPresupuestarioByRangoFecha($fechaInicio, $fechaFinMesAnterior);
    }

    /**
     * 
     * @return int
     */
    public function getMontoSectorPrivado($mesInicio, $mesFin, $ejercicio) {
        return 0;
    }

    /**
     * 
     * @return int
     */
    public function getMontoEjecucionMes($mesInicio, $mesFin, $ejercicio) {

        $fechaInicio = new \DateTime(
                date('Y-m-d', strtotime(
                                (new \DateTime($ejercicio . '-' . $mesFin . '-01'))
                                        ->format("Y-m-d"))
                )
        );

        $fechaFin = new \DateTime(
                date('Y-m-d H:i:s', strtotime(
                                (new \DateTime($ejercicio . '-' . $mesFin . '-01 23:59:59'))
                                        ->format("Y-m-t H:i:s"))
                )
        );

        return $this->getMontoTotalPresupuestarioByRangoFecha($fechaInicio, $fechaFin);
    }

    /**
     * 
     * @return int
     */
    public function getMontoTotalAcumulado($mesInicio, $mesFin, $ejercicio) {

        $fechaInicio = new \DateTime(
                date('Y-m-d', strtotime(
                                (new \DateTime($ejercicio . '-' . $mesInicio . '-01'))
                                        ->format("Y-m-d"))
                )
        );

        $fechaFin = new \DateTime(
                date('Y-m-d H:i:s', strtotime(
                                (new \DateTime($ejercicio . '-' . $mesFin . '-01 23:59:59'))
                                        ->format("Y-m-t H:i:s"))
                )
        );

        return $this->getMontoTotalPresupuestarioByRangoFecha($fechaInicio, $fechaFin);
    }

    /**
     * 
     * @param type $fechaInicio
     * @param type $fechaFin
     * @return type
     */
    private function getMontoTotalPresupuestarioByRangoFecha($fechaInicio, $fechaFin) {

        $monto = 0;

        // Por cada devengado
        foreach ($this->devengados as $devengado) {

            if ($devengado->getEjecutado() == null //
                    && $devengado->getFechaDevengado() <= $fechaFin //
                    && $devengado->getFechaDevengado() >= $fechaInicio) {
                $monto += $devengado->getMonto();
            }
        }

        // Por cada ejecutado
        foreach ($this->ejecutados as $ejecutado) {
            if ($ejecutado->getFechaEjecutado() <= $fechaFin //
                    && $ejecutado->getFechaEjecutado() >= $fechaInicio) {
                $monto += $ejecutado->getMonto();
            }
        }
        if (!$this->esImputable) {
            foreach ($this->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomicaHija) {
                $monto += $cuentaPresupuestariaEconomicaHija->getMontoTotalPresupuestarioByRangoFecha($fechaInicio, $fechaFin);
            }
        }

        return $monto;
    }

    /**
     * Get provisorios sin definitivos
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProvisoriosSinDefinitivo() {
        return $this->provisorios->filter(
                        function($provisorio) {
                    return $provisorio->getDefinitivo() == null;
                }
        );
    }

    /**
     * Get definitivos con saldo
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefinitivosConSaldo() {

        return $this->definitivos->filter(function($definitivo) {
                    return $definitivo->getSaldo() > 0;
                }
        );
    }

    /**
     * Get devengados sin ejecutado
     * 
     * @return Collection 
     */
    public function getDevengadosSinEjecutado() {

        return $this->devengados->filter(
                        function($devengado) {
                    return $devengado->getEjecutado() == null;
                }
        );
    }

    /**
     * 
     * @param type $fechaEjercicioInicio
     * @param type $fechaEjercicioFin
     * @return type
     */
    public function getTotalProvisorio($fechaEjercicioInicio, $fechaEjercicioFin) {

        $suma = 0;

        // Por cada cuenta presupuestaria economica hija
        foreach ($this->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomicaHija) {
            $suma += $cuentaPresupuestariaEconomicaHija
                    ->getTotalProvisorio($fechaEjercicioInicio, $fechaEjercicioFin);
        }

        foreach ($this->getProvisoriosSinDefinitivo() as $provisorio) {

            // Si el provisorio pertenece al Ejercicio recibido como parámetro
            if ($provisorio->getFechaProvisorio() >= $fechaEjercicioInicio //
                    && $provisorio->getFechaProvisorio() <= $fechaEjercicioFin) {

                $suma += $provisorio->getMonto();
            }
        }

        return $suma;
    }

    /**
     * 
     * @param type $fechaEjercicioInicio
     * @param type $fechaEjercicioFin
     * @return type
     */
    public function getTotalDefinitivo($fechaEjercicioInicio, $fechaEjercicioFin) {

        $suma = 0;

        // Por cada cuenta presupuestaria economica hija
        foreach ($this->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomicaHija) {
            $suma += $cuentaPresupuestariaEconomicaHija
                    ->getTotalDefinitivo($fechaEjercicioInicio, $fechaEjercicioFin);
        }

        foreach ($this->getDefinitivosConSaldo() as $definitivo) {

            // Si el definitivo pertenece al Ejercicio recibido como parámetro
            if ($definitivo->getFechaDefinitivo() >= $fechaEjercicioInicio //
                    && $definitivo->getFechaDefinitivo() <= $fechaEjercicioFin) {

                // Sumo el SALDO del definitivo
                $suma += $definitivo->getSaldo();
            }
        }

        return $suma;
    }

    /**
     * 
     * @param type $fechaEjercicioInicio
     * @param type $fechaEjercicioFin
     * @return type
     */
    public function getTotalDevengado($fechaEjercicioInicio, $fechaEjercicioFin) {

        $suma = 0;

        // Por cada cuenta presupuestaria economica hija
        foreach ($this->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomicaHija) {
            $suma += $cuentaPresupuestariaEconomicaHija
                    ->getTotalDevengado($fechaEjercicioInicio, $fechaEjercicioFin);
        }

        foreach ($this->getDevengadosSinEjecutado() as $devengado) {

            // Si el devengado pertenece al Ejercicio recibido como parámetro
            if ($devengado->getFechaDevengado() >= $fechaEjercicioInicio //
                    && $devengado->getFechaDevengado() <= $fechaEjercicioFin) {

                $suma += $devengado->getMonto();
            }
        }

        return $suma;
    }

    /**
     * 
     * @param type $fechaEjercicioInicio
     * @param type $fechaEjercicioFin
     * @return type
     */
    public function getTotalEjecutado($fechaEjercicioInicio, $fechaEjercicioFin) {

        $suma = 0;

        // Por cada cuenta presupuestaria economica hija
        foreach ($this->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomicaHija) {
            $suma += $cuentaPresupuestariaEconomicaHija
                    ->getTotalEjecutado($fechaEjercicioInicio, $fechaEjercicioFin);
        }

        foreach ($this->ejecutados as $ejecutado) {

            // Si el ejecutado pertenece al Ejercicio recibido como parámetro
            if ($ejecutado->getFechaEjecutado() >= $fechaEjercicioInicio //
                    && $ejecutado->getFechaEjecutado() <= $fechaEjercicioFin) {

                $suma += $ejecutado->getMonto();
            }
        }

        return $suma;
    }

}
