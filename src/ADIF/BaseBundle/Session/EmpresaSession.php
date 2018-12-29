<?php

namespace ADIF\BaseBundle\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use ADIF\AutenticacionBundle\Entity\Empresa;

/**
* Guardo la datos de la session de cada empresa 
*
* @author Gustavo Luis
*/
class EmpresaSession extends Session
{
	private static $instance = null;
	
	private $empresasHabilitadas = array('adif', 'fase', 'vossloh');
	
	/**
	* Devuelve la instancia de la sesion de la empresa cargada
	* @return EmpresaSession
	*/
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	* Devuelve el manager para conectarse a la base 
	* @return string
	*/
	public static function getAutenticacionInstance()
	{
		$session = self::getInstance();
		if ($session->getEmpresa() != null) {
			$session->load();
		} else {
			$session->set('emAutenticacion', 'siga_autenticacion');
		}
		
		
		return $session->get('emAutenticacion');
	}
	
	/**
	* Devuelve el manager para conectarse a la base 
	* @return string
	*/
	public static function getContableInstance()
	{
		$session = self::getInstance();
        
		if ($session->getEmpresa() != null) {
			$session->load();
		} else {
			$session->set('emContable', 'adif_contable');
		}
		
		return $session->get('emContable');
	}
	
	/**
	* Devuelve el manager para conectarse a la base 
	* @return string
	*/
	public static function getComprasInstance()
	{
		$session = self::getInstance();
		if ($session->getEmpresa() != null) {
			$session->load();
		} else {
			$session->set('emCompras', 'adif_compras');
		}
		

		return $session->get('emCompras');
	}
	
	/**
	* Devuelve el manager para conectarse a la base 
	* @return string
	*/
	public static function getRrhhInstance()
	{
		$session = self::getInstance();
		if ($session->getEmpresa() != null) {
			$session->load();
		} else {
			$session->set('emRrhh', 'adif_rrhh');
		}
		
		
		return $session->get('emRrhh');
	}
	
	/**
	* Devuelve el manager para conectarse a la base 
	* @return string
	*/
	public static function getWarehouseInstance()
	{
		$session = self::getInstance();
		if ($session->getEmpresa() != null) {
			$session->load();
		} else {
			$session->set('emWarehouse,', 'adif_warehouse');
		}
		
		return $session->get('emWarehouse');
	}
	
	/**
	* Carga los managers para cada base, depende que empresa se haya elegido
	*/
	public function load()
	{
		$session = self::getInstance();
		
		if ($session->getEmpresa() != null) {
			
			$empresa = $session->getEmpresa();
			
			$session->set('emAutenticacion', 'siga_autenticacion');
			$session->set('emCompras', $empresa . '_compras');
			$session->set('emContable', $empresa . '_contable');
			$session->set('emRrhh', $empresa . '_rrhh');
			if ($empresa == 'adif') {
				$session->set('emWarehouse', $empresa . '_warehouse');
			}
		}
	}
	
	/**
	* @param string $empresa
	* @return EmpresaSession
	*/
	public function setEmpresa($empresa) 
	{
		$session = self::getInstance();
		if (in_array($empresa, $this->empresasHabilitadas)) {
			$session->set('empresa', $empresa);
		} 
		
		return $this;
	}
	/**
	* @param Empresa $empresa
	* @return EmpresaSession
	*/
	public function setEmpresaEntity(Empresa $empresa)
	{
		$session = self::getInstance();
		$session->set('empresaEntity', $empresa);
		
		return $this;
	}
	
	/**
	* @param int $idEmpresa
	* @return EmpresaSession
	*/
	public function setIdEmpresa($idEmpresa) 
	{
		$session = self::getInstance();
		$session->set('idEmpresa', $idEmpresa);
		
		return $this;
	}
	
	/**
	* @return string
	*/
	public function getEmpresa()
	{
		$session = self::getInstance();
		$empresa = $session->get('empresa');
		return ($empresa != null) ? $empresa : 'adif';
	}
	
	/**
	* @return int
	*/
	public function getIdEmpresa()
	{
		$session = self::getInstance();
		$empresa = $session->get('idEmpresa');
		return ($empresa != null) ? $empresa : 2;
	}
	
	public function getEmpresaEntity()
	{
		$session = self::getInstance();
		return $session->get('empresaEntity');
	}
}