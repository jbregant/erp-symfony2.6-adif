<?php

namespace ADIF\AutenticacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 *
 * @author Gustavo Luis
 * 
 * @Route("/home")
 */
class DefaultController extends Controller
{
	/**
     * Landing page del siga.
     *
     * @Route("/", name="siga_home")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('ADIFAutenticacionBundle:Default:index.html.twig');
    }
}
