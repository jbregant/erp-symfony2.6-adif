<?php

namespace ADIF\AutenticacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\AutenticacionBundle\Entity\Usuario;
use ADIF\AutenticacionBundle\Form\UsuarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Usuario controller.
 *
 * @Route("/usuarios")
 * @Security("has_role('ROLE_MENU_SEGURIDAD')")
 */
class UsuarioController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
	{
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Usuarios' => $this->generateUrl('usuarios_index')
        );
    }

    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="usuarios_index")
     * @Method("GET")
     * @Template("ADIFAutenticacionBundle:Usuario:index.html.twig")
     */
    public function indexAction()
	{
        $bread = $this->base_breadcrumbs;

        return array(
            'breadcrumbs' => $bread,
        );
    }

    /**
     * Tabla para Usuario.
     *
     * @Route("/index_table/", name="usuarios_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
	{
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFAutenticacionBundle:Usuario')->findAll();

        return $this->render('ADIFAutenticacionBundle:Usuario:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}", name="usuarios_show")
     * @Method("GET")
     * @Template("ADIFAutenticacionBundle:Usuario:show.html.twig")
     */
    public function showAction($id)
	{
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFAutenticacionBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Usuario.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Usuario'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
			'idUsuario' => $entity->getId()
        );
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/editar/{id}", name="usuarios_edit")
     * @Method("GET")
     * @Template("ADIFAutenticacionBundle:Usuario:edit.html.twig")
     */

    public function editAction($id) {
		
        $user = $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFAutenticacionBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Usuario.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar un usuario'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
        );
    }

    /**
     * Creates a form to edit a Usuario entity.
     *
     * @param Usuario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Usuario $entity)
	{
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('usuarios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }

    /**
     * Edits an existing Usuario entity.
     *
     * @Route("/actualizar/{id}", name="usuarios_update")
     * @Method("PUT")
     * @Template("ADIFAutenticacionBundle:Usuario:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
	{
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		$emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = $em->getRepository('ADIFAutenticacionBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Usuario.');
        }

		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		try {

			if ($editForm->isValid()) {

				$em->getRepository('ADIFAutenticacionBundle:Usuario')
					->updateUsuario($request, $id);

				$idEmpresa = $request->get('adif_autenticacionbundle_usuario')['empresas'];
				$idsGrupos = $request->get('adif_autenticacionbundle_usuario')['groups'];

				$em->getRepository('ADIFAutenticacionBundle:Usuario')
					->addGruposByIdUsuarioAndIdEmpresa($id, $idEmpresa, $idsGrupos);

				$this->addSuccessFlash('Se modifico el usuario con exito.');

				return $this->redirect($this->generateUrl('usuarios_index'));

			} else {

				$this->addErrorFlash('Hubo un error al modificar el usuario.');
				return $this->redirect($this->generateUrl('usuarios_edit', array('id' => $id)));

			}

		} catch(\Exception $e) {
			$this->addErrorFlash('Hubo un error al modificar el usuario.' . $e->getMessage());
			return $this->redirect($this->generateUrl('usuarios_edit', array('id' => $id)));
		}

        $bread = $this->base_breadcrumbs;
        $bread['Editar un usuario'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
        );
    }

    /**
     * Limpia la contraseña de un usuario a su default.
     *
     * @Route("/limpiar/{id}", name="usuarios_limpiar")
     * @Method("GET")
     */
    public function limpiarPasswordAction($id)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
        $userManager = $this->get('fos_user.user_manager');

        $entity = $em->getRepository('ADIFAutenticacionBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Usuario.');
        }

        $entity->setPlainPassword($entity->getUsername());

        $userManager->updateUser($entity);

        return $this->redirect($this->generateUrl('usuarios_index'));
    }

//		$userManager->updateUser($entity);
//
//		return $this->redirect($this->generateUrl('usuarios_index'));
//
//    }
}
