<?php

/**
* Implementacion de Voters para el bundle de autenticacion 
*
* @author Gustavo Luis
* 
*/

namespace ADIF\AutenticacionBundle\Security;
 
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
 

class AutenticacionVoter extends AbstractVoter
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $container;
	
    const CREATE = 'create';
    const EDIT   = 'edit';
	const DELETE   = 'delete';
 
	public function __construct(ContainerInterface $container) 
	{
        $this->container = $container;
    }
 
    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::EDIT, self::DELETE);
    }
 
    protected function getSupportedClasses()
    {
        return array(
			'ADIF\AutenticacionBundle\Entity\Usuario',
			'ADIF\AutenticacionBundle\Entity\Grupo'
		);
    }
 
    protected function isGranted($attribute, $object, $user = null)
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
		
        if ($attribute == self::CREATE && !in_array('ROLE_SOLO_LECTURA', $user->getRoles())) {
			
			if (in_array('ROLE_MENU_SEGURIDAD', $user->getRoles())) {
				return true;
			}
			
        }
 
        if ($attribute == self::EDIT && !in_array('ROLE_SOLO_LECTURA', $user->getRoles())) {
            
			if (in_array('ROLE_MENU_SEGURIDAD', $user->getRoles())) {
				return true;
			}
			
        }
		
		if ($attribute == self::DELETE && !in_array('ROLE_SOLO_LECTURA', $user->getRoles())) {
			
			if (in_array('ROLE_MENU_SEGURIDAD', $user->getRoles())) {
				return true;
			}
			
        }
		
		
 
        return false;
    }
}

