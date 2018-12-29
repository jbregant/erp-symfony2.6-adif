<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * BaseController
 *
 * @author Gustavo Luis
 */
class BaseController extends GeneralController 
{
	private $emDefault = 'adif_contable';
	
    /**
     * 
     * @return string
     */
    public function getEntityManager() 
	{
        $em = EntityManagers::getEmContable();
		return ($em != null) ? $em : $this->emDefault;
    }
}
