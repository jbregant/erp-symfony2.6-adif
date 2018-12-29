<?php

namespace ADIF\WarehouseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ADIFWarehouseBundle:Default:index.html.twig', array('name' => $name));
    }
}
