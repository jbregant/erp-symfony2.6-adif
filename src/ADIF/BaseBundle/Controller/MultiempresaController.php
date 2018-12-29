<?php

namespace ADIF\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\GeneralController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use ADIF\BaseBundle\Session\EmpresaSession;
use ADIF\BaseBundle\Entity\EntityManagers;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * author Gustavo Luis
 * date 15/08/2017
 * @Route("/multiempresa")
 */
class MultiempresaController extends BaseController
{
	/**
	 * Cambia de empresa
     * @Route("/cambiar/", name="multiempresa_cambiar")
     * @Method("POST|GET")     
     */
	public function cambiarEmpresaAction(Request $request)
	{
		try {
			
			$idEmpresa = $request->get('idEmpresa');
			
			if ($idEmpresa == $this->get('adif.multiempresa_service')->getIdEmpresaActual()) {
				return new JsonResponse(
					array(
						'status' => 'nok', 
						'mensaje' => 'Ya se encuentra en la empresa a la que quiere cambiar.'
					)
				);
			}
			
			$user = $this->get('security.context')->getToken()->getUser();
			
			$this->get('adif.multiempresa_service')->cargarEmpresa($idEmpresa);
					
			$em = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
					
			$roles = $em->getRepository('ADIFAutenticacionBundle:Usuario')
				->getRolesByIdUsuarioAndIdEmpresa($user->getId(), $idEmpresa);	
			
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
			
			$this->get('security.context')->setToken($token);
			
			return new JsonResponse(
				array(
					'status' => 'ok', 
					'idUser' => $user->getId()
				)
			);
			
		} catch (\Exception $e) {
			
			return new JsonResponse(
				array(
					'status' => 'nok', 
					'idUser' => $user->getId(),
					'mensaje' => 'Ha ocurrido un error al cambiar de empresa',
					'debugMsg' => $e->getMessage(),
					'debugTrace' => $e->getTraceAsString()
				)
			);
			
		}
	}
}
