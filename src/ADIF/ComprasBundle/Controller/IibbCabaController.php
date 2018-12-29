<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\IibbCaba;
use ADIF\ComprasBundle\Form\IibbCabaType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * IibbCaba controller.
 *
 * @Route("/iibb_caba")
  */
class IibbCabaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'IibbCaba' => $this->generateUrl('iibb_caba')
        );
    }
    /**
     * Lists all IibbCaba entities.
     *
     * @Route("/", name="iibb_caba")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
		$esProveedor = $request->get('esProveedor');
        $bread = $this->base_breadcrumbs;
        $bread['IibbCaba'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'IibbCaba',
            'page_info' => 'Lista de iibbcaba',
			'esProveedor' => $esProveedor
        );
    }

    /**
     * Tabla para IibbCaba .
     *
     * @Route("/index_table/", name="iibb_caba_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request)
    {
		$esProveedor = $request->get('esProveedor');
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:IibbCaba')->findBy(
			array('esProveedor' => $esProveedor),
			array('grupo' => 'ASC')
		);
        
        $bread = $this->base_breadcrumbs;
        $bread['IibbCaba'] = null;

		return $this->render('ADIFComprasBundle:IibbCaba:index_table.html.twig', array(
			'entities' => $entities
			)
        );
    }
    /**
     * Creates a new IibbCaba entity.
     *
     * @Route("/insertar", name="iibb_caba_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:IibbCaba:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new IibbCaba();
		$esProveedor = $request->get('esProveedor');
        $form = $this->createCreateForm($entity, $esProveedor);
        $form->handleRequest($request);
		
		$entity->setEsProveedor($esProveedor);
		
        if ($form->isValid()) {
			
			$grupo = $entity->getGrupo();
			$padron = !empty($esProveedor) ? 'proveedores' : 'clientes';
			
			if ( $this->validarGrupo($esProveedor, $grupo) ) {
			
				$em = $this->getDoctrine()->getManager($this->getEntityManager());
				$em->persist($entity);
			
				try {
				
					$em->flush();
					$this->get('session')->getFlashBag() 
							->add('success', "Se ha guardado correctamente el grupo $grupo.");
				
				} catch (Excepcion $e) {
					
					$this->get('session')->getFlashBag() 
							->add('error', "Ha ocurrido un error al guardar el padr&oacute;n de $padron.");
				}

			} else {
				// Existe un grupo para el cliente/proveedor
				$this->get('session')->getFlashBag() 
							->add('error', "Ya existe el grupo $grupo para el padr&oacute;n de $padron.");
			}
			
			return $this->redirect($this->generateUrl('iibb_caba', array('esProveedor' => $esProveedor)));
			
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear IibbCaba',
			'esProveedor' => $esProveedor
        );
    }

    /**
    * Creates a form to create a IibbCaba entity.
    *
    * @param IibbCaba $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(IibbCaba $entity, $esProveedor)
    {
        $form = $this->createForm(new IibbCabaType(), $entity, array(
            'action' => $this->generateUrl('iibb_caba_create', 
				array('esProveedor' => $esProveedor)
			),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new IibbCaba entity.
     *
     * @Route("/crear", name="iibb_caba_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
		$esProveedor = $request->get('esProveedor');
        $entity = new IibbCaba();
        $form   = $this->createCreateForm($entity, $esProveedor);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear IibbCaba',
			'esProveedor' => $esProveedor
        );
}

    /**
     * Finds and displays a IibbCaba entity.
     *
     * @Route("/{id}", name="iibb_caba_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:IibbCaba')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad IibbCaba.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['IibbCaba'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver IibbCaba'
        );
    }

    /**
     * Displays a form to edit an existing IibbCaba entity.
     *
     * @Route("/editar/{id}", name="iibb_caba_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:IibbCaba:new.html.twig")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$esProveedor = $request->get('esProveedor');

        $entity = $em->getRepository('ADIFComprasBundle:IibbCaba')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad IibbCaba.');
        }

        $editForm = $this->createEditForm($entity, $esProveedor);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar IibbCaba',
			'esProveedor' => $esProveedor
        );
    }

    /**
    * Creates a form to edit a IibbCaba entity.
    *
    * @param IibbCaba $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(IibbCaba $entity, $esProveedor)
    {
        $form = $this->createForm(new IibbCabaType(), $entity, array(
            'action' => $this->generateUrl('iibb_caba_update', 
				array(
					'id' => $entity->getId(),
					'esProveedor' => $esProveedor
				)
			),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing IibbCaba entity.
     *
     * @Route("/actualizar/{id}", name="iibb_caba_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:IibbCaba:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:IibbCaba')->find($id);
		$grupoOriginal = $entity->getGrupo();
		
		$esProveedor = $request->get('esProveedor');

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad IibbCaba.');
        }

        $editForm = $this->createEditForm($entity, $esProveedor);
        $editForm->handleRequest($request);
		
		$entity->setEsProveedor($esProveedor);

        if ($editForm->isValid()) {
			
			$grupo = $entity->getGrupo();
			$padron = !empty($esProveedor) ? 'proveedores' : 'clientes';
			
			if ( $this->validarGrupo($esProveedor, $grupo, $grupoOriginal) ) {
			
				try {
					
					$em->flush();
					$this->get('session')->getFlashBag() 
							->add('success', "Se ha editado correctamente el grupo $grupo.");
					
				} catch (Excepcion $e) {
					
					$this->get('session')->getFlashBag() 
							->add('error', "Ha ocurrido un error al guardar el padr&oacute;n de $padron.");
				}
				
			} else {
				// Existe un grupo para el cliente/proveedor
				$this->get('session')->getFlashBag() 
							->add('error', "Ya se existe el grupo $grupo para el padr&oacute;n de $padron.");
			}

            return $this->redirect($this->generateUrl('iibb_caba', array('esProveedor' => $esProveedor)));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar IibbCaba',
			'esProveedor' => $esProveedor
        );
    }
    /**
     * Deletes a IibbCaba entity.
     *
     * @Route("/borrar/{id}", name="iibb_caba_delete")
     * @Method("GET")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:IibbCaba')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad IibbCaba.');
        }

		// Antes de hacer el borrado logico, me fijo si no tiene alguna FK
		$esProveedor = $request->get('esProveedor');
		$cliProv = null;
		if ($esProveedor == 1) {
			$cliProv = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByIibbCaba($id);
		} else {
			$cliProv = $em->getRepository('ADIFComprasBundle:Cliente')->findOneByIibbCaba($id);
		}
		
		if ($cliProv != null) {
			// Encontro una FK
			$entityFk = !empty($esProveedor) ? 'proveedor' : 'cliente';
			$this->get('session')->getFlashBag()
						->add('error', "No se puede borrar el grupo, ya que esta asignado a un $entityFk ($cliProv)");
						
			return $this->redirect($this->generateUrl('iibb_caba', array('esProveedor' => $esProveedor)));
		}
		
		$entity->setFechaBaja(new \DateTime());
        $em->persist($entity);
        $em->flush();

		$this->get('session')->getFlashBag()
						->add('success', "Se ha borrado el grupo con &eacute;xito");
        return $this->redirect($this->generateUrl('iibb_caba', array('esProveedor' => $esProveedor)));
    }
	
	/**
	* Este metodo controla que sea unique el grupo + esProveedor + fechaBaja
	*/
	private function validarGrupo($esProveedor, $grupo, $grupoOriginal = null)
	{
		
		if ($grupoOriginal == $grupo) {
			// Esto quiere decir, que en el update, lo unico que se cambio es la alicuota
			return true;
		}
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$grupo = $em->getRepository('ADIFComprasBundle:IibbCaba')->findOneBy(
					array(
						'grupo' => $grupo,
						'esProveedor' => $esProveedor,
						'fechaBaja' => null
					)
				);
		
		return ($grupo) ? false : true;
	}
}
