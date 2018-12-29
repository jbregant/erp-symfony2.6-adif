<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * BaseController
 *
 * @author Carlos Sanchez
 */
class BaseController extends GeneralController
{
	private $emDefault = 'adif_proveedores';

    /**
     *
     * @return string
     */
    public function getEntityManager()
	{
        return $this->emDefault;
    }
}
