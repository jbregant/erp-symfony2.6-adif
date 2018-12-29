<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * BaseController
 *
 * @author Gustavo Luis
 */
class BaseController extends GeneralController 
{
	private $emDefault = 'adif_compras';
	
    /**
     * 
     * @return string
     */
    public function getEntityManager() 
	{
        $em = EntityManagers::getEmCompras();
		return ($em != null) ? $em : $this->emDefault;
    }
}
