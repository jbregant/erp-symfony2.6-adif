<?php

namespace ADIF\AutenticacionBundle\Handler;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\EjercicioContable;
use DateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use ADIF\BaseBundle\Controller;

/**
 * 
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface {

    /**
     *
     * @var type 
     */
    protected $router;

    /**
     *
     * @var type 
     */
    protected $security;

    /**
     *
     * @var type 
     */
    protected $container;

    /**
     * 
     * @param Router $router
     * @param SecurityContext $security
     * @param ContainerInterface $container
     */
    public function __construct(Router $router, SecurityContext $security, ContainerInterface $container) {

        $this->router = $router;

        $this->security = $security;

        $this->container = $container;
    }

    /**
     * 
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) 
	{		
        $this->actualizarEjercicioContableUsuario();

		return new RedirectResponse($this->router->generate('siga_home'));
    }

    /**
     * 
     */
    private function actualizarEjercicioContableUsuario() {

        $em = $this->container->get('doctrine')
                ->getManager(EntityManagers::getEmContable());

        $ejerciciosContables = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->findAll();

        $ejercicioContableEnCurso = (new \DateTime())->format('Y');

        $ejerciciosContablesSesion = array();

        foreach ($ejerciciosContables as $ejercicioContable) {

            /* @var $ejercicioContable EjercicioContable */

            $denominacionEjercicio = $ejercicioContable->getDenominacionEjercicio();

            $ejerciciosContablesSesion[] = $denominacionEjercicio;
        }

        $this->container->get('session')
                ->set('ejercicio_contable', $ejercicioContableEnCurso);

        $this->container->get('session')
                ->set('ejercicios_contables', $ejerciciosContablesSesion);
    }

}
