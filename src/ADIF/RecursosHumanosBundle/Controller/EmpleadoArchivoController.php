<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoArchivo;
use ADIF\RecursosHumanosBundle\Form\EmpleadoArchivoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * EmpleadoArchivo controller.
 *
 * @Route("/empleados/{idEmpleado}/archivos")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class EmpleadoArchivoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Empleados' => $this->generateUrl('empleados'),
        );
    }

    /**
     * Lists all EmpleadoArchivo entities.
     *
     * @Route("/", name="archivosempleado")
     * @Template()
     */
    public function indexAction($idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $archivos = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->findByIdEmpleado($idEmpleado);

        $bread = $this->base_breadcrumbs;

        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = null;

        return array(
            'empleado' => $empleado,
            'archivos' => $archivos,
            'breadcrumbs' => $bread,
            'page_title' => 'Archivos del empleado',
            'page_info' => 'Lista de archivos'
        );
    }

    /**
     * Creates a new EmpleadoArchivo entity.
     *
     * @Route("/insertar", name="archivosempleado_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoArchivo:new.html.twig")
     */
    public function createAction(Request $request, $idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $empleadoArchivo = new EmpleadoArchivo();
        $form = $this->createCreateForm($empleadoArchivo, $idEmpleado);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $empleadoArchivo->setIdEmpleado($empleado);
            $empleadoArchivo->upload();
            $em->persist($empleadoArchivo);
            $em->flush();

            return $this->redirect($this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado)));
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = $this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado));
        $bread['Crear'] = null;

        return array(
            'empleado' => $empleado,
            'entity' => $empleadoArchivo,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Adjuntar archivos',
        );
    }

    /**
     * Creates a form to create a EmpleadoArchivo entity.
     *
     * @param EmpleadoArchivo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EmpleadoArchivo $entity, $idEmpleado) {
        $form = $this->createForm(new EmpleadoArchivoType(), $entity, array(
            'action' => $this->generateUrl('archivosempleado_create', array('idEmpleado' => $idEmpleado)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EmpleadoArchivo entity.
     *
     * @Route("/crear", name="archivosempleado_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $entity = new EmpleadoArchivo();
        $form = $this->createCreateForm($entity, $idEmpleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = $this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado));
        $bread['Crear'] = null;


        return array(
            'empleado' => $empleado,
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Adjuntar archivos'
        );
    }

    /**
     * Finds and displays a EmpleadoArchivo entity.
     *
     * @Route("/show/{id}", name="archivosempleado_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($idEmpleado, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);
        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoArchivo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = $this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado));
        $bread['Archivo'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver archivo'
        );
    }

    /**
     * Displays a form to edit an existing EmpleadoArchivo entity.
     *
     * @Route("/editar/{id}", name="archivosempleado_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoArchivo:new.html.twig")
     */
    public function editAction($idEmpleado, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);
        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoArchivo.');
        }

        $editForm = $this->createEditForm($entity, $idEmpleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = $this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado));
        $bread['Editar'] = null;

        return array(
            'empleado' => $empleado,
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar archivos'
        );
    }

    /**
     * Creates a form to edit a EmpleadoArchivo entity.
     *
     * @param EmpleadoArchivo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EmpleadoArchivo $entity, $idEmpleado) {
        $form = $this->createForm(new EmpleadoArchivoType(), $entity, array(
            'action' => $this->generateUrl('archivosempleado_update', array('id' => $entity->getId(), 'idEmpleado' => $idEmpleado)),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EmpleadoArchivo entity.
     *
     * @Route("/actualizar/{id}", name="archivosempleado_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoArchivo:new.html.twig")
     */
    public function updateAction(Request $request, $id, $idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $empleadoArchivo = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->find($id);

        if (!$empleadoArchivo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoArchivo.');
        }

        $editForm = $this->createEditForm($empleadoArchivo, $idEmpleado);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $empleadoArchivo->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado)));
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $idEmpleado));
        $bread['Archivos'] = $this->generateUrl('archivosempleado', array('idEmpleado' => $idEmpleado));
        $bread['Editar'] = null;

        return array(
            'empleado' => $empleado,
            'entity' => $empleadoArchivo,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar archivos'
        );
    }

    /**
     * Deletes a EmpleadoArchivo entity.
     *
     * @Route("/borrar/{id}", name="archivosempleado_delete")
     * @Method("DELETE")
     */
    public function deleteAction($idEmpleado, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoArchivo.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('archivosempleado'));
    }

    /**
     * Download 
     *
     * @Route("/download/{id}", name="archivosempleado_download")
     * @Method("GET")
     */
    public function downloadAction($idEmpleado, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoArchivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoArchivo.');
        }

        $this->redirect($entity->getAbsolutePath());
        return sfView::NONE;
//        header("Location: ".);
    }

    /**
     * Upload 
     *
     * @Route("/upload", name="archivosempleado_upload")
     * @Method("POST")
     */
    public function uploadAction($idEmpleado) {

//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $request = Request::createFromGlobals();
//        
//        if (null === $request->files->get('file', null)) {
//            return;
//        }
//        
//        $file = $request->files->get('file');
//        
//        $filename = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
////        $file->move(__DIR__ . '/../../../../web/uploads/empleados/archivos', $filename);
//        $file->move($this->container->getParameter('empleados_upload_path_archivos').$idEmpleado.'/', $filename);
//
//        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);
//        
//        $empleadoArchivo = new EmpleadoArchivo();
//        $empleadoArchivo->setArchivo($filename);
//        $empleadoArchivo->setNombre($file->getClientOriginalName());
//        $empleadoArchivo->setDescripcion($file->getClientOriginalName());
//        $empleadoArchivo->setIdEmpleado($empleado);
//        
////        $archivos = $empleado->getArchivos();
////        $archivos->add($empleadoArchivo);
////        
//        $em->persist($empleadoArchivo);
//        
//        $em->flush();
//        
//        return new Response('OK');
    }

}
