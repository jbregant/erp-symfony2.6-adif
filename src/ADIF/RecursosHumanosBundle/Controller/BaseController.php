<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * Description of BaseController
 *
 * @author Gustavo Luis
 */
class BaseController extends GeneralController 
{
	private $emDefault = 'adif_rrhh';
	
    /**
     * 
     * @return string
     */
    public function getEntityManager() 
	{
        $em = EntityManagers::getEmRrhh();
		return ($em != null) ? $em : $this->emDefault;
    }
}
