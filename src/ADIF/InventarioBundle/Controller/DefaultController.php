<?php

namespace ADIF\InventarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ADIFInventarioBundle:Default:index.html.twig', array('name' => $name));
    }
}
