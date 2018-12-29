<?php

namespace ADIF\AutenticacionBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * Description of BaseController
 *
 * @author Gustavo Luis
 */
class BaseController extends GeneralController 
{
	private $emDefault = 'adif_autenticacion';
	
    /**
     * 
     * @return string
     */
    public function getEntityManager() 
	{
        $em = EntityManagers::getEmAutenticacion();
		return ($em != null) ? $em : $this->emDefault;
    }
}
