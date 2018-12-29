<?php

namespace ADIF\BaseBundle\Entity;

use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * EntityManagers
 *
 * @author Gustavo Luis
 */
class EntityManagers 
{
	
	public static function getEmAutenticacion()
	{
		return EmpresaSession::getAutenticacionInstance();
	}
	
	public static function getEmCompras()
	{
		return EmpresaSession::getComprasInstance();
	}
	
	public static function getEmContable()
	{
		return EmpresaSession::getContableInstance();
	}
	
	public static function getEmRrhh()
	{
		return EmpresaSession::getRrhhInstance();
	}
	
	public static function getEmWarehouse()
	{
		return EmpresaSession::getWarehouseInstance();
	}

}
