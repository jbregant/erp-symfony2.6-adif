<?php

/*
 * Customizacion para logeo de usuarios al sistema 
 *
 * @author Gustavo Luis
 *
 */

namespace ADIF\AutenticacionBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use ADIF\BaseBundle\Entity\EntityManagers;


class AutenticacionListener
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	private $router;

    public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Container $container, Router $router)
    {
        $this->securityContext = $securityContext;
		//$this->em = $doctrine->getEntityManager();
        $this->em = $doctrine->getManager(EntityManagers::getEmAutenticacion());
		$this->container = $container;
		$this->router = $router;
    }

	/**
	 * 
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			
			$request = $this->container->get('request');
			
			$empresa = $request->get('empresa');
			
			$this->container->get('adif.multiempresa_service')->cargarEmpresa($empresa);
			
			$user = $event->getAuthenticationToken()->getUser();
			
			$roles = $this->em->getRepository('ADIFAutenticacionBundle:Usuario')
				->getRolesByIdUsuarioAndIdEmpresa($user->getId(), $empresa);	
			
			$customRoles = array();
			foreach($roles as $rol) {
				$arrayRoles = unserialize($rol['roles']);
				foreach($arrayRoles as $role) {
					$customRoles[] = $role;
				}
			}
			
			if (empty($customRoles)) {
				// Si no tiene rol, le seteo por default algun rol
				$customRoles[] = 'ROLE_DEMO';
			} elseif(!in_array('ROLE_DEMO', $customRoles)) {
				// Si dentro de los roles traidos, no esta ROLE_DEMO,
				// se lo agrego, sino tira error de Access Denied
				$customRoles[] = 'ROLE_DEMO';
			}
			
			$token = new UsernamePasswordToken(
				$user,
				$user->getPassword(),
				'main',
				$customRoles
			);
			
			$this->container->get('security.context')->setToken($token);
			
		}
	}
}
