<?php

namespace ADIF\BaseBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use ADIF\BaseBundle\Session\EmpresaSession;
use ADIF\BaseBundle\Entity\EntityManagers;

/**
 * Servicio de cambio de empresa
 *
 * @author Gustavo Luis
 */
class MultiEmpresaService
{
	const ADIFSE = 1;
	const FASE = 2;
	const CONSORCIO_VOSSLOH = 3;
	
	/**
	* @var Symfony\Component\DependencyInjection\ContainerInterface
	*/
	private $container;
	
	private $em;
	
	/**
	* @var ADIF\AutenticacionBundle\Entity\Empresa
	*/
	private $empresa;
	
	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
	{
		$this->container = $container;
		$this->em = $this->container->get('doctrine')->getManager(EntityManagers::getEmAutenticacion());
	}
	
	/**
	* Levanta la empresa 
	* @param int $empresa
	*/
	public function cargarEmpresa($empresa) 
	{
		$session = EmpresaSession::getInstance();
		$session->setIdEmpresa($empresa);
		
		switch($empresa) {
			
			case self::ADIFSE:
				$this->setEmpresa('adif');
				break;
			case self::FASE:
				$this->setEmpresa('fase');
				break;
			case self::CONSORCIO_VOSSLOH:				
				$this->setEmpresa('vossloh');
				break;
		}
		
		$session->load();
	}
	
	/**
	* @param string $empresa 
	* @return MultiEmpresaService
	*/
	public function setEmpresa($empresa)
	{
		$session = EmpresaSession::getInstance();
		$session->setEmpresa($empresa);
		
		$empresa = $this->em->getRepository('ADIFAutenticacionBundle:Empresa')
			->find($this->getIdEmpresaActual());
		
		//var_dump($this->getIdEmpresaActual());
		//echo "<br>----------<br>";
		//\Doctrine\Common\Util\Debug::dump( $empresa->getWebServices() ); exit;
		
		if ($empresa) {
			$session->setEmpresaEntity($empresa);
		}
		
		return $this;
	}
	
	/**
	* @param bool $reload
	* @return ADIF\AutenticacionBundle\Entity\Empresa
	*/
	public function getEmpresa($reload = false)
	{
		$session = EmpresaSession::getInstance();
		
		if ($reload) {
			$idEmpresa = $session->getIdEmpresa();
			return $this->em->getRepository('ADIFAutenticacionBundle:Empresa')->find($idEmpresa);
		} else {
			return $session->getEmpresaEntity();
		}
	}
	
	/**
	* @return string
	*/
	public function getEmpresaActual()
	{
		$session = EmpresaSession::getInstance();
		
		return $session->getEmpresa();
	}
	
	/**
	* @return int
	*/
	public function getIdEmpresaActual()
	{
		$session = EmpresaSession::getInstance();
		
		return $session->getIdEmpresa();
	}
	
	/**
	* @return string
	*/
	public function getOrganizationName()
	{
		$session = EmpresaSession::getInstance();
		
		$idEmpresa = $session->getIdEmpresa();
		
		$empresa = $this->em->getRepository('ADIFAutenticacionBundle:Empresa')->find($idEmpresa);
		
		return $empresa->getDenominacionLarga();
	}
	
	/**
	* @return array<ADIF\AutenticacionBundle\Entity\Empresa>
	*/
	public function getDatosTodasEmpresas()
	{
		return $this->em->getRepository('ADIFAutenticacionBundle:Empresa')->findAll();
	}
    
    /**
     * Me devuelve los grupos filtrado por idEmpresa y idUsuario
     * @param int $idEmpresa
     * @param int $idUsuario
     * @return array
     */
    public function getGruposPorEmpresaYUsuario($idEmpresa, $idUsuario)
    {
        $arrayGrupos = array();
        $gruposUsuarioEmpresa = $this->em->getRepository('ADIFAutenticacionBundle:Usuario')
                 ->getGruposByIdUsuarioAndIdEmpresa($idUsuario, $idEmpresa);
         
         foreach($gruposUsuarioEmpresa as $grupoUE) {
             $arrayGrupos[] = $grupoUE['grupo'];
         }
         
         return $arrayGrupos;
    }
}
