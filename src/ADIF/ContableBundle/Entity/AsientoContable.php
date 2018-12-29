<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\BaseBundle\Twig\TwigExtension;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of AsientoContable
 *
 * @author Manuel Becerra
 * created 24/09/2014
 * 
 * @ORM\Table(name="asiento_contable")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\AsientoContableRepository")
 */
class AsientoContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * Indica el número de asiento oficial. Es el que se modifica al modificar 
     * la fecha del asiento contable.
     * 
     * @var intenger
     * 
     * @ORM\Column(name="numero_asiento", type="integer", nullable=true)
     */
    protected $numeroAsiento;

    /**
     * @var intenger
     * 
     * @ORM\Column(name="numero_original", type="integer", nullable=true)
     */
    protected $numeroOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=1024, nullable=false)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="La denominacion no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionAsientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="TipoAsientoContable")
     * @ORM\JoinColumn(name="id_tipo_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $tipoAsientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\ConceptoAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="ConceptoAsientoContable")
     * @ORM\JoinColumn(name="id_concepto_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $conceptoAsientoContable;

    /**
     * Indica en qué momento se debe asentar el Asiento Contable. 
     * 
     * @var \Date
     *
     * @ORM\Column(name="fecha_contable", type="datetime", nullable=true)
     */
    protected $fechaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="EstadoAsientoContable")
     * @ORM\JoinColumn(name="id_estado_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $estadoAsientoContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fue_revertido", type="boolean", nullable=false)
     */
    protected $fueRevertido;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La razón social no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El numero de documento no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroDocumento;

    /**
     * @ORM\OneToMany(targetEntity="RenglonAsientoContable", mappedBy="asientoContable", cascade={"all"})
     * @ORM\OrderBy({"tipoOperacionContable" = "ASC", "cuentaContable" = "ASC"})
     */
    protected $renglonesAsientoContable;
	
	
	private $_debugAsientosMalBalanceados;

    /**
     * Constructor
     */
    public function __construct($debugAsientosMalBalanceados = false) {

        $this->fechaContable = new \DateTime();

        $this->fueRevertido = false;

        $this->renglonesAsientoContable = new ArrayCollection();
		
		$this->_debugAsientosMalBalanceados = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionAsientoContable;
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
     * 
     * @return type
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     */
    public function setUsuario($usuario) {

        if (null != $usuario) {
            $this->idUsuario = $usuario->getId();
        } //.
        else {
            $this->idUsuario = null;
        }

        $this->usuario = $usuario;
    }

    /**
     * 
     * @return type
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set numeroAsiento
     *
     * @param \intenger $numeroAsiento
     * @return AsientoContable
     */
    public function setNumeroAsiento($numeroAsiento) {
        $this->numeroAsiento = $numeroAsiento;

        return $this;
    }

    /**
     * Get numeroAsiento
     *
     * @return \intenger 
     */
    public function getNumeroAsiento($conFormato = true) {

        $numeroAsiento = $this->numeroAsiento;

        if ($conFormato) {
            $numeroAsiento = str_pad($this->numeroAsiento, 6, '0', STR_PAD_LEFT);
        }

        return $numeroAsiento;
    }

    /**
     * Set numeroOriginal
     *
     * @param \intenger $numeroOriginal
     * @return AsientoContable
     */
    public function setNumeroOriginal($numeroOriginal) {
        $this->numeroOriginal = $numeroOriginal;

        return $this;
    }

    /**
     * Get numeroOriginal
     *
     * @return \intenger 
     */
    public function getNumeroOriginal() {
        return str_pad($this->numeroOriginal, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Set denominacionAsientoContable
     *
     * @param string $denominacionAsientoContable
     * @return AsientoContable
     */
    public function setDenominacionAsientoContable($denominacionAsientoContable) {
        $this->denominacionAsientoContable = $denominacionAsientoContable;

        return $this;
    }

    /**
     * Get denominacionAsientoContable
     *
     * @return string 
     */
    public function getDenominacionAsientoContable() {
        return html_entity_decode($this->denominacionAsientoContable, ENT_QUOTES);
    }

    /**
     * Set fechaContable
     *
     * @param \DateTime $fechaContable
     * @return AsientoContable
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
     * Set tipoAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\TipoAsientoContable $tipoAsientoContable
     * @return AsientoContable
     */
    public function setTipoAsientoContable(\ADIF\ContableBundle\Entity\TipoAsientoContable $tipoAsientoContable = null) {
        $this->tipoAsientoContable = $tipoAsientoContable;

        return $this;
    }

    /**
     * Get tipoAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\TipoAsientoContable 
     */
    public function getTipoAsientoContable() {
        return $this->tipoAsientoContable;
    }

    /**
     * Set conceptoAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoAsientoContable $conceptoAsientoContable
     * @return AsientoContable
     */
    public function setConceptoAsientoContable(\ADIF\ContableBundle\Entity\ConceptoAsientoContable $conceptoAsientoContable = null) {
        $this->conceptoAsientoContable = $conceptoAsientoContable;

        return $this;
    }

    /**
     * Get conceptoAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoAsientoContable 
     */
    public function getConceptoAsientoContable() {
        return $this->conceptoAsientoContable;
    }

    /**
     * Set estadoAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\EstadoAsientoContable $estadoAsientoContable
     * @return AsientoContable
     */
    public function setEstadoAsientoContable(\ADIF\ContableBundle\Entity\EstadoAsientoContable $estadoAsientoContable = null) {
        $this->estadoAsientoContable = $estadoAsientoContable;

        return $this;
    }

    /**
     * Get estadoAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\EstadoAsientoContable 
     */
    public function getEstadoAsientoContable() {
        return $this->estadoAsientoContable;
    }

    /**
     * Set fueRevertido
     *
     * @param boolean $fueRevertido
     * @return AsientoContable
     */
    public function setFueRevertido($fueRevertido) {
        $this->fueRevertido = $fueRevertido;

        return $this;
    }

    /**
     * Get fueRevertido
     *
     * @return boolean 
     */
    public function getFueRevertido() {
        return $this->fueRevertido;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return AsientoContable
     */
    public function setRazonSocial($razonSocial) {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial() {
        return $this->razonSocial;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     * @return AsientoContable
     */
    public function setNumeroDocumento($numeroDocumento) {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string 
     */
    public function getNumeroDocumento() {
        return $this->numeroDocumento;
    }

    /**
     * Add renglonesAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\RenglonAsientoContable $renglonAsientoContable
     * @return AsientoContable
     */
    public function addRenglonesAsientoContable(\ADIF\ContableBundle\Entity\RenglonAsientoContable $renglonAsientoContable) {

        $renglonAsientoContable->setAsientoContable($this);

        $this->renglonesAsientoContable[] = $renglonAsientoContable;

        return $this;
    }

    /**
     * Remove renglonesAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\RenglonAsientoContable $renglonesAsientoContable
     */
    public function removeRenglonesAsientoContable(\ADIF\ContableBundle\Entity\RenglonAsientoContable $renglonesAsientoContable) {
        $this->renglonesAsientoContable->removeElement($renglonesAsientoContable);
    }

    /**
     * Get renglonesAsientoContable
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesAsientoContable() {
        return $this->renglonesAsientoContable;
    }

    /**
     * Get esManual
     * 
     * @return type
     */
    public function getEsManual() {

        return $this->tipoAsientoContable->getDenominacion() == ConstanteTipoAsientoContable::TIPO_ASIENTO_MANUAL;
    }

    /**
     * 
     * @return type
     */
    public function getAsientoBalanceado() {
        $totalDebe = $this->getTotalDebe();
        $totalHaber = $this->getTotalHaber();
        
        //var_dump(round($totalDebe, 2)." - ".round($totalHaber, 2)); exit;
        //$diferencia = TwigExtension::roundandmatch($totalDebe, $totalHaber);

        if (round($totalDebe, 2) != round($totalHaber, 2)) {
			//var_dump(round($totalDebe, 2)." - ".round($totalHaber, 2)); exit;
            
			if ($this->_debugAsientosMalBalanceados) {
				$this->debugAsiento();
			}
			
            return false;
        }

        return true;
    }

    /**
     * 
     * @return type
     */
    public function getTotalDebe() {

        return $this->getTotalByTipoOperacion(ConstanteTipoOperacionContable::DEBE);
    }

    /**
     * 
     * @return type
     */
    public function getTotalHaber() {

        return $this->getTotalByTipoOperacion(ConstanteTipoOperacionContable::HABER);
    }

    /**
     * 
     * @param type $tipoOperacion
     * @return type
     */
    public function getTotalByTipoOperacion($tipoOperacion) {

        $total = 0;

        foreach ($this->renglonesAsientoContable as $renglonAsientoContable) {
            if ($tipoOperacion == $renglonAsientoContable->getTipoOperacionContable()->getDenominacion()) {
                $total += $renglonAsientoContable->getImporteMCL();
            }
        }

        return $total;
    }
	
	/**
     * Get Id 
     *
     * @return \integer 
     */
    public function getIdConFormato() {
		return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
	
	private function debugAsiento()
	{
		$fullPath = $_SERVER['SCRIPT_FILENAME'];
		$arrFullPath = explode('/', $fullPath);
		$file = end($arrFullPath);
		if ($file == 'app_dev.php') {
			
			$tabla = '<h1>Debug de asientos no balanceados!</h1>';
			$tabla .= '<table border="1">
								<thead>
									<tr>
										<th width="50%" colspan ="2" class="th-cuenta-contable">Cuenta contable</th>
										<th width="10%" class="text-center">Debe</th>
										<th width="10%" class="text-center">Haber</th>
										<th width="30%">Detalle</th>
									</tr>
								</thead>';
								
			foreach($this->getRenglonesAsientoContable() as $renglon) {
				$tabla .= '<tr>';
				$tabla .= '<td colspan ="2">' . $renglon->getCuentaContable()->__toString() . '</td>';
				if ($renglon->getTipoOperacionContable()->getId() == 1) {
					// DEBE
					$tabla .= '<td>' . $renglon->getImporteMO() . '</td>';
					$tabla .= '<td>&nbsp;&nbsp;&nbsp;</td>';
				} else {
					// HABER
					$tabla .= '<td>&nbsp;&nbsp;&nbsp;</td>';
					$tabla .= '<td>' . $renglon->getImporteMO() . '</td>';
				}
				
				$tabla .= '<td>' . $renglon->getDetalle() . '</td>';
				
				$tabla .= '</tr>';
			}
			
			$tabla .= '</table>';
			
			$totalDebe = $this->getTotalDebe();
			$totalHaber = $this->getTotalHaber();
			$tabla2 = 'Total DEBE: ' . $totalDebe . '<br/>'; 
			$tabla2 .= 'Total HABER: ' . $totalHaber . '<br/>';
			$tabla2 .= 'Diferencia: ' . ($totalDebe - $totalHaber) . '<br/>';
			
			echo($tabla);
			die($tabla2);
		}
	}

}
