<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;

class DefaultController extends BaseController
{
    public function indexAction($name)
    {
        return $this->render('ADIFRecursosHumanosBundle:Default:index.html.twig', array('name' => $name));
    }
}
