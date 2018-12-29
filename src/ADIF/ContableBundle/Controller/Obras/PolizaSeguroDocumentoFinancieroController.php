<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * PolizaSeguroDocumentoFinancieroController.
 *
 * 
 * @Route("/polizadocumentofinanciero")
 */
class PolizaSeguroDocumentoFinancieroController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'P&oacute;lizas' => $this->generateUrl('polizadocumentofinanciero')
        );
    }

    /**
     * Lists all PolizaSeguroDocumentoFinanciero entities.
     *
     * @Route("/", name="polizadocumentofinanciero")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;lizas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'P&oacute;lizas',
            'page_info' => 'Lista de p&oacute;lizas'
        );
    }

    /**
     * Devuelve todas las polizas de las obras y doc. financieros.
     *
     * @Route("/index_table/", name="polizadocumentofinanciero_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        /* */

        $polizasDocumentofinanciero = $em->getRepository('ADIFContableBundle:Obras\PolizaSeguroDocumentoFinanciero')
                ->createQueryBuilder('p')
                ->innerJoin('p.documentoFinanciero', 'df')
                ->where('df.fechaAnulacion IS NULL')
                ->orderBy('p.id', 'DESC')
                ->getQuery()
				//->setMaxResults(5)
                ->getResult();
				
		$polizasObras = $em->getRepository('ADIFContableBundle:Obras\PolizaSeguroObra')
			->createQueryBuilder('p')
			->innerJoin('p.tramo', 't')
			->orderBy('p.id', 'DESC')
			->getQuery()
			//->setMaxResults(5)
			->getResult();
			
		$entities = array_merge($polizasDocumentofinanciero, $polizasObras);
		
		// \Doctrine\Common\Util\Debug::dump( $entities ); exit; 
			
        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;lizas'] = null;

        return $this->render('ADIFContableBundle:Obras/PolizaSeguroDocumentoFinanciero:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Finds and displays a PolizaSeguroDocumentoFinanciero entity.
     *
     * @Route("/{id}", name="polizadocumentofinanciero_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\PolizaSeguroDocumentoFinanciero')
                ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PolizaSeguroDocumentoFinanciero.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;liza'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver p&oacute;liza'
        );
    }

}
