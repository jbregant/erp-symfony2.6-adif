<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDocumentoFinanciero;
use ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato;
use ADIF\ContableBundle\Entity\Obras\AnticipoFinanciero;
use ADIF\ContableBundle\Entity\Obras\CertificadoObra;
use ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero;
use ADIF\ContableBundle\Entity\Obras\FondoReparo;
use ADIF\ContableBundle\Entity\Obras\PolizaSeguroDocumentoFinanciero;
use ADIF\ContableBundle\Entity\Obras\RedeterminacionObra;
use ADIF\ContableBundle\Form\Obras\DocumentoFinancieroType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Obras\DocumentoFinanciero controller.
 *
 * 
 * @Route("/documento_financiero")
 */
class DocumentoFinancieroController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Documentos financieros' => $this->generateUrl('documento_financiero')
        );
    }

    /**
     * Lists all Obras\DocumentoFinanciero entities.
     *
     * @Route("/", name="documento_financiero")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Documentos financieros'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Documentos financieros',
            'page_info' => 'Lista de documentos financieros'
        );
    }

    /**
     * Tabla para Obras\DocumentoFinanciero .
     *
     * @Route("/index_table/", name="documento_financiero_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $entities = $em
            ->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
            ->getAll();

        $bread = $this->base_breadcrumbs;
        $bread['Documentos financieros'] = null;

        return $this->render('ADIFContableBundle:Obras/DocumentoFinanciero:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Tabla para Obras\DocumentoFinanciero .
     *
     * @Route("/index_table_proveedor/", name="documento_financiero_table_proveedor")
     * @Method("GET|POST")
     */
    public function indexTableByProveedorAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipoDocumentosFinancierosNoValidos = [
            ConstanteTipoDocumentoFinanciero::ECONOMIA
        ];

        $documentosFinancieros = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->createQueryBuilder('df')
                ->innerJoin('df.tipoDocumentoFinanciero', 'tdf')
                ->innerJoin('df.tramo', 't')
                ->where('tdf.id NOT IN (:tipoDocumentosFinancieros)')
                ->andWhere('df.fechaAnulacion IS NULL')
                ->andWhere('t.idProveedor = :idProveedor')
                ->setParameter('idProveedor', $request->request->get('id_proveedor'))
                ->setParameter('tipoDocumentosFinancieros', $tipoDocumentosFinancierosNoValidos)
                ->orderBy('df.fechaDocumentoFinancieroInicio', 'ASC')
                ->getQuery()
                ->getResult();

//        $documentosFinancierosFiltrados = array_filter($documentosFinancieros, function($documentoFinanciero) {
//            return $documentoFinanciero->getSaldo() != 0;
//        });

        return $this->render('ADIFContableBundle:Obras/DocumentoFinanciero:index_table_por_proveedor.html.twig', array('documentosFinancieros' => $documentosFinancieros));
    }

    /**
     * Tabla para Obras\DocumentoFinanciero .
     *
     * @Route("/index_table_tramo/", name="documento_financiero_table_tramo")
     * @Method("GET|POST")
     */
    public function indexTableByTramoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $documentosFinancierosJson = [];

        $documentosFinancieros = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->createQueryBuilder('df')
                ->innerJoin('df.tramo', 't')
                ->where('t.id = :idTramo')
				->andWhere('df.fechaAnulacion IS NULL')
                ->setParameter('idTramo', $request->request->get('id_tramo'))
                ->orderBy('df.fechaDocumentoFinancieroInicio', 'ASC')
                ->getQuery()
                ->getResult();

        foreach ($documentosFinancieros as $documentoFinanciero) {

            /* @var $documentoFinanciero DocumentoFinanciero */

            $documentosFinancierosJson[] = [
                'id' => $documentoFinanciero->getId(),
                'tipoDocumentoFinanciero' => $documentoFinanciero->getTipoDocumentoFinanciero()->__toString(),
                'numero' => $documentoFinanciero->getNumero() != null ? $documentoFinanciero->getNumero() : '',
                'fechaDocumentoFinancieroInicio' => $documentoFinanciero->getFechaDocumentoFinancieroInicio()->format('d/m/Y'),
                'fechaDocumentoFinancieroFin' => $documentoFinanciero->getFechaDocumentoFinancieroFin()->format('d/m/Y'),
                'montoSinIVA' => $documentoFinanciero->getMontoSinIVA() * ($documentoFinanciero->getEsEconomia() ? -1 : 1),
                'montoFondoReparo' => $documentoFinanciero->getMontoFondoReparo(),
				'montoTotalDocumentoFinanciero' => $documentoFinanciero->getMontoTotalDocumentoFinanciero()
            ];
        }

        return new JsonResponse($documentosFinancierosJson);
    }

    /**
     * Creates a new Obras\DocumentoFinanciero entity.
     *
     * @Route("/insertar", name="documento_financiero_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\DocumentoFinanciero:new.html.twig")
     */
    public function createAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipoDocumentoFinanciero = $request->request->get('adif_contablebundle_obras_documentofinanciero', false)['tipoDocumentoFinanciero'];

        $entity = ConstanteTipoDocumentoFinanciero::getSubclass($tipoDocumentoFinanciero);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $accion = $request->request->get('accion');

            // A cada PolizaSeguroDocumentoFinanciero, le seteo el DocumentoFinanciero
            foreach ($entity->getPolizasSeguro() as $polizaSeguro) {
                $polizaSeguro->setDocumentoFinanciero($entity);
            }

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($entity);

            $this->setSubclassData($request, $entity);

            if ($entity->getEsRedeterminacionObra() || $entity->getEsDemasia()) {

                // Actualizo el monto del difinitivo del tramo, sumandole el monto del DocumentoFinanciero
                $this->get('adif.contabilidad_presupuestaria_service')
                        ->actualizarDefinitivoFromDocumentoFinanciero($entity);
            } elseif ($entity->getEsEconomia()) {

                // Actualizo el monto del difinitivo del tramo, restandole el monto del DocumentoFinanciero
                $this->get('adif.contabilidad_presupuestaria_service')
                        ->actualizarDefinitivoFromDocumentoFinanciero($entity, false);
            }

            $em->persist($entity);

            $em->flush();

            if (null != $accion) {

                // Si se apretó el boton "Guardar"
                if ('save' == $accion) {
                    return $this->redirect($this->generateUrl('documento_financiero'));
                }

                // Sino, si se apretó el boton "Guardar y cargar comprobante"
                else if ('save_continue' == $accion) {
                    return $this->redirect($this->generateUrl('comprobanteobra_new_from_documento_financiero', array('documentoFinanciero' => $entity->getId())));
                }
            }
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear documento financiero',
        );
    }

    /**
     * Creates a form to create a Obras\DocumentoFinanciero entity.
     *
     * @param DocumentoFinanciero $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    
    private function createCreateForm(DocumentoFinanciero $entity) {
        $form = $this->createForm(new DocumentoFinancieroType(), $entity, array(
            'action' => $this->generateUrl('documento_financiero_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar')
                )
                ->add('save_continue', 'submit', array(
                    'label' => 'Guardar y cargar comprobante'
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\DocumentoFinanciero entity.
     *
     * @Route("/crear", name="documento_financiero_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $entity = new DocumentoFinanciero();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
			'polizas' => array(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear documento financiero',
			'total_real_df' => 0
        );
    }

    /**
     * Finds and displays a Obras\DocumentoFinanciero entity.
     *
     * @Route("/{id}", name="documento_financiero_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\DocumentoFinanciero.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Documento financiero'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver documento financiero'
        );
    }

    /**
     * Displays a form to edit an existing Obras\DocumentoFinanciero entity.
     *
     * @Route("/editar/{id}", name="documento_financiero_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\DocumentoFinanciero:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);

        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\DocumentoFinanciero.');
        }

        $proveedor = $documentoFinanciero->getTramo()->getProveedor();

        $editForm = $this->createEditForm($documentoFinanciero);

        $editForm->get('proveedor_razonSocial')->setData($proveedor->getRazonSocial());

        $editForm->get('proveedor_cuit')->setData($proveedor->getCUIT());

        $editForm->get('montoTotalDocumentoFinanciero')
                ->setData($documentoFinanciero->getMontoTotalDocumentoFinanciero());

        // AnticipoFinanciero
        if ($documentoFinanciero->getEsAnticipoFinanciero()) {

            $editForm->get('porcentajeAnticipo')
                    ->setData($documentoFinanciero->getPorcentajeAnticipo());

            $editForm->get('montoFondoReparo')
                    ->setData($documentoFinanciero->getMontoFondoReparo());
        }

        // CertificadoObra
        if ($documentoFinanciero->getEsCertificadoObra()) {

            $editForm->get('numero')->setData($documentoFinanciero->getNumero());

            $editForm->get('porcentajeCertificacion')
                    ->setData($documentoFinanciero->getPorcentajeCertificacion());

            $editForm->get('montoFondoReparo')
                    ->setData($documentoFinanciero->getMontoFondoReparo());
        }

        // FondoReparo
        if ($documentoFinanciero->getEsFondoReparo()) {

            $editForm->get('porcentajeAbonar')
                    ->setData($documentoFinanciero->getPorcentajeAbonar());
        }

        // RedeterminacionObra
        if ($documentoFinanciero->getEsRedeterminacionObra()) {

            $editForm->get('numero')->setData($documentoFinanciero->getNumero());

            $editForm->get('montoFondoReparo')
                    ->setData($documentoFinanciero->getMontoFondoReparo());
        }


        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $documentoFinanciero,
            'polizas' => $documentoFinanciero->getPolizasSeguro(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar documento financiero',
            'total_real_df' => $documentoFinanciero->getMontoTotalDocumentoFinanciero()
        );
    }
    /**
     * Muestra el modal para editar la fecha de aprobacion tecnica.
     *
     * @Route("/editar_fecha_aprobacion_tecnica_modal/{id}", name="documento_financiero_edit_fecha_aprobacion_tecnica_modal")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\DocumentoFinanciero:editar_fecha_aprobacion_tecnica_form.html.twig")
     * @Security("has_role('ROLE_DOCUMENTO_FINANCIERO_EDITAR_FECHA_APROBACION_TECNICA')")   
     */
    public function editFechaAprobacionTecnicaModalAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);
        
        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\DocumentoFinanciero.');
        }
        
        return array(
                'id' => $id,
                'fecha_aprobacion_tecnica' => ($documentoFinanciero->getFechaAprobacionTecnica() != null) 
                                                ? $documentoFinanciero->getFechaAprobacionTecnica()->format('d/m/Y')
                                                : null
        );
    }

    /**
     * Creates a form to edit a Obras\DocumentoFinanciero entity.
     *
     * @param DocumentoFinanciero $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DocumentoFinanciero $entity) {
        $form = $this->createForm(new DocumentoFinancieroType(), $entity, array(
            'action' => $this->generateUrl('documento_financiero_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar')
                )
                ->add('save_continue', 'submit', array(
                    'label' => 'Guardar y cargar comprobante'
        ));

        return $form;
    }
    
    /**
     * Edits an existing Obras\DocumentoFinanciero entity.
     *
     * @Route("/actualizar/{id}", name="documento_financiero_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\DocumentoFinanciero:new.html.twig")
     */
    public function updateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);

        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DocumentoFinanciero.');
        }

        $polizasOriginales = new ArrayCollection();

        $adjuntosOriginales = new ArrayCollection();

        // Creo un ArrayCollection de las PolizaSeguroDocumentoFinanciero actuales en la BBDD
        foreach ($documentoFinanciero->getPolizasSeguro() as $polizaSeguro) {
            $polizasOriginales->add($polizaSeguro);
        }

        // Creo un ArrayCollection de los adjuntos actuales en la BBDD
        foreach ($documentoFinanciero->getArchivos() as $adjunto) {
            $adjuntosOriginales->add($adjunto);
        }

        $editForm = $this->createEditForm($documentoFinanciero);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $accion = $request->request->get('accion');

            $documentoFinanciero->setFechaUltimaActualizacion(new \DateTime());

            // A cada PolizaSeguroDocumentoFinanciero, le seteo el DocumentoFinanciero
			if (isset($request->request->get('adif_contablebundle_obras_documentofinanciero')['polizasSeguro']) 
				&& $request->request->get('adif_contablebundle_obras_documentofinanciero')['polizasSeguro'] != null) {
					
				$arr = array_values($request->request->get('adif_contablebundle_obras_documentofinanciero')['polizasSeguro']);
				$i = 0;
				foreach ($documentoFinanciero->getPolizasSeguro() as $polizaSeguro) {

					$idAseguradora = $arr[$i]["aseguradora2"];
					$aseguradora = $em->getRepository('ADIFContableBundle:Aseguradora')->find($idAseguradora);
					$polizaSeguro->setAseguradora( $aseguradora->getNombre() );
					$polizaSeguro->setDocumentoFinanciero($documentoFinanciero);
					$i++;
				}
			}

			// Por cada PolizaSeguroDocumentoFinanciero original
			foreach ($polizasOriginales as $polizaSeguro) {

				// Si fue eliminado
				if (false === $documentoFinanciero->getPolizasSeguro()->contains($polizaSeguro)) {
					$documentoFinanciero->removePolizasSeguro($polizaSeguro);
					$em->remove($polizaSeguro);
				}
			}
			

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($documentoFinanciero);

            // Por cada adjunto original
            foreach ($adjuntosOriginales as $adjunto) {

                // Si fue eliminado
                if (false === $documentoFinanciero->getArchivos()->contains($adjunto)) {

                    $documentoFinanciero->removeArchivo($adjunto);

                    $em->remove($adjunto);
                }
            }

            $this->setSubclassData($request, $documentoFinanciero);

            $em->flush();

            if (null != $accion) {

                // Si se apretó el boton "Guardar"
                if ('save' == $accion) {
                    return $this->redirect($this->generateUrl('documento_financiero'));
                }

                // Sino, si se apretó el boton "Guardar y cargar comprobante"
                else if ('save_continue' == $accion) {
                    return $this->redirect($this->generateUrl('comprobanteobra_new_from_documento_financiero', array('documentoFinanciero' => $documentoFinanciero->getId())));
                }
            }
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $documentoFinanciero,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar documento financiero',
            'total_real_df' => $documentoFinanciero->getMontoTotalDocumentoFinanciero()
        );
    }
    
     /**
     * Edita solo el campo "fechaAprobacionTecnica" de un DocumentoFinanciero existente.
     *
     * @Route("/actualizar_fecha_aprobacion_tecnica", name="documento_financiero_update_fecha_aprobacion_tecnica")
     * @Method("POST")
     * @Template("")
     */
    public function updateFechaAprobacionTecnicaAction(Request $request) 
    {
        $id = $request->get('id');
        $fechaAprobacionTecnica = $request->get('adif_contablebundle_obras_documentofinanciero')['fechaAprobacionTecnica'];
        $resultPago = array();
        
        if ($id == null) {
            $resultPago = array(
                'result' => 'ERROR',
                'msg' => 'No se encuentra el ID del documento financiero cuando se envió el formulario. Intente más tarde'
            );
            return new JsonResponse($resultPago);
        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);

        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DocumentoFinanciero.');
        }

        if (!empty($fechaAprobacionTecnica)) {
            $dtFecha = \DateTime::createFromFormat('d/m/Y', $fechaAprobacionTecnica);
            $documentoFinanciero->setFechaAprobacionTecnica($dtFecha);
        } else {
            $documentoFinanciero->setFechaAprobacionTecnica(null);
        }
        
        try {
            $em->persist($documentoFinanciero);
            $em->flush();
            
            $resultPago = array(
                'result' => 'OK',
                'msg' => 'Se editó la fecha de aprobación técnica con exito'
            );
            
        } catch (Exception $ex) {
            
            $resultPago = array(
                'result' => 'ERROR',
                'msg' => 'No se pudo editar la fecha de aprobación técnica para el documento financiero'
            );
        }
        
        return new JsonResponse($resultPago);
    }

    /**
     * Deletes a Obras\DocumentoFinanciero entity.
     *
     * @Route("/borrar/{id}", name="documento_financiero_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\DocumentoFinanciero.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('documento_financiero'));
    }

    /**
     * @Route("/polizas_vencidas/", name="documento_financiero_polizas_vencidas")
     */
    public function cantidadPolizasVencidasByFecha(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->request->get('id');

        $fecha = \DateTime::createFromFormat('d/m/Y', $request->get('fecha'));

        $tramo = $em->getRepository('ADIFContableBundle:Obras\Tramo')->find($id);

        $cantidadPolizasVencidas = 0;

        foreach ($tramo->getPolizasSeguro() as $polizaSeguro) {

            /* @var $polizaSeguro PolizaSeguroContrato */

            $polizaSeguro->getFechaVencimiento();

            if ($polizaSeguro->getFechaVencimiento()->format("Y-m-d") < $fecha->format("Y-m-d")) {

                $cantidadPolizasVencidas++;
            }
        }

        return new JsonResponse($cantidadPolizasVencidas);
    }

    /**
     * 
     * @param DocumentoFinanciero $documentoFinanciero
     */
    private function updateAdjuntos(DocumentoFinanciero $documentoFinanciero) {

        foreach ($documentoFinanciero->getArchivos() as $adjunto) {

            if ($adjunto->getArchivo() != null) {

                $adjunto->setDocumentoFinanciero($documentoFinanciero);

                $adjunto->setNombre($adjunto->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * 
     * @param type $request
     * @param FondoReparo $entity
     */
    private function setSubclassData($request, $entity) {

        $requestDocumentoFinanciero = $request->request->get('adif_contablebundle_obras_documentofinanciero');

        // Si es un CertificadoObra
        if ($entity->getEsCertificadoObra()) {

            /* @var $entity CertificadoObra */

            $porcentajeCertificacion = str_replace(',', '.', $requestDocumentoFinanciero['porcentajeCertificacion']);

            $montoFondoReparo = str_replace(',', '.', $requestDocumentoFinanciero['montoFondoReparo']);

            $entity->setNumero($requestDocumentoFinanciero['numero']);

            $entity->setPorcentajeCertificacion($porcentajeCertificacion);

            $entity->setMontoFondoReparo($montoFondoReparo);
        }

        // Si es una RedeterminacionObra
        if ($entity->getEsRedeterminacionObra()) {

            /* @var $entity RedeterminacionObra */

            $montoFondoReparo = str_replace(',', '.', $requestDocumentoFinanciero['montoFondoReparo']);

            $entity->setNumero($requestDocumentoFinanciero['numero']);

            $entity->setMontoFondoReparo($montoFondoReparo);
        }

        // Si es un AnticipoFinanciero
        if ($entity->getEsAnticipoFinanciero()) {

            /* @var $entity AnticipoFinanciero */

            $porcentajeAnticipo = str_replace(',', '.', $requestDocumentoFinanciero['porcentajeAnticipo']);

            $montoFondoReparo = str_replace(',', '.', $requestDocumentoFinanciero['montoFondoReparo']);

            $entity->setPorcentajeAnticipo($porcentajeAnticipo);

            $entity->setMontoFondoReparo($montoFondoReparo);
        }

        // Si es un FondoReparo
        if ($entity->getEsFondoReparo()) {

            /* @var $entity FondoReparo */

            $porcentajeAbonar = str_replace(',', '.', $requestDocumentoFinanciero['porcentajeAbonar']);

            $entity->setPorcentajeAbonar($porcentajeAbonar);
        }
    }

    /**
     * Reporte general de documentos financieros.
     *
     * @Route("/reporte_general/", name="documento_financiero_reporte_general")
     * @Method("GET|POST")
     */
    public function reporteGeneralDocumentoFinancieroAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte general'] = null;

        return $this->render('ADIFContableBundle:Obras\DocumentoFinanciero:reporte_general.html.twig', array(
                    'breadcrumbs' => $bread,
                    'page_title' => 'Reporte general de documentos financieros'
        ));
    }

    /**
     * Tabla para Obras\DocumentoFinanciero .
     *
     * @Route("/reporte_general_table/", name="documento_financiero_reporte_general_table")
     * @Method("GET|POST")
     */
    public function reporteGeneralTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $documentosFinancieros = $em
            ->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
            ->getReporteGeneral();
        
        $bread = $this->base_breadcrumbs;
        $bread['Documentos financieros'] = null;

        return $this->render('ADIFContableBundle:Obras/DocumentoFinanciero:reporte_general_table.html.twig', array(
                    'documentosFinancieros' => $documentosFinancieros
                        )
        );
    }

    /**
     * @Route("/anular/{id}", name="documento_financiero_anular")
     * @Method("GET")
     */
    public function anularAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);

        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DocumentoFinanciero.');
        }

        $documentoFinanciero->setFechaAnulacion(new \DateTime());

        if ($documentoFinanciero->getEsRedeterminacionObra() || $documentoFinanciero->getEsDemasia()) {

            // Actualizo el monto del difinitivo del tramo, restandolo el monto del DocumentoFinanciero
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->actualizarDefinitivoFromDocumentoFinanciero($documentoFinanciero, false);
        } elseif ($documentoFinanciero->getEsEconomia()) {

            // Actualizo el monto del difinitivo del tramo, sumandole el monto del DocumentoFinanciero
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->actualizarDefinitivoFromDocumentoFinanciero($documentoFinanciero);
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La anulación se realizó con éxito.');

        return $this->redirect($this->generateUrl('documento_financiero'));
    }

    /**
     * @param Request $request
     * 
     * @Route("/validaciones/", name="documento_financiero_validaciones")
     * @Method("POST")
     */
    public function getValidaciones(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idTramo = $request->request->get('idTramo');

        $ultimoCertificadoObra = $em->getRepository('ADIFContableBundle:Obras\CertificadoObra')
                ->findOneBy(array('tramo' => $idTramo), array('numero' => 'DESC'));

        if ($ultimoCertificadoObra) {
            $numeroUltimoCertificado = $ultimoCertificadoObra->getNumeroSinFormato();
            $fechaUltimoCertificado = $ultimoCertificadoObra->getFechaDocumentoFinancieroFin()->format('d/m/Y');
        } else {
            $numeroUltimoCertificado = -1;
            $fechaUltimoCertificado = -1;
        }

        $ultimaRedeterminacionObra = $em->getRepository('ADIFContableBundle:Obras\RedeterminacionObra')
                ->findOneBy(array('tramo' => $idTramo), array('numero' => 'DESC'));

        if ($ultimaRedeterminacionObra) {
            $numeroUltimaRedeterminacion = $ultimaRedeterminacionObra->getNumeroSinFormato();
            $fechaUltimaRedeterminacion = $ultimaRedeterminacionObra->getFechaDocumentoFinancieroFin()->format('d/m/Y');
        } else {
            $numeroUltimaRedeterminacion = -1;
            $fechaUltimaRedeterminacion = -1;
        }


        $resultado = array(
            'numero_ultimo_certificado' => $numeroUltimoCertificado,
            'fecha_ultimo_certificado' => $fechaUltimoCertificado,
            'numero_ultima_redeterminacion' => $numeroUltimaRedeterminacion,
            'fecha_ultima_redeterminacion' => $fechaUltimaRedeterminacion
        );

        return new JsonResponse($resultado);
    }

    /**
     * @Route("/corresponde_pago/{id}", name="documento_financiero_corresponde_pago")
     * @Method("GET")
     */
    public function correspondePagoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $em->getRepository('ADIFContableBundle:Obras\DocumentoFinanciero')
                ->find($id);

        if (!$documentoFinanciero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DocumentoFinanciero.');
        }

        $documentoFinanciero->setCorrespondePago(!$documentoFinanciero->getCorrespondePago());

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El documento financiero se actualizó correctamente.');

        return $this->redirect($this->generateUrl('documento_financiero'));
    }
    
    /**     
     *
     * @Route("/generar_reporte_seguimiento_certificados/", name="documento_financiero_generar_reporte_seguimiento_certificados")
     * @Method("GET|POST")
     */
    public function generarReporteSeguimientoCertificadosAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();        
        $rsm->addScalarResult('fechaCreacion', 'fechaCreacion');
        $rsm->addScalarResult('tipoContratacionAlias', 'tipoContratacionAlias');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('anio', 'anio');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('tipoDocumentoFinanciero', 'tipoDocumentoFinanciero');
        $rsm->addScalarResult('numeroDocumentoFinanciero', 'numeroDocumentoFinanciero');
        $rsm->addScalarResult('fechaAnulacion', 'fechaAnulacion');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('comprobante', 'comprobante');
        $rsm->addScalarResult('totalComprobante', 'totalComprobante');
        $rsm->addScalarResult('fechaIngresoADIF', 'fechaIngresoADIF');
        $rsm->addScalarResult('fechaIngresoGerenciaAdministracion', 'fechaIngresoGerenciaAdministracion');
        $rsm->addScalarResult('numeroReferencia', 'numeroReferencia');        
        $rsm->addScalarResult('ordenPago', 'ordenPago');
        $rsm->addScalarResult('pago', 'pago');
        $rsm->addScalarResult('fechaPago', 'fechaPago');
        $rsm->addScalarResult('estadoPago', 'estadoPago');
        $rsm->addScalarResult('montoNeto', 'montoNeto');    
		
		$rsm->addScalarResult('porcentajeCertificacion', 'porcentajeCertificacion');    
		$rsm->addScalarResult('fechaInicio', 'fechaInicio');    
		$rsm->addScalarResult('fechaFin', 'fechaFin');    
        
        $form = $this->createReporteSeguimientoCertificadosForm();
        $form->handleRequest($request);
                
        $tiposDocumentos = array();
        
        foreach($form['tiposDocumento']->getData() as $tipoDocumento){
            $tiposDocumentos[] = $tipoDocumento->getId();
        }
        $correspondePago = $form['correspondePago']->getData();
        $incluirAnulados = $form['fechaAnulacion']->getData();
        $poseeCheque = $form['poseeCheque']->getData();
        
        $query = "
            SELECT
                fechaCreacion,
                tipoContratacionAlias,
                numero,
                anio,
                descripcion,
                cuit,
                razonSocial,
                tipoDocumentoFinanciero,
                numeroDocumentoFinanciero,
                fechaAnulacion,
                correspondePago,
                comprobante,
                totalComprobante,
                fechaIngresoADIF,
                fechaIngresoGerenciaAdministracion,
                numeroReferencia,
                ordenPago,
                pago,
                fechaPago,
                estadoPago,
                montoNeto,
				porcentajeCertificacion,
				fechaInicio,
				fechaFin
            FROM vistaseguimientocertificados
            WHERE idTipoDocumentoFinanciero IN (?)
                AND correspondePago = ?
                AND ordenPago ".($poseeCheque ? '<>':'=')." '-'";
        
        if(!$incluirAnulados){
            $query .= ' AND fechaAnulacion IS NULL';
        }
        
        $native_query = $em->createNativeQuery($query, $rsm);
        
        $native_query->setParameter(1, $tiposDocumentos);
        $native_query->setParameter(2, $correspondePago);
        
        $documentosFinancieros = $native_query->getResult();
        
        $eController = new \ADIF\ContableBundle\Controller\ExporterCustomController();
        $eController->setContainer($this->container);
        return $eController->exportReporteSeguimientoCertificadosAction($documentosFinancieros);
    }
    
    private function createReporteSeguimientoCertificadosForm() {
        return
            $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('documento_financiero_generar_reporte_seguimiento_certificados'),
                'method' => 'POST'
            ))
            ->add('tiposDocumento', 'entity', array(
                'class' => 'ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero',
                'required' => true,
                'label' => 'Tipos documento',
                'multiple' => true,
                'attr' => array('class' => ' form-control choice '),
                'empty_value' => '-- Elija los tipos --',
                'by_reference' => true
            ))
            ->add('fechaAnulacion', 'checkbox', array(
                'required' => false,                
                'label' => 'Incluir anulados',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control checkbox ignore')
            ))
            ->add('correspondePago', 'checkbox', array(
                'required' => false,
                'label' => 'Corresponde pago',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control checkbox ignore')
            ))                
            ->add('poseeCheque', 'checkbox', array(
                'required' => false,
                'label' => 'Posee cheque',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control checkbox ignore')
            ))
            ->add('submit', 'submit', array('label' => 'Generar'))
            ->getForm();
    }
    
    /**     
     *
     * @Route("/reporte_seguimiento_certificados/", name="documento_financiero_reporte_seguimiento_certificados")
     * @Method("GET")
     * @Template()
     */
    public function indexReporteSeguimientoCertificadosAction() {
        $bread = $this->base_breadcrumbs;        
        $bread['Documentos financieros'] = null;

        $form = $this->createReporteSeguimientoCertificadosForm()->createView();
        
        return array(
            'form' => $form,
            'breadcrumbs' => $bread,
            'page_title' => 'Reporte seguimiento certificados'
        );
    }

}
