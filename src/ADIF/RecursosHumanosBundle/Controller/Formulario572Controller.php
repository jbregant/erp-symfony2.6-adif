<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Formulario572;
use ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572;
use ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572;
use ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572Aplicado;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Form\Formulario572Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DOMDocument;
use Symfony\Component\Debug\Exception\ContextErrorException;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;

/**
 * Formulario572 controller.
 *
 * @Route("/formulario572")
 * @Security("has_role('ROLE_RRHH_ALTA_F572')")
 */
class Formulario572Controller extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Formularios 572' => $this->generateUrl('formulario572')
        );
    }

    /**
     * Lists all Formulario572 entities.
     *
     * @Route("/", name="formulario572")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Formularios 572'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Formulario 572',
            'page_info' => 'Lista de formularios 572'
        );
    }

    /**
     * Creates a new Formulario572 entity.
     *
     * @Route("/insertar/{empleado}", name="formulario572_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Formulario572:new.html.twig")
     */
    public function createAction(Request $request, Empleado $empleado) {
        $formulario572 = new Formulario572();

        $aniosFormularios = [];

        foreach ($empleado->getFormularios572() as $formulario572Empleado) {
            $aniosFormularios[] = $formulario572Empleado->getAnio();
        }

        $form = $this->createCreateForm($formulario572, $empleado);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $formulario572->setAnio($formulario572->getAnio());
			
			// Armo el datetime del periodo
			$dtPeriodoFechaDesde = \Datetime::createFromFormat('Y-m-d H:i:s', $formulario572->getAnio() . '-01-01 00:00:00');

            // Seteo el Empleado al Formulario 572
            $formulario572->setEmpleado($empleado);
            
            $conceptosConDetalleAplicado = array(
                ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR, 
                ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR,
                ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO, 
                ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO
            );

            // Por cada Concepto del F. 572
            foreach ($formulario572->getConceptos() as $concepto) {
                /* @var $concepto \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                $concepto->setFormulario572($formulario572);
                if (!$concepto->getConceptoGanancia()->getTieneDetalle()) {
                    $concepto->setDetalleConceptoFormulario572(null);
                } else {
                    $concepto->getDetalleConceptoFormulario572()->setConceptoFormulario572($concepto);
                    if (in_array($concepto->getConceptoGanancia()->getCodigo572(), $conceptosConDetalleAplicado)) {
                        $concepto->setMesHasta($concepto->getMesDesde());
                        $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                        $detalleConceptoF572Aplicado->setPeriodo($concepto->getMesDesde())
                                ->setAplicado(false)
                                ->setConceptoFormulario572($concepto);
                        $concepto->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                        
                        if ($concepto->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR) {
                            $concepto->setMesDesde(1);
                            $concepto->setMesHasta(1);
                        }
                    }
                }
                if ($concepto->getConceptoGanancia()->getEsCargaFamiliar()) {
					
                    $topeConceptoGanancia = $em
						->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
						->findOneBy(
							array(
								'rangoRemuneracion' => $empleado->getRangoRemuneracion(), 
								'conceptoGanancia' => $concepto->getConceptoGanancia(), 
								'fechaDesde' => $dtPeriodoFechaDesde
								//'vigente' => 1
							)
						);
					
					if ($topeConceptoGanancia) {
						$concepto->setMonto(($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope());
					} else {
						$request->attributes->set('form-error', true);
						$this->container->get('request')->getSession()->getFlashBag()
							->add('error', 'No se encuentra el tope concepto ganancia, para el concepto: ' . $concepto->getConceptoGanancia()->getDenominacion());
						
						return $this->redirect($this->generateUrl('formulario572'));
					}
                }
            }

            $em->persist($formulario572);
            $em->flush();

            return $this->redirect($this->generateUrl('formulario572_show', array('id' => $formulario572->getId())));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('formulario572_index_empleado', array('id' => $empleado->getId()));
        $bread['Crear'] = null;

        return array(
            'entity' => $formulario572,
            'anioActual' => date("Y"),
            'aniosFormularios' => $aniosFormularios,
            'empleado' => $empleado,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Formulario 572',
        );
    }

    /**
     * Creates a form to create a Formulario572 entity.
     *
     * @param Formulario572 $formulario572 The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Formulario572 $formulario572, Empleado $empleado) {
        $form = $this->createForm(new Formulario572Type(), $formulario572, array(
            'action' => $this->generateUrl('formulario572_create', array('empleado' => $empleado->getId())),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Formulario572 entity.
     *
     * @Route("/crear/{empleado}", name="formulario572_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Empleado $empleado) {
        $formulario572 = new Formulario572();

        $aniosFormularios = [];

        foreach ($empleado->getFormularios572() as $formulario572Empleado) {
            $aniosFormularios[] = $formulario572Empleado->getAnio();
        }

        $formulario572->setEmpleado($empleado);
        $formulario572->setAnio(date("Y"));

        $formulario572AnioAnterior = $empleado->getFormulario572(date("Y") - 1);
        if ($formulario572AnioAnterior != null) {
            foreach ($formulario572AnioAnterior->getConceptos() as $conceptoFormulario572) {
                /* @var $conceptoFormulario572 ConceptoFormulario572 */
                if ($conceptoFormulario572->getConceptoGanancia()->getEsCargaFamiliar()) {
                    $formulario572->addConcepto(clone $conceptoFormulario572);
                }
            }
        }

        $form = $this->createCreateForm($formulario572, $empleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('formulario572_index_empleado', array('id' => $empleado->getId()));
        $bread['Crear'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conceptos = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findAll();

        return array(
            'entity' => $formulario572,
            'anioActual' => date("Y"),
            'aniosFormularios' => $aniosFormularios,
            'empleado' => $empleado,
            'conceptos' => $this->getTipoConceptos($conceptos),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Formulario 572'
        );
    }

    /**
     * Finds and displays a Formulario572 entity.
     *
     * @Route("/{id}", name="formulario572_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formulario572 = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($id);

        if (!$formulario572) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Formulario572.');
        }

        $empleado = $formulario572->getEmpleado();

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('formulario572_index_empleado', array('id' => $empleado->getId()));
        $bread['Formulario 572 - Año: ' . $formulario572->getAnio()] = null;

        return array(
            'entity' => $formulario572,
            'totalByConcepto' => $this->totalizarPorConcepto($formulario572),
            'empleado' => $empleado,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Formulario 572'
        );
    }

    /**
     * Displays a form to edit an existing Formulario572 entity.
     *
     * @Route("/editar/{id}", name="formulario572_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Formulario572:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Formulario572.');
        }

        $empleado = $entity->getEmpleado();

        $aniosFormularios = [];

        foreach ($empleado->getFormularios572() as $formulario572Empleado) {
            if ($formulario572Empleado->getAnio() != $entity->getAnio()) {
                $aniosFormularios[] = $formulario572Empleado->getAnio();
            }
        }

        $editForm = $this->createEditForm($entity);

        $conceptos = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('formulario572_index_empleado', array('id' => $empleado->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'empleado' => $empleado,
            'anioActual' => date("Y"),
            'aniosFormularios' => $aniosFormularios,
            'conceptos' => $this->getTipoConceptos($conceptos),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Formulario 572'
        );
    }

    /**
     * Creates a form to edit a Formulario572 entity.
     *
     * @param Formulario572 $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Formulario572 $entity) {
        $form = $this->createForm(new Formulario572Type(), $entity, array(
            'action' => $this->generateUrl('formulario572_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Formulario572 entity.
     *
     * @Route("/actualizar/{id}", name="formulario572_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Formulario572:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formulario572 = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($id);

        if (!$formulario572) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Formulario572.');
        }

        $empleado = $formulario572->getEmpleado();

        $aniosFormularios = [];

        foreach ($empleado->getFormularios572() as $formulario572Empleado) {
            if ($formulario572Empleado->getAnio() != $formulario572->getAnio()) {
                $aniosFormularios[] = $formulario572Empleado->getAnio();
            }
        }

		// Armo el datetime del periodo
		$dtPeriodoFechaDesde = \Datetime::createFromFormat('Y-m-d H:i:s', $formulario572->getAnio() . '-01-01 00:00:00');

        $conceptosOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los ConceptoFormulario572 del F. 572 actuales en la BBDD
        foreach ($formulario572->getConceptos() as $concepto) {
            $conceptosOriginales->add($concepto);
        }

        $editForm = $this->createEditForm($formulario572);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Por cada ConceptoFormulario572 original
            foreach ($conceptosOriginales as $concepto) {

                // Si fue eliminado
                if (false === $formulario572->getConceptos()->contains($concepto)) {

                    $formulario572->removeConcepto($concepto);

                    $em->remove($concepto);
                }
            }

            
            $conceptosConDetalleAplicado = array(
                ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR, 
                ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR,
                ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR,
                ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO, 
                ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO
            );

            // Por cada ConceptoFormulario572 del F. 572
            foreach ($formulario572->getConceptos() as $concepto) {
                /* @var $concepto ConceptoFormulario572 */
                $concepto->setFormulario572($formulario572);
                if (!$concepto->getConceptoGanancia()->getTieneDetalle()) {
                    $concepto->setDetalleConceptoFormulario572(null);
                } else {
                    $concepto->getDetalleConceptoFormulario572()->setConceptoFormulario572($concepto);
                    if (in_array($concepto->getConceptoGanancia()->getCodigo572(), $conceptosConDetalleAplicado)) {
                        if ($concepto->getDetalleConceptoFormulario572Aplicado() != null) {
                            if (!$concepto->getDetalleConceptoFormulario572Aplicado()->getAplicado()) {

                                $conceptoPersistido = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')->find($concepto->getId());

                                if ($concepto->getMonto() != $conceptoPersistido->getMonto()) {
                                    $concepto->getDetalleConceptoFormulario572Aplicado()->setAplicado(false);
                                }
                                $concepto->getDetalleConceptoFormulario572Aplicado()->setPeriodo($concepto->getMesDesde());
                            }
                        } else {
                            $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                            $detalleConceptoF572Aplicado->setPeriodo($concepto->getMesDesde())
                                    ->setAplicado(false)
                                    ->setConceptoFormulario572($concepto);
                            $concepto->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                        }
                        
                        if ($concepto->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR) {
                            $concepto->setMesDesde(1);
                            $concepto->setMesHasta(1);
                        }
                    }
                }
				
                if ($concepto->getConceptoGanancia()->getEsCargaFamiliar()) {
					
                    $topeConceptoGanancia = $em
						->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
						->findOneBy(
							array(
								'rangoRemuneracion' => $formulario572->getEmpleado()->getRangoRemuneracion(), 
								'conceptoGanancia' => $concepto->getConceptoGanancia(), 
								'fechaDesde' => $dtPeriodoFechaDesde
								//'vigente' => 1
							)
						);
						
					if ($topeConceptoGanancia) {
						$concepto->setMonto(($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope());
					} else {
						$request->attributes->set('form-error', true);
						$this->container->get('request')->getSession()->getFlashBag()
							->add('error', 'No se encuentra el tope concepto ganancia, para el concepto: ' . $concepto->getConceptoGanancia()->getDenominacion());
						
						return $this->redirect($this->generateUrl('formulario572_show', array('id' => $id)));
					}
                    
                }
            }
            $em->flush();

            return $this->redirect($this->generateUrl('formulario572_show', array('id' => $id)));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('formulario572_index_empleado', array('id' => $empleado->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $formulario572,
            'empleado' => $empleado,
            'anioActual' => date("Y"),
            'aniosFormularios' => $aniosFormularios,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Formulario 572'
        );
    }

    /**
     * Deletes a Formulario572 entity.
     *
     * @Route("/borrar/{id}", name="formulario572_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Formulario572.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('formulario572'));
    }

    /**
     * Devuelve para cada concepto si es carga familiar, y si tiene detalle
     * 
     */
    private function getTipoConceptos($conceptos) {
        $conceptosArray = [];

        foreach ($conceptos as $concepto) {
            $conceptosArray[$concepto->getId()] = array('cargaFamiliar' => $concepto->getEsCargaFamiliar(), 'detalle' => $concepto->getTieneDetalle(), 'esOtrosIngresos' => in_array($concepto->getCodigo572(), array(ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO)), 'periodo' => ($concepto->getCodigo572() == ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR) ? 'Mes' : 'Año');
        }

        return $conceptosArray;
    }

    /**
     * @Route("/importar/f572", name="formulario572_importar")
     * 
     */
    public function importarF572(Request $request) {

        if (strpos($request->files->all()['form_importar_f572_file']->getMimeType(), 'xml') !== false) {

            $filename = time() . '.xml';
            $request->files->all()['form_importar_f572_file']->move(__DIR__ . '/../../../../web/uploads/f572web', $filename);

            $doc = new DOMDocument();
            
            try {
                
                $doc->load(__DIR__ . '/../../../../web/uploads/f572web/' . $filename);
                $is_valid_xml = $doc->schemaValidate(__DIR__ . '/../../../../web/files/f572/schemaf572.xsd');
                
                if ($is_valid_xml) {

                    $archivo = simplexml_load_file(__DIR__ . '/../../../../web/uploads/f572web/' . $filename);

                    //obtengo cuil
                    $cuil_xml = $archivo->empleado->cuit;
                    $cuil = $this->formatearCuil($cuil_xml);
                    $em = $this->getDoctrine()->getManager($this->getEntityManager());
                    //busco a la persona
                    $persona = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->findOneByCuil($cuil);
                    if (!$persona) {
                        $this->get('session')->getFlashBag()->add(
                                'error', 'El empleado indicado en el formulario 572 no existe'
                        );
                        return $this->redirect($this->generateUrl('formulario572'));
                    }

                    //obtengo el empleado
                    $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findOneByPersona($persona);
                    if (!$empleado) {
                        $this->get('session')->getFlashBag()->add(
                                'error', 'El empleado indicado en el formulario 572 no existe'
                        );
                        return $this->redirect($this->generateUrl('formulario572'));
                    }

                    //reviso si tiene rango remuneración asignado
                    if ($empleado->getRangoRemuneracion() == null) {
                        $this->get('session')->getFlashBag()->add(
                                'error', 'El empleado debe tener configurado el rango remuneración'
                        );
                        return $this->redirect($this->generateUrl('formulario572'));
                    }


                    $conceptosNoPisables = null;
                    $ajustes = null;
                    $otrosEmpleadores = null;
                    //revisar si tenia formulario 572 previamente cargado
                    $this->get('session')->set('formulario572Viejo', null);
                    $formulario572 = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->findOneBy(
                            array('empleado' => $empleado, 'anio' => (string) $archivo->periodo)
                    );

                    if ($formulario572) {
                        //agrego los conceptos que no se deben sobreescribir
                        $conceptosNoPisables = new ArrayCollection();
                        //obtengo los ajustes previamente importados
                        $ajustes = new ArrayCollection();
                        //obtengo las remuneraciones de otros empleadores previamente importados
                        $otrosEmpleadores = new ArrayCollection();
                        foreach ($formulario572->getConceptos() as $concepto) {
                            if (!($concepto->getConceptoGanancia()->getF572Sobreescribe())) {
                                $conceptosNoPisables->add($concepto);
                            }
                            if (($concepto->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO) || ($concepto->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO)) {
                                $ajustes->add($concepto);
                            }
                            if ($concepto->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR) {
                                $otrosEmpleadores->add($concepto);
                            }
                        }
                        $this->get('session')->set('formulario572Viejo', $formulario572->getId());
                    }
                    //crear F572 nuevo
                    $formulario572 = new Formulario572();
                    //seteamos el empleado
                    $formulario572->setEmpleado($empleado);
                    //seteamos la fecha
                    $formulario572->setFechaFormulario(new \Datetime((string) $archivo->fechaPresentacion));
                    //seteamos el anio
                    $formulario572->setAnio((string) $archivo->periodo);
                    // Armo el datetime del periodo
                    $dtPeriodoFechaDesde = \Datetime::createFromFormat('Y-m-d H:i:s', (string) $archivo->periodo . '-01-01 00:00:00');

                    //guardamos los conceptos no pisables si los había
                    if ($conceptosNoPisables != null) {
                        foreach ($conceptosNoPisables as $concepto) {
                            $conceptoF572NoPisable = new ConceptoFormulario572();
                            $conceptoF572NoPisable
                                    ->setFormulario572($formulario572)
                                    ->setMesDesde($concepto->getMesDesde())
                                    ->setMesHasta($concepto->getMesHasta())
                                    ->setConceptoGanancia($concepto->getConceptoGanancia())
                                    ->setMonto($concepto->getMonto());
                            $formulario572->addConcepto($conceptoF572NoPisable);
                        }
                    }

                    //guardamos los ajustes
                    if ($ajustes != null) {
                        foreach ($ajustes as $concepto) {
                            /* @var $concepto \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                            $conceptoF572NoPisable = new ConceptoFormulario572();
                            $detalleConceptoF572 = new DetalleConceptoFormulario572();
                            $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                            $detalleConceptoF572
                                    ->setCuit($concepto->getDetalleConceptoFormulario572()->getCuit())
                                    ->setDetalle($concepto->getDetalleConceptoFormulario572()->getDetalle());
                            $detalleConceptoF572Aplicado->setPeriodo($concepto->getDetalleConceptoFormulario572Aplicado()->getPeriodo())
                                    ->setMontoAplicado($concepto->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado())
                                    ->setAplicado($concepto->getDetalleConceptoFormulario572Aplicado()->getAplicado());
                            $conceptoF572NoPisable
                                    ->setFormulario572($formulario572)
                                    ->setMesDesde($concepto->getMesDesde())
                                    ->setMesHasta($concepto->getMesHasta())
                                    ->setConceptoGanancia($concepto->getConceptoGanancia())
                                    ->setMonto($concepto->getMonto())
                                    ->setDetalleConceptoFormulario572($detalleConceptoF572)
                                    ->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                            $formulario572->addConcepto($conceptoF572NoPisable);
                        }
                    }
                    //guardamos las remuneraciones de otro empleador
                    if ($otrosEmpleadores != null) {
                        foreach ($otrosEmpleadores as $concepto) {
                            /* @var $concepto \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                            $conceptoF572NoPisable = new ConceptoFormulario572();
                            $detalleConceptoF572 = new DetalleConceptoFormulario572();
                            $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                            $detalleConceptoF572
                                    ->setCuit($concepto->getDetalleConceptoFormulario572()->getCuit())
                                    ->setDetalle($concepto->getDetalleConceptoFormulario572()->getDetalle());
                            $detalleConceptoF572Aplicado->setPeriodo($concepto->getDetalleConceptoFormulario572Aplicado()->getPeriodo())
                                    ->setMontoAplicado($concepto->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado())
                                    ->setAplicado($concepto->getDetalleConceptoFormulario572Aplicado()->getAplicado());
                            $conceptoF572NoPisable
                                    ->setFormulario572($formulario572)
                                    ->setMesDesde($concepto->getMesDesde())
                                    ->setMesHasta($concepto->getMesHasta())
                                    ->setConceptoGanancia($concepto->getConceptoGanancia())
                                    ->setMonto($concepto->getMonto())
                                    ->setDetalleConceptoFormulario572($detalleConceptoF572)
                                    ->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                            $formulario572->addConcepto($conceptoF572NoPisable);
                        }
                    }

                    //Creamos los conceptos
                    //Cargas Familiares
                    if ($archivo->cargasFamilia->cargaFamilia !== null) {
                        foreach ($archivo->cargasFamilia->cargaFamilia as $cargaFamiliar) {
                            $conceptoF572 = new ConceptoFormulario572();
                            $detalleConceptoF572 = new DetalleConceptoFormulario572();
                            $detalleConceptoF572
                                    ->setCuit($this->formatearCuil($cargaFamiliar->nroDoc))
                                    ->setDetalle((string) $cargaFamiliar->apellido . ' ' . (string) $cargaFamiliar->nombre);
                            $conceptoF572
                                    ->setFormulario572($formulario572)
                                    ->setMesDesde((string) $cargaFamiliar->mesDesde)
                                    ->setMesHasta((string) $cargaFamiliar->mesHasta)
                                    ->setDetalleConceptoFormulario572($detalleConceptoF572);

                            $conceptoGanancia = null;
                            switch ((string) $cargaFamiliar->parentesco) {

                                /**
                                * Parentescos validos a partir del 2017:
                                * Codigo 		Descripcion
                                * 1				Cónyuge
                                * 3				Hijo/a Menor de 18 Años
                                * 30			Hijastro/a Menor de 18 Años
                                * 31			Hijo/a Incapacitado para el Trabajo
                                * 32			Hijastro/a Incapcacitado para el Trabajo
                                */

                                // conyuge
                                case '1':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_CONYUGE);
                                    break;
                                // hijos e hijastros...
                                case '3':
                                case '30':
                                case '31':
                                case '32':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_HIJOS);
                                    break;
                                //otras cargas
                                default:
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_OTRAS_CARGAS);
                                    break;
                            }

                            $topeConceptoGanancia = $em
                                ->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
                                ->findOneBy(
                                    array(
                                        'rangoRemuneracion' => $empleado->getRangoRemuneracion(), 
                                        'conceptoGanancia' => $conceptoGanancia, 
                                        'fechaDesde' => $dtPeriodoFechaDesde
                                        //'vigente' => 1
                                        )
                                    );
                            if (!$topeConceptoGanancia) {
                                $this->get('session')->getFlashBag()->add('error', 'No se encontro el concepto de cargas familiar, para el tope vigente.');
                                return $this->redirect($this->generateUrl('formulario572'));
                            }
                            $conceptoF572->setConceptoGanancia($conceptoGanancia);
                            $conceptoF572->setMonto(($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope());
                            $formulario572->addConcepto($conceptoF572);
                        }
                    }

                    $otrosEmpleadoresNuevos = new ArrayCollection();
                    $montoOtrosEmp = 0;
                    $retencionGananciasOtrosEmp = 0;

                    $jubilacionOtroEmpleador = 0;
                    $obraSocialOtroEmpleador = 0;
                    $sindicalOtroEmpleador = 0;
                    //Otros empleadores
                    if ($archivo->ganLiqOtrosEmpEnt->empEnt !== null) {
                         
                        foreach ($archivo->ganLiqOtrosEmpEnt->empEnt as $otroEmpleador) {
                            
                            foreach ($otroEmpleador->ingresosAportes as $ingresosAportes) {
                                
                                foreach ($ingresosAportes->ingAp as $ingreso) {
                                    
                                    if ($ingreso->segSoc != null && !empty($ingreso->segSoc)) {
                                        $conceptoGanancia = $em
                                            ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                                            ->findOneByCodigo572(ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR);
                                        
                                        $jubilacionOtroEmpleador = (float) $ingreso->segSoc;
                                        
                                        // Guardo el nuevo concepto ganancia "Aportes jubilatorios otros empleos"
                                        $conceptoF572 = new ConceptoFormulario572();
                                        $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                        $detalleConceptoF572
                                                ->setCuit($this->formatearCuil($otroEmpleador->cuit))
                                                ->setDetalle((string) $otroEmpleador->denominacion);
                                        $conceptoF572
                                                ->setFormulario572($formulario572)
                                                ->setMesDesde((string) $ingreso->attributes()[0])
                                                ->setMesHasta((string) $ingreso->attributes()[0]);
                                        $conceptoF572->setConceptoGanancia($conceptoGanancia);

                                        $conceptoF572->setMonto($jubilacionOtroEmpleador);
                                        $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                        $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                        $detalleConceptoF572Aplicado->setPeriodo($conceptoF572->getMesDesde())
                                                ->setAplicado(false);
                                        $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                        $otrosEmpleadoresNuevos->add($conceptoF572);
                                    }     
                                        
                                    if ($ingreso->obraSoc != null && !empty($ingreso->obraSoc)) {
                                        $conceptoGanancia = $em
                                            ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                                            ->findOneByCodigo572(ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR);
                                        
                                        $obraSocialOtroEmpleador = (float) $ingreso->obraSoc;
                                        
                                        // Guardo el nuevo concepto ganancia "Aportes obra social otros empleos"
                                        $conceptoF572 = new ConceptoFormulario572();
                                        $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                        $detalleConceptoF572
                                                ->setCuit($this->formatearCuil($otroEmpleador->cuit))
                                                ->setDetalle((string) $otroEmpleador->denominacion);
                                        $conceptoF572
                                                ->setFormulario572($formulario572)
                                                ->setMesDesde((string) $ingreso->attributes()[0])
                                                ->setMesHasta((string) $ingreso->attributes()[0]);
                                        $conceptoF572->setConceptoGanancia($conceptoGanancia);

                                        $conceptoF572->setMonto($obraSocialOtroEmpleador);
                                        $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                        $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                        $detalleConceptoF572Aplicado->setPeriodo($conceptoF572->getMesDesde())
                                                ->setAplicado(false);
                                        $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                        $otrosEmpleadoresNuevos->add($conceptoF572);
                                    }    
                                        
                                    if ($ingreso->sind != null && !empty($ingreso->sind)) {
                                        $conceptoGanancia = $em
                                            ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                                            ->findOneByCodigo572(ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR);
                                           
                                        $sindicalOtroEmpleador = (float) $ingreso->sind;
                                        
                                        // Guardo el nuevo concepto ganancia "Aportes obra social otros empleos"
                                        $conceptoF572 = new ConceptoFormulario572();
                                        $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                        $detalleConceptoF572
                                                ->setCuit($this->formatearCuil($otroEmpleador->cuit))
                                                ->setDetalle((string) $otroEmpleador->denominacion);
                                        $conceptoF572
                                                ->setFormulario572($formulario572)
                                                ->setMesDesde((string) $ingreso->attributes()[0])
                                                ->setMesHasta((string) $ingreso->attributes()[0]);
                                        $conceptoF572->setConceptoGanancia($conceptoGanancia);

                                        $conceptoF572->setMonto($sindicalOtroEmpleador);
                                        $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                        $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                        $detalleConceptoF572Aplicado->setPeriodo($conceptoF572->getMesDesde())
                                                ->setAplicado(false);
                                        $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                        $otrosEmpleadoresNuevos->add($conceptoF572);
                                    } 
                                    
                                    if ($ingreso->retGan != null && !empty($ingreso->retGan)) {
                                         $conceptoGanancia = $em
                                            ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                                            ->findOneByCodigo572(ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR);
                                           
                                        $retencionGananciasOtrosEmp = (float) $ingreso->retGan;
                                        
                                        // Guardo el nuevo concepto ganancia "Aportes obra social otros empleos"
                                        $conceptoF572 = new ConceptoFormulario572();
                                        $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                        $detalleConceptoF572
                                                ->setCuit($this->formatearCuil($otroEmpleador->cuit))
                                                ->setDetalle((string) $otroEmpleador->denominacion);
                                        $conceptoF572
                                                ->setFormulario572($formulario572)
                                                ->setMesDesde((string) $ingreso->attributes()[0])
                                                ->setMesHasta((string) $ingreso->attributes()[0]);
                                        $conceptoF572->setConceptoGanancia($conceptoGanancia);

                                        $conceptoF572->setMonto($retencionGananciasOtrosEmp);
                                        $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                        $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                        $detalleConceptoF572Aplicado->setPeriodo($conceptoF572->getMesDesde())
                                                ->setAplicado(false);
                                        $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                        $otrosEmpleadoresNuevos->add($conceptoF572);
                                    }
                                    
                                    // Monto otros empleadores...
                                    $conceptoGanancia = $em
                                            ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                                            ->findOneByCodigo572(ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
                                    
                                    $montoOtrosEmp = (float) $ingreso->ganBrut + (float) $ingreso->retribNoHab + (float) $ingreso->ajuste; // - (float) $ingreso->obraSoc - (float) $ingreso->segSoc - (float) $ingreso->sind;
                                    /**
                                    * A partir de la V1.7 (29/12/2016) se agregaron al siradig empleador,
                                    * elementos opcionales exeNoAlc y sac
                                    * El elemento sac se suma y exeNoAlc no hay que tenerlo en cuenta
                                    * gluis - 03/05/2017
                                    */
                                    $montoOtrosEmp += (float) $ingreso->sac;
                                    
                                    /** 
                                     * Le sumo al monto de otros empleados el elemento "horasExtGr"
                                     * gluis - 26/09/2017
                                     */
                                    $montoOtrosEmp += (float) $ingreso->horasExtGr;


                                    $conceptoF572 = new ConceptoFormulario572();
                                    $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                    $detalleConceptoF572
                                            ->setCuit($this->formatearCuil($otroEmpleador->cuit))
                                            ->setDetalle((string) $otroEmpleador->denominacion);
                                    $conceptoF572
                                            ->setFormulario572($formulario572)
                                            ->setMesDesde((string) $ingreso->attributes()[0])
                                            ->setMesHasta((string) $ingreso->attributes()[0]);
                                    $conceptoF572->setConceptoGanancia($conceptoGanancia);
                                    
                                    
                                    $conceptoF572->setMonto($montoOtrosEmp);
                                    $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                    $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                    $detalleConceptoF572Aplicado->setPeriodo($conceptoF572->getMesDesde())
                                            ->setAplicado(false);
                                    $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                    $otrosEmpleadoresNuevos->add($conceptoF572);
                                }
                            }
                        }
                    }

                    $otrosEmpleadoresResultante = new ArrayCollection();
                    foreach ($otrosEmpleadoresNuevos as $conceptoOtroEmpleador) {
                        /* @var $conceptoOtroEmpleador \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                        $conceptoGanancia = $conceptoOtroEmpleador->getConceptoGanancia();
                        $cuit = $conceptoOtroEmpleador->getDetalleConceptoFormulario572()->getCuit();
                        $periodo = $conceptoOtroEmpleador->getDetalleConceptoFormulario572Aplicado()->getPeriodo();
                        $monto = $conceptoOtroEmpleador->getMonto();
                        $encontrado = false;
                        foreach ($formulario572->getConceptos() as $concepto) {
                            if ($concepto->getConceptoGanancia() == $conceptoGanancia) {
                                if (($concepto->getDetalleConceptoFormulario572()->getCuit() == $cuit) && ($concepto->getDetalleConceptoFormulario572Aplicado()->getPeriodo() == $periodo)) {
                                    $encontrado = true;
                                    if ($concepto->getMonto() != $monto) {
                                        $concepto->setMonto($monto);
                                        $concepto->getDetalleConceptoFormulario572Aplicado()->setAplicado(false);
                                    }
                                }
                            }
                        }
                        if (!$encontrado) {
                            $otrosEmpleadoresResultante->add($conceptoOtroEmpleador);
                        }
                    }

                    foreach ($otrosEmpleadoresResultante as $conceptoEmpleador) {
                        $formulario572->addConcepto($conceptoEmpleador);
                    }

                    //Deducciones
                    if ($archivo->deducciones->deduccion !== null) {
                        foreach ($archivo->deducciones->deduccion as $deduccion) {
                            $ignorado = false;
                            $sinPeriodo = false;
                            $conceptoGanancia = null;
                            $aplicaTope = false;
                            switch ((string) $deduccion->attributes()[0]) {
                                //cuotas medico asistenciales
                                case '1':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL);
                                    break;
                                //primas de seguro
                                case '2':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO);
                                    break;
                                //donaciones
                                case '3':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_DONACIONES);
                                    break;
                                //hipotecarios
                                case '4':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_HIPOTECARIO);
                                    break;
                                //sepelio
                                case '5':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_SEPELIO);
                                    break;
                                //honorarios medicos
                                case '7':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA);
                                    break;
                                //domestico
                                case '8':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO);
                                    break;
                                //jubilatorios
                                case '99':
                                    if ($deduccion->detalles !== null) {
                                        if (((string) $deduccion->detalles->detalle[0]->attributes()[0] === 'motivo') && (((string) $deduccion->detalles->detalle[0]->attributes()[1] === '2') || ((string) $deduccion->detalles->detalle[0]->attributes()[1] === '1'))) {
                                            $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_JUBILATORIO);
                                            $sinPeriodo = true;
                                        } else {
                                            $ignorado = true;
                                        }
                                    } else {
                                        $ignorado = true;
                                    }
                                    break;
                                case '22':
                                    // Alquiler
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_ALQUILER);
                                    $aplicaTope = true;
                                    break;
                                default:
                                    $ignorado = true;
                            }
                            if (!$ignorado) {
                                if (!$sinPeriodo) {
                                    foreach ($deduccion->periodos->periodo as $periodo) {
                                        $conceptoF572 = new ConceptoFormulario572();
                                        if ($conceptoGanancia->getTieneDetalle()) {
                                            $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                            $detalleConceptoF572
                                                    ->setCuit($this->formatearCuil($deduccion->nroDoc))
                                                    ->setDetalle((string) $deduccion->denominacion);
                                            $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                        }

                                        $monto = (float) $periodo->attributes()[2];
                                        if ($aplicaTope) {
                                            $topeConceptoGanancia = $em
                                                ->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
                                                ->findOneBy(
                                                    array(
                                                        'rangoRemuneracion' => $empleado->getRangoRemuneracion(), 
                                                        'conceptoGanancia' => $conceptoGanancia, 
                                                        'fechaDesde' => $dtPeriodoFechaDesde,
                                                        //'vigente' => 1
                                                    )
                                                );

                                            $tope = $topeConceptoGanancia->getEsValorAnual() 
                                                ? $topeConceptoGanancia->getValorTope() / 12 
                                                : $topeConceptoGanancia->getValorTope();

                                            $monto = $monto > $tope ? $tope : $monto;
                                        }

                                        $conceptoF572
                                                ->setFormulario572($formulario572)
                                                ->setMesDesde((float) $periodo->attributes()[0])
                                                ->setMesHasta((float) $periodo->attributes()[1])
                                                ->setMonto($monto)
                                                ->setConceptoGanancia($conceptoGanancia);
                                        $formulario572->addConcepto($conceptoF572);
                                    }
                                } else {
                                    $conceptoF572 = new ConceptoFormulario572();
                                    if ($conceptoGanancia->getTieneDetalle()) {
                                        $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                        $detalleConceptoF572
                                                ->setCuit($this->formatearCuil($deduccion->nroDoc))
                                                ->setDetalle((string) $deduccion->denominacion);
                                        $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                    }
                                    $conceptoF572
                                            ->setFormulario572($formulario572)
                                            ->setMesDesde((float) $deduccion->detalles->detalle[1]->attributes()[1])
                                            ->setMesHasta((float) $deduccion->detalles->detalle[1]->attributes()[1])
                                            ->setMonto((float) $deduccion->montoTotal)
                                            ->setConceptoGanancia($conceptoGanancia);
                                    $formulario572->addConcepto($conceptoF572);
                                }
                            }
                        }
                    }

                    //Retenciones, percepciones y pagos
                    if ($archivo->retPerPagos->retPerPago !== null) {
                        foreach ($archivo->retPerPagos->retPerPago as $percepcion) {
                            $conceptoGanancia = null;
                            switch ((string) $percepcion->attributes()[0]) {
                                //Impuestos sobre Creditos y Debitos en cuenta Bancaria
                                case '6':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_CREDITOS_Y_DEBITOS);
                                    break;
                                //Retenciones y Percepciones Aduaneras
                                case '12':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_PERCEPCIONES_Y_RETENCIONES_ADUANERAS);
                                    break;
                                //Pago a Cuenta - Compras en el Exterior
                                case '13':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_COMPRAS_EN_EL_EXTERIOR);
                                    break;
                                //Impuesto sobre los Movimientos de Fondos Propios o de Terceros
                                case '14':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_LOS_MOVIMIENTOS);
                                    break;
                                //Pago a Cuenta - Compra de Paquetes Turisticos
                                case '15':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_COMPRA_DE_PAQUETES_TURISTICOS);
                                    break;
                                //Pago a Cuenta - Compra de Pasajes
                                case '16':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_COMPRA_DE_PASAJES);
                                    break;
                                //Pago a Cuenta - Compra de Moneda Extranjera para Turismo / Transf. al Exterior
                                case '17':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_COMPRA_DE_MONEDA);
                                    break;
                                //Pago a Cuenta - Adquisicion de moneda extranjera para tenencia de billetes extranjeros en el pais
                                case '18':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_ADQUISICION_DE_MONEDA);
                                    break;
                            }
                            foreach ($percepcion->periodos->periodo as $periodo) {
                                $conceptoF572 = new ConceptoFormulario572();
                                if ($conceptoGanancia->getTieneDetalle()) {
                                    $detalleConceptoF572 = new DetalleConceptoFormulario572();
                                    $detalleConceptoF572
                                            ->setCuit($this->formatearCuil($percepcion->nroDoc))
                                            ->setDetalle((string) $percepcion->denominacion);
                                    $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                                }
                                $conceptoF572
                                        ->setFormulario572($formulario572)
                                        ->setMesDesde((float) $periodo->attributes()[0])
                                        ->setMesHasta((float) $periodo->attributes()[1])
                                        ->setMonto((float) $periodo->attributes()[2])
                                        ->setConceptoGanancia($conceptoGanancia);
                                $formulario572->addConcepto($conceptoF572);
                            }
                        }
                    }

                    $ajustesNuevos = new ArrayCollection();
                    //Ajustes
                    if ($archivo->ajustes->ajuste !== null) {
                        foreach ($archivo->ajustes->ajuste as $ajuste) {
                            $conceptoF572 = new ConceptoFormulario572();
                            $conceptoF572
                                    ->setFormulario572($formulario572)
                                    ->setMesDesde(1)
                                    ->setMesHasta(1);
                            $conceptoGanancia = null;
                            switch ((string) $ajuste->attributes()[0]) {
                                //Ajustes de retroactivos de los empleadores
                                case '1':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
                                    break;
                                //Ajustes por Reintegros de Aportes efectuados por los Socios Protectores a las Sociedades de Garantia Reciproca
                                case '2':
                                    $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
                                    break;
                            }
                            $conceptoF572->setConceptoGanancia($conceptoGanancia);
                            $conceptoF572->setMonto((float) $ajuste->montoTotal);
                            $detalleConceptoF572 = new DetalleConceptoFormulario572();
                            $detalleConceptoF572
                                    ->setCuit($this->formatearCuil($ajuste->cuit))
                                    ->setDetalle((string) $ajuste->denominacion);
                            $conceptoF572->setDetalleConceptoFormulario572($detalleConceptoF572);
                            if (($ajuste->detalles !== null) && ( (string) $ajuste->detalles->detalle[0]->attributes()[0] === 'anio')) {
                                $detalleConceptoF572Aplicado = new DetalleConceptoFormulario572Aplicado();
                                $detalleConceptoF572Aplicado->setPeriodo((string) $ajuste->detalles->detalle[0]->attributes()[1])
                                        ->setAplicado(false);
                                $conceptoF572->setDetalleConceptoFormulario572Aplicado($detalleConceptoF572Aplicado);
                                $ajustesNuevos->add($conceptoF572);
                            }
                        }
                    }

                    $ajustesResultante = new ArrayCollection();
                    foreach ($ajustesNuevos as $conceptoAjuste) {
                        /* @var $conceptoAjuste \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                        $conceptoGanancia = $conceptoAjuste->getConceptoGanancia();
                        $cuit = $conceptoAjuste->getDetalleConceptoFormulario572()->getCuit();
                        $periodo = $conceptoAjuste->getDetalleConceptoFormulario572Aplicado()->getPeriodo();
                        $monto = $conceptoAjuste->getMonto();
                        $encontrado = false;
                        foreach ($formulario572->getConceptos() as $concepto) {
                            if ($concepto->getConceptoGanancia() == $conceptoGanancia) {
                                if (($concepto->getDetalleConceptoFormulario572()->getCuit() == $cuit) && ($concepto->getDetalleConceptoFormulario572Aplicado()->getPeriodo() == $periodo)) {
                                    $encontrado = true;
                                    if ($concepto->getMonto() != $monto) {
                                        $concepto->setMonto($monto);
                                        $concepto->getDetalleConceptoFormulario572Aplicado()->setAplicado(false);
                                    }
                                }
                            }
                        }
                        if ($encontrado) {
                            $ajustesResultante->add($conceptoAjuste);
                        }
                    }

                    foreach ($ajustesResultante as $conceptoAjuste) {
                        $formulario572->addConcepto($conceptoAjuste);
                    }

                    $this->get('session')->set('formulario572', $formulario572);
                    return $this->redirect($this->generateUrl('formulario572_importar_confirmar'));

                } else {
                    $this->get('session')->getFlashBag()->add(
                            'error', 'El archivo importado es inválido');
                    return $this->redirect($this->generateUrl('formulario572'));
                } // hasta aca
                
            } catch (ContextErrorException $exc) {
                

                $this->get('session')->getFlashBag()->add(
                        'error', 'El archivo xml importado tiene un error de estructura: ' . $exc->getMessage()
                );
                
            } catch (\Exception $e) {
                
                $this->get('session')->getFlashBag()->add(
                        'error', 'Hubo un error al importar el formulario 572.'
                );
            }
            
            
        } else {
            
            $this->get('session')->getFlashBag()->add(
                    'error', 'El archivo importado es incorrecto');
            
        }
        
        return $this->redirect($this->generateUrl('formulario572'));
    }

    /**
     * @Route("/importar/f572Confirmar", name="formulario572_importar_confirmar")
     * @Template("ADIFRecursosHumanosBundle:Formulario572:show_confirmar.html.twig")
     */
    public function importarF572Confirmar(Request $request) {

        $bread = $this->base_breadcrumbs;
        $bread['Formularios 572'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formulario572Viejo = null;

        if ($this->get('session')->get('formulario572Viejo') != null) {
            $formulario572Viejo = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($this->get('session')->get('formulario572Viejo'));
        }

        return array(
            'entity' => $this->get('session')->get('formulario572'),
            'entityVieja' => $formulario572Viejo,
            'breadcrumbs' => $bread,
            'page_title' => 'Confirmar importación de Formulario 572'
        );
    }

    /**
     * @Route("/importar/f572Confirmado", name="formulario572_importar_confirmado")
     */
    public function importarF572Confirmado(Request $request) {
        $bread = $this->base_breadcrumbs;
        $bread['Formularios 572'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());


        if ($this->get('session')->get('formulario572Viejo') != null) {
            $formulario572Viejo = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->find($this->get('session')->get('formulario572Viejo'));

            $em->remove($formulario572Viejo);
            $em->flush();

            $this->get('session')->remove('formulario572Viejo');
        }

        $formulario572 = $this->get('session')->get('formulario572');
        $empleado = $formulario572->getEmpleado()->getPersona()->getApellido() . ', ' . $formulario572->getEmpleado()->getPersona()->getNombre();
        $formulario572_mergeada = $em->merge($formulario572);


        foreach ($formulario572_mergeada->getConceptos() as $concepto) {

            if ($concepto->getDetalleConceptoFormulario572() != null) {
                $concepto->getDetalleConceptoFormulario572()->setConceptoFormulario572($concepto);
            }
            if ($concepto->getDetalleConceptoFormulario572Aplicado() != null) {
                $concepto->getDetalleConceptoFormulario572Aplicado()->setConceptoFormulario572($concepto);
            }
        }
		
        $em->persist($formulario572_mergeada);
        $em->flush();

        $this->get('session')->remove('formulario572');

        $this->get('session')->getFlashBag()->add(
                'success', 'Se importó el formulario 572 del empleado ' . $empleado . ' correctamente.'
        );
        return $this->redirect($this->generateUrl('formulario572'));
    }

    /*
     * se formatea el cuil de la forma ##-########-#
     */

    private function formatearCuil($cuil) {
        return substr($cuil, 0, 2) . '-' . substr($cuil, 2, 8) . '-' . substr($cuil, 10, 2);
    }

    /**
     * Finds and displays a Formulario572 entity.
     *
     * @Route("/empleado/{id}", name="formulario572_index_empleado")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Formulario572:indexEmpleado.html.twig")
     */
    public function indexF572EmpleadoAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);
        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }
        $formularios572 = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')->findByEmpleado($empleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = null;

        return array(
            'formulario572' => $formularios572,
            'empleado' => $empleado,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Formularios 572'
        );
    }

    /**
     * Tabla para Formularios572.
     *
     * @Route("/index_table/", name="formulario572_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formularios572 = null;

        if ($request->query->get('anio')) {

            $formularios572 = $em->getRepository('ADIFRecursosHumanosBundle:Formulario572')
                    ->findByAnio($request->query->get('anio'));
        }

        return $this->render('ADIFRecursosHumanosBundle:Formulario572:index_table.html.twig', array('entities' => $formularios572)
        );
    }

    /**
     * 
     * @param Formulario572 $formulario572
     * @return array
     */
    private function totalizarPorConcepto(Formulario572 $formulario572) {

        $totalPorConcepto = [];

        foreach ($formulario572->getConceptos() as $conceptoFormulario572) {
            /* @var $conceptoFormulario572 \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
            if (!isset($totalPorConcepto[$conceptoFormulario572->getConceptoGanancia()->getId()])) {
                $totalPorConcepto[$conceptoFormulario572->getConceptoGanancia()->getId()] = array(
                    'id' => $conceptoFormulario572->getConceptoGanancia()->getId(),
                    'concepto' => $conceptoFormulario572->getConceptoGanancia()->__toString(),
                    'monto' => $conceptoFormulario572->getMonto() * (($conceptoFormulario572->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_SAC) ? 1 : ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1))
                );
            } else {
                $totalPorConcepto[$conceptoFormulario572->getConceptoGanancia()->getId()]['monto'] += $conceptoFormulario572->getMonto() * (($conceptoFormulario572->getConceptoGanancia()->getCodigo572() == ConceptoGanancia::__CODIGO_SAC) ? 1 : ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1));
            }
        }

        return $totalPorConcepto;
    }

}
