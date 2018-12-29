<?php

namespace ADIF\BaseBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * GeneralController
 *
 * @author Manuel Becerra
 */
class GeneralController extends Controller {

    /**
     * INDEX_ACTION
     */
    const INDEX_ACTION = 'index';

    /**
     * DELETE_SUFFIX
     */
    const DELETE_SUFFIX = '_delete';

    /**
     *
     * @var type 
     */
    private $associationTypeArray = array(ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY);

    /**
     * 
     * @param type $id
     * @param type $indexParams
     * @return type
     * @throws type
     */
    public function baseDeleteAction($id, $indexParams = array()) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $guesser = $this->getGuesser($this);

        $entityName = $guesser->getBundleShortName()
                . ':'
                . $guesser->getEntityPrefix()
                . $guesser->guessEntityShortName();

        $entity = $em->getRepository($entityName)->find($id);

        if (!$entity) {

            $entityShortName = $guesser->guessEntityShortName();

            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $entityShortName . '.');
        }

        $error = false;

        if ($this->validateGeneralDeleteByEntityId($id, $this)) {

            $this->customRemove($em, $entity);

            try {
                $em->flush();
            } //.
            catch (DBALException $e) {

                $error = true;

                // throw $e;
            }
        } //.
        else {
            $error = true;
        }

        // Si hubo un error
        if ($error) {
            $this->getRequest()->attributes->set('form-error', true);

            $this->get('session')->getFlashBag()
                    ->set('error', $this->getSessionMessage());
        }

        $indexPath = $this->getIndexPath();

        return $this->redirect($this->generateUrl($indexPath, $indexParams));
    }

    /**
     * Valida que la entidad obtenida a partir del parámetro 
     * <code>$id</code> no sea referenciada por otras entidades.
     * 
     * @param type $id
     * @param type $controller
     * @return type
     */
    public function validateGeneralDeleteByEntityId($id, $controller) {

        $isValid = $this->validateLocalDeleteById($id);

        if ($isValid) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $guesser = $this->getGuesser($controller);

            $entityName = $guesser->getBundleShortName()
                    . ':'
                    . $guesser->getEntityPrefix()
                    . $guesser->guessEntityShortName();

            $entity = $em->getRepository($entityName)->find($id);

            $isValid = $this->validateGeneralDeleteByEntity($entity);
        }

        return $isValid;
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        return true;
    }

    /**
     * 
     * @return string
     */
    public function getIndexPath() {

        $routeName = $this->getRequest()->get('_route');

        $indexPath = str_replace(self::DELETE_SUFFIX, '', $routeName);

        return $indexPath;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la entidad '
                . 'ya que es referenciada por otras entidades.';
    }

    /**
     * 
     * @param type $em
     * @param type $entity
     */
    public function customRemove($em, $entity) {

        $em->remove($entity);
    }

    /**
     * Valida que la entidad recibida como parámetro no sea referenciada 
     * por otras entidades.
     * 
     * @param type $entity
     * @return boolean
     */
    public function validateGeneralDeleteByEntity($entity) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $manager = $em->getClassMetadata(get_class($entity));

        $isValid = true;

        foreach ($manager->associationMappings as $field) {

            if (in_array($field['type'], $this->associationTypeArray)) {

                $reflProp = $manager->reflClass->getProperty($field['fieldName']);
                $reflProp->setAccessible(true);

                $property = $reflProp->getValue($entity);

                if (null != $property && count($property) > 0) {
                    $isValid = false;
                    break;
                }
            }
        }

        return $isValid;
    }

    /**
     * 
     * @param type $controller
     * @return type
     */
    private function getGuesser($controller) {
        return $this->container
                        ->get('adif.base.entity_management_guesser')
                        ->initialize($controller);
    }
	
	public function addSuccessFlash($message)
	{
		parent::addFlash('success', $message);
	}
	
	public function addErrorFlash($message)
	{
		parent::addFlash('error', $message);
	}
	
	public function addWarningFlash($message)
	{
		parent::addFlash('warning', $message);
	}

}
