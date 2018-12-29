<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Controller\GeneralController;

/**
 * BaseController
 *
 * @author Carlos Sanchez
 */
class BaseController extends GeneralController
{
	private $emDefault = 'adif_inventario';

    /**
     *
     * @return string
     */
    public function getEntityManager()
	{
        return $this->emDefault;
    }
}
