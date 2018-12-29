<?php

namespace ADIF\AutenticacionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * GrupoController.
 *
 * @Route("/grupos")
 * @Security("has_role('ROLE_MENU_SEGURIDAD')")
 */
class GrupoController extends BaseController 
{
    /**
     * Tabla para Grupo.
     *
     * @Route("/index_table/", name="grupos_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFAutenticacionBundle:Grupo')->findAll();

        return $this->render('ADIFAutenticacionBundle:Grupo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
	
	/**
     * Devuelve el select html armado filtrando por empresa
     *
     * @Route("/get_select_grupos_by_empresa_and_usuario", name="get_grupos_by_empresa")
     * @Method("POST")
     */
	public function getSelectGruposByEmpresaAction(Request $request)
	{
		$idEmpresa = $request->get('idEmpresa');
		$idUsuario = $request->get('idUsuario');
        $idSelect = $request->get('idSelect');

		$res['select'] = $this->getSelect($idEmpresa, $idUsuario, $idSelect);
		return new JsonResponse($res);
	}
	
    private function getSelect($idEmpresa, $idUsuario, $idSelect)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
		$gruposTodos = $em->getRepository('ADIFAutenticacionBundle:Grupo')->findAll();
        $gruposUsuarioEmpresa = $em->getRepository('ADIFAutenticacionBundle:Usuario')
                ->getGruposByIdUsuarioAndIdEmpresa($idUsuario, $idEmpresa);
        
		//\Doctrine\Common\Util\Debug::dump( $gruposEmpresa ); exit;
		$select = '<select id="' . $idSelect . '" ';
		$select .= 'class="form-control choice" name="adif_autenticacionbundle_usuario[groups][]" ';
		$select .= 'required="required" placeholder="Seleccione los grupos aquí." multiple="multiple" >';
		
		$selected = array();
		
		foreach($gruposTodos as $grupoT) {
			
			$selected[$grupoT->getName()] = '';
			
			foreach($gruposUsuarioEmpresa as $grupoE) {
				
				if ($grupoT->getId() == $grupoE['id_grupo']) {
							
                    $selected[$grupoT->getName()] = ' selected="selected" ';
							
                    break 1;
				}
			}
			
			$select .= '<option value="' . $grupoT->getId() . '"' . $selected[$grupoT->getName()] . '>' . $grupoT->getName() . '</option>';
		}
		
		$select .= '</select>';
        
        return $select;
    }
}
