<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\BienEconomico;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoBienEconomico;
use ADIF\ComprasBundle\Form\BienEconomicoType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * BienEconomico controller.
 *
 * @Route("/bieneconomico")
 */
class BienEconomicoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Bienes econ&oacute;micos' => $this->generateUrl('bieneconomico')
        );
    }

    /**
     * Lists all BienEconomico entities.
     *
     * @Route("/", name="bieneconomico")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Bienes econ&oacute;micos'] = null;

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $impuestos = $emContable->getRepository('ADIFContableBundle:TipoImpuesto')->findAll();
        $regimenes = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->findAll();

        return array(
            'impuestos' => $this->getTipoImpuestos($impuestos),
            'regimenes' => $this->getRegimenesByImpuesto($impuestos, $regimenes),
            'breadcrumbs' => $bread,
            'page_title' => 'Bien econ&oacute;mico',
            'page_info' => 'Lista de bienes econ&oacute;micos'
        );
    }

    /**
     * Creates a new BienEconomico entity.
     *
     * @Route("/insertar", name="bieneconomico_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:BienEconomico:new.html.twig")
     */
    public function createAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $bienEconomico = new BienEconomico();

        $form = $this->createCreateForm($bienEconomico);
        $form->handleRequest($request);

        $bienEconomicoRequest = $request->request->get('adif_comprasbundle_bieneconomico');

        $idSUSS = $bienEconomicoRequest['regimenRetencionSUSS'];
        $idIVA = $bienEconomicoRequest['regimenRetencionIVA'];
        $idIIBB = $bienEconomicoRequest['regimenRetencionIIBB'];
        $idGanancias = $bienEconomicoRequest['regimenRetencionGanancias'];

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Si el usuario indicó todos los RegimenRetencion y la CuentaContable
            if ($idSUSS && $idIVA && $idIIBB && $idGanancias && $bienEconomico->getIdCuentaContable() != null) {

                // Obtengo el EstadoBienEconomico igual a "Activo"
                $estadoBienEconomico = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->
                        findOneBy(array('denominacionEstadoBienEconomico' => ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO), array('id' => 'desc'), 1, 0);
            } else {

                // Obtengo el EstadoBienEconomico igual a "Pendiente de carga"
                $estadoBienEconomico = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->
                        findOneBy(array('denominacionEstadoBienEconomico' => ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_PENDIENTE_CARGA), array('id' => 'desc'), 1, 0);
            }

            $bienEconomico->setEstadoBienEconomico($estadoBienEconomico);

            $em->persist($bienEconomico);

            $em->flush();

            //REGIMENES RETENCIONES

            if ($idSUSS) {
                $regimenSUSS = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idSUSS);
                $regimenBienSUSS = new RegimenRetencionBienEconomico();
                $regimenBienSUSS->setIdBienEconomico($bienEconomico->getId());
                $regimenBienSUSS->setRegimenRetencion($regimenSUSS);
                $regimenSUSS->addRegimenesRetencionBienEconomico($regimenBienSUSS);
            }

            if ($idIVA) {
                $regimenIVA = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idIVA);
                $regimenBienIVA = new RegimenRetencionBienEconomico();
                $regimenBienIVA->setIdBienEconomico($bienEconomico->getId());
                $regimenBienIVA->setRegimenRetencion($regimenIVA);
                $regimenIVA->addRegimenesRetencionBienEconomico($regimenBienIVA);
            }

            if ($idIIBB) {
                $regimenIIBB = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idIIBB);
                $regimenBienIIBB = new RegimenRetencionBienEconomico();
                $regimenBienIIBB->setIdBienEconomico($bienEconomico->getId());
                $regimenBienIIBB->setRegimenRetencion($regimenIIBB);
                $regimenIIBB->addRegimenesRetencionBienEconomico($regimenBienIIBB);
            }

            if ($idGanancias) {
                $regimenGanancias = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idGanancias);
                $regimenBienGanancias = new RegimenRetencionBienEconomico();
                $regimenBienGanancias->setIdBienEconomico($bienEconomico->getId());
                $regimenBienGanancias->setRegimenRetencion($regimenGanancias);
                $regimenGanancias->addRegimenesRetencionBienEconomico($regimenBienGanancias);
            }

            $emContable->flush();


            // Es un popup
            if (!empty($bienEconomicoRequest['submit']) && $bienEconomicoRequest['submit'] == 'popup') {

                return $this->render('::base_iframe.html.twig', array(
                            'response' => 'OK',
                            'response_id' => $bienEconomico->getId())
                );
            }
            // Si no, Redirijo al index
            else {
                return $this->redirect($this->generateUrl('bieneconomico'));
            }
        } //. 
        else {
            $request->attributes->set('form-error', true);

            // Es un popup
            if (!empty($bienEconomicoRequest['submit']) && $bienEconomicoRequest['submit'] == 'popup') {

                $request->attributes->set('popup', true);
            }
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $bienEconomico,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear bien econ&oacute;mico',
        );
    }

    /**
     * Creates a form to create a BienEconomico entity.
     *
     * @param BienEconomico $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BienEconomico $entity) {
        $form = $this->createForm(new BienEconomicoType(), $entity, array(
            'action' => $this->generateUrl('bieneconomico_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new BienEconomico entity.
     *
     * @Route("/crear", name="bieneconomico_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new BienEconomico();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear bien econ&oacute;mico'
        );
    }

    /**
     * Finds and displays a BienEconomico entity.
     *
     * @Route("/{id}", name="bieneconomico_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:BienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BienEconomico.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionBienEconomico()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver bien econ&oacute;mico'
        );
    }

    /**
     * Displays a form to edit an existing BienEconomico entity.
     *
     * @Route("/editar/{id}", name="bieneconomico_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:BienEconomico:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:BienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BienEconomico.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionBienEconomico()] = $this->generateUrl('bieneconomico_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $regimenes = [];
        $regimenSUSS = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                ->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::SUSS, $id);

        $regimenIVA = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                ->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::IVA, $id);

        $regimenIIBB = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                ->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::IIBB, $id);

        $regimenGanancias = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                ->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::Ganancias, $id);

        $regimenes[] = ($regimenSUSS != null) ? $regimenSUSS->getId() : null;
        $regimenes[] = ($regimenIVA != null) ? $regimenIVA->getId() : null;
        $regimenes[] = ($regimenIIBB != null) ? $regimenIIBB->getId() : null;
        $regimenes[] = ($regimenGanancias != null) ? $regimenGanancias->getId() : null;

        return array(
            'entity' => $entity,
            'regimenes' => $regimenes,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar bien econ&oacute;mico'
        );
    }

    /**
     * Creates a form to edit a BienEconomico entity.
     *
     * @param BienEconomico $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BienEconomico $entity) {
        $form = $this->createForm(new BienEconomicoType(), $entity, array(
            'action' => $this->generateUrl('bieneconomico_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing BienEconomico entity.
     *
     * @Route("/actualizar/{id}", name="bieneconomico_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:BienEconomico:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $bienEconomico = $em->getRepository('ADIFComprasBundle:BienEconomico')->find($id);

        if (!$bienEconomico) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BienEconomico.');
        }

        $editForm = $this->createEditForm($bienEconomico);
        $editForm->handleRequest($request);

        $bienEconomicoRequest = $request->request->get('adif_comprasbundle_bieneconomico');

        $idSUSS = $bienEconomicoRequest['regimenRetencionSUSS'];
        $idIVA = $bienEconomicoRequest['regimenRetencionIVA'];
        $idIIBB = $bienEconomicoRequest['regimenRetencionIIBB'];
        $idGanancias = $bienEconomicoRequest['regimenRetencionGanancias'];

        if ($editForm->isValid()) {

            // Si el usuario indicó todos los RegimenRetencion y la CuentaContable
            if ($idSUSS && $idIVA && $idIIBB && $idGanancias && $bienEconomico->getIdCuentaContable() != null) {

                // Obtengo el EstadoBienEconomico igual a "Activo"
                $estadoBienEconomico = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->
                        findOneBy(array('denominacionEstadoBienEconomico' => ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO), array('id' => 'desc'), 1, 0);
            } else {

                // Obtengo el EstadoBienEconomico igual a "Pendiente de carga"
                $estadoBienEconomico = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->
                        findOneBy(array('denominacionEstadoBienEconomico' => ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_PENDIENTE_CARGA), array('id' => 'desc'), 1, 0);
            }

            $bienEconomico->setEstadoBienEconomico($estadoBienEconomico);

            $em->flush();

            //REGIMENES RETENCIONES

            if ($idSUSS) {
                $regimenSUSS = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idSUSS);
                $regimenBienSUSS = new RegimenRetencionBienEconomico();
                $regimenBienSUSS->setIdBienEconomico($id);
                $regimenBienSUSS->setRegimenRetencion($regimenSUSS);
                $regimenSUSS->addRegimenesRetencionBienEconomico($regimenBienSUSS);
            }

            if ($idIVA) {
                $regimenIVA = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idIVA);
                $regimenBienIVA = new RegimenRetencionBienEconomico();
                $regimenBienIVA->setIdBienEconomico($id);
                $regimenBienIVA->setRegimenRetencion($regimenIVA);
                $regimenIVA->addRegimenesRetencionBienEconomico($regimenBienIVA);
            }

            if ($idIIBB) {
                $regimenIIBB = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idIIBB);
                $regimenBienIIBB = new RegimenRetencionBienEconomico();
                $regimenBienIIBB->setIdBienEconomico($id);
                $regimenBienIIBB->setRegimenRetencion($regimenIIBB);
                $regimenIIBB->addRegimenesRetencionBienEconomico($regimenBienIIBB);
            }

            if ($idGanancias) {
                $regimenGanancias = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')->find($idGanancias);
                $regimenBienGanancias = new RegimenRetencionBienEconomico();
                $regimenBienGanancias->setIdBienEconomico($id);
                $regimenBienGanancias->setRegimenRetencion($regimenGanancias);
                $regimenGanancias->addRegimenesRetencionBienEconomico($regimenBienGanancias);
            }

            $regimenSUSSViejo = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')
                    ->getRegimenRetencionBienEconomicoByImpuestoYBienEconomico(ConstanteTipoImpuesto::SUSS, $id);

            $regimenIVAViejo = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')
                    ->getRegimenRetencionBienEconomicoByImpuestoYBienEconomico(ConstanteTipoImpuesto::IVA, $id);

            $regimenIIBBViejo = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')
                    ->getRegimenRetencionBienEconomicoByImpuestoYBienEconomico(ConstanteTipoImpuesto::IIBB, $id);

            $regimenGananciasViejo = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')
                    ->getRegimenRetencionBienEconomicoByImpuestoYBienEconomico(ConstanteTipoImpuesto::Ganancias, $id);

            if ($regimenSUSSViejo != null) {
                $emContable->remove($regimenSUSSViejo);
            }
            if ($regimenIVAViejo != null) {
                $emContable->remove($regimenIVAViejo);
            }
            if ($regimenIIBBViejo != null) {
                $emContable->remove($regimenIIBBViejo);
            }
            if ($regimenGananciasViejo != null) {
                $emContable->remove($regimenGananciasViejo);
            }

            $emContable->flush();


            return $this->redirect($this->generateUrl('bieneconomico'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$bienEconomico->getDenominacionBienEconomico()] = $this->generateUrl('bieneconomico_show', array('id' => $bienEconomico->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $bienEconomico,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar bien econ&oacute;mico'
        );
    }

    /**
     * Deletes a BienEconomico entity.
     *
     * @Route("/borrar/{id}", name="bieneconomico_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:BienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BienEconomico.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('bieneconomico'));
    }

    /**
     * @Route("/lista_bienes", name="lista_bienes")
     */
    public function getBienesEconomicosByRubroAction(Request $request) {

        $id_rubro = $request->request->get('id_rubro');

        $repository = $this->getDoctrine()->getRepository('ADIFComprasBundle:BienEconomico', $this->getEntityManager());

        $query = $repository->createQueryBuilder('be')
                ->select('be.id', 'be.denominacionBienEconomico')
                ->join('be.estadoBienEconomico', 'e')
                ->where('be.rubro =  :rubro')
                ->andWhere('e.denominacionEstadoBienEconomico =  :denominacionEstadoBienEconomico')
                ->setParameter('rubro', $id_rubro)
                ->setParameter('denominacionEstadoBienEconomico', ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO)
                ->orderBy('be.denominacionBienEconomico', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

    /**
     * @Route("/requiere_especificacion_tecnica", name="requiere_especificacion_tecnica")
     */
    public function getRequiereEspecificacionTecnicaAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idBienEconomico = $request->request->get('id_bien_economico');

        if ($idBienEconomico) {
            $bienEconomico = $em->getRepository('ADIFComprasBundle:BienEconomico')->find($idBienEconomico);

            if (!$bienEconomico) {
                throw $this->createNotFoundException('No se puede encontrar la entidad BienEconomico.');
            }

            return new JsonResponse($bienEconomico->getRequiereEspecificacionTecnica());
        } //. 
        else {
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/estados", name="bieneconomico_estados")
     */
    public function listaEstadoBienEconomicoAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoBienEconomico', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstadoBienEconomico')
                ->orderBy('e.denominacionEstadoBienEconomico', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'bieneconomico_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * Tabla para BienEconomico.
     *
     * @Route("/index_table/", name="bieneconomico_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        return $this->render('ADIFComprasBundle:BienEconomico:index_table.html.twig', array('bienesEconomicos' => $em->getRepository('ADIFComprasBundle:Vistas\VistaBienEconomico')->findAll())
        );
    }

    /**
     * 
     * @param type $arrayCollection
     * @param type $constante
     */
    private function getRegimenByImpuesto($arrayCollection, $constante) {
        return $arrayCollection->filter(
                        function($entry) use ($constante) {
                    return in_array($entry->getRegimenRetencion()->getTipoImpuesto()->getDenominacion(), array($constante));
                });
    }

    /**
     * 
     * @param type $tipoImpuestos
     * @return type
     */
    private function getTipoImpuestos($tipoImpuestos) {
        $impuestosArray = [];

        foreach ($tipoImpuestos as $tipoImpuesto) {
            $impuestosArray[$tipoImpuesto->getId()] = $tipoImpuesto->getDenominacion();
        }

        return $impuestosArray;
    }

    /**
     * 
     * @param type $tipoImpuestos
     * @return type
     */
    private function getRegimenesByImpuesto($impuestos, $regimenes) {
        $regimenesArray = [];

        foreach ($impuestos as $impuesto) {
            $regimenesArray[$impuesto->getId()] = [];
            foreach ($regimenes as $regimen) {
                if ($regimen->getTipoImpuesto() === $impuesto) {
                    $regimenesArray[$impuesto->getId()][$regimen->getId()] = $regimen->getDenominacion();
                }
            }
        }

        return $regimenesArray;
    }

    /**
     * @Route("/ajax_table/", name="bieneconomico_ajax_table")
     * @Method("GET|POST")
     * @Template()
     */
    public function ajaxTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $get = $request->query->all();

        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $columns = array('id', 'esProducto',
            'denominacionBienEconomico', 'rubro',
            'requiereEspecificacionTecnica', 'estadoBienEconomico'
        );

        $get['columns'] = &$columns;

        $bienesEconomicos = $em->getRepository('ADIFComprasBundle:BienEconomico')
                        ->ajaxTable($get, true)->getArrayResult();

        /*
         * Output
         */
        $output = array(
            "draw" => intval($get['draw']),
            "recordsTotal" => $em->getRepository('ADIFComprasBundle:BienEconomico')->getCount(),
            "recordsFiltered" => $em->getRepository('ADIFComprasBundle:BienEconomico')
                    ->getFilteredCount($get),
            "data" => array()
        );

        $regimenBien = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')
                ->findAll();

        $arrayCollectionRegimenesBien = ($regimenBien != null) //
                ? new ArrayCollection($regimenBien) //
                : new ArrayCollection();

        foreach ($bienesEconomicos as $bienEconomico) {

            $row = array();

            $idBienEconomico = $bienEconomico['id'];

            $regimenesBien = $arrayCollectionRegimenesBien->filter(
                    function($entry) use ($idBienEconomico) {
                return in_array($entry->getIdBienEconomico(), array($idBienEconomico));
            });

            $regimenSUSS = $this->getRegimenByImpuesto($regimenesBien, ConstanteTipoImpuesto::SUSS);
            $regimenIVA = $this->getRegimenByImpuesto($regimenesBien, ConstanteTipoImpuesto::IVA);
            $regimenIIBB = $this->getRegimenByImpuesto($regimenesBien, ConstanteTipoImpuesto::IIBB);
            $regimenGanancias = $this->getRegimenByImpuesto($regimenesBien, ConstanteTipoImpuesto::Ganancias);

            $row[] = $bienEconomico['id'];
            $row[] = $bienEconomico['id'];
            $row[] = ($bienEconomico['esProducto'] ? 'Producto' : 'Servicio');
            $row[] = $bienEconomico['denominacionBienEconomico'];
            $row[] = $bienEconomico['rubro']['denominacionRubro'];
            $row[] = ($bienEconomico['requiereEspecificacionTecnica'] ? 'Si' : 'No');
            $row[] = ($regimenSUSS->isEmpty()) ? 'No asignado' : $regimenSUSS->first()->getRegimenRetencion()->getDenominacionCompleta();
            $row[] = ($regimenIVA->isEmpty()) ? 'No asignado' : $regimenIVA->first()->getRegimenRetencion()->getDenominacionCompleta();
            $row[] = ($regimenIIBB->isEmpty()) ? 'No asignado' : $regimenIIBB->first()->getRegimenRetencion()->getDenominacionCompleta();
            $row[] = ($regimenGanancias->isEmpty()) ? 'No asignado' : $regimenGanancias->first()->getRegimenRetencion()->getDenominacionCompleta();
            $row[] = [
                "aliasTipoImportancia" => $bienEconomico['estadoBienEconomico']['tipoImportancia']['aliasTipoImportancia'],
                "estadoBienEconomico" => $bienEconomico['estadoBienEconomico']['denominacionEstadoBienEconomico']
            ];
            $row[] = [
                "show" => $idBienEconomico,
                "edit" => "editar/" . $idBienEconomico
                    ]
            ;

            $output['data'][] = $row;
        }

        unset($bienesEconomicos);

        return new JsonResponse($output);
    }

}
