<?php

namespace ADIF\AutenticacionBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as TheBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;

/**
 * RegistrationController.
 * @Security("has_role('ROLE_MENU_SEGURIDAD')")
 */
class RegistrationController extends TheBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Usuarios' => $this->container->get('router')->generate('usuarios_index'),
        );
    }

    public function registerAction(Request $request) 
	{
		$em = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
		
		$idEmpresa = $request->get('fos_user_registration_form')['empresas'];
		$idsGrupos = $request->get('fos_user_registration_form')['groups'];
		
        $user = $userManager->createUser();
        //si se quiere dejar al usuario deshabilitado se debe poner en false
        $user->setEnabled(true);
		
		/** Grupos y empresas **/
		$idsGrupos = ($idsGrupos != null) ? $idsGrupos : array();
		foreach($idsGrupos as $idGrupo) {
			$grupo = $em->getRepository('ADIFAutenticacionBundle:Grupo')->find($idGrupo);
			if ($grupo) {
				$user->addGroup($grupo);
			}
		}
        
		$idEmpresa = ($idEmpresa != null) ? $idEmpresa : 0;
		$empresa = $em->getRepository('ADIFAutenticacionBundle:Empresa')->find($idEmpresa);
		if ($empresa) {
			$user->addEmpresa($empresa);
		}
		/** Fin Grupos y empresas **/
		
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
		
        if ('POST' === $request->getMethod()) {
			
            $form->bind($request);
			
            if ($form->isValid()) {
						
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
				
                $userManager->updateUser($user);
				
				// Work-around para que el fos-user no cree cualquier cosa - gluis - 18/08/2017
				$em->getRepository('ADIFAutenticacionBundle:Usuario')
					->addGruposByIdUsuarioAndIdEmpresaOnCreate($user->getId(), $idEmpresa, $idsGrupos);
				
                //si el usuario esta asociado a una cuenta de active directory
                if ($form['usuario_ad']->getData() === 'true') {
                    $user->setPassword('');
                    $userManager->updateUser($user);
                }
				
				$this->addFlash('success', 'Se ha creado el usuario con exito.');
				
                $url = $this->container->get('router')->generate('usuarios_index');
                $response = new RedirectResponse($url);
				
                return $response;
            } else {
				$this->addFlash('error', 'Hubo un error al crear el usuario.');
			}
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear un usuario'] = null;

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.twig', array(
                    'form' => $form->createView(),
                    'breadcrumbs' => $bread,
                    'active_directory' => $this->getAD(),
        ));
    }

    private function getAD() {
//        // LDAP variables
//        $ldaphost = $this->container->getParameter('ldap_host');  // your ldap servers
//        $ldaprdn = $this->container->getParameter('ldap_username');     // ldap rdn or dn
//        $ldappass = $this->container->getParameter('ldap_password');  // associated password
//        // connect to ldap server
//        $ldapconn = ldap_connect($ldaphost) or die("Could not connect to LDAP server.");

        return array();

//        if ($ldapconn) {
//
//            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
//            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
//
//            // binding to ldap server
//            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
//
//            // verify binding
//            if ($ldapbind) {
//                //echo "LDAP bind successful...";
//
//                $attributes = array();
//                $attributes[] = 'givenname';
//                $attributes[] = 'mail';
//                $attributes[] = 'samaccountname';
//                $attributes[] = 'sn';
//
//                $sr = ldap_search($ldapconn, "DC=ueppfe,DC=local", "(&(objectClass=Person)(sn=*)(givenname=*)(mail=*))", $attributes);
//                if ($sr !== FALSE) {
//                    $entries = ldap_get_entries($ldapconn, $sr);
//                }
//            } else {
//                echo "LDAP bind failed...";
//            }
//            ldap_unbind($ldapconn);
//
//            return $entries;
//        }
    }

}
