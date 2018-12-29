<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class DefaultController extends BaseController implements AlertControllerInterface {
    
    public function indexAction() {
        return;
    }

    
}
