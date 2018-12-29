<?php

namespace ADIF\BaseBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Manuel Becerra
 * created 29/08/2014
 * 
 * EntityManagementGuesser
 * 
 */
class EntityManagementGuesser {

    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * 
     * @param \Symfony\Bundle\FrameworkBundle\Controller\Controller $entity
     * @return \ADIF\AutenticacionBundle\Service\EntityManagementGuesser
     */
    public function initialize(Controller $entity) {

        $this->reflector = new \ReflectionClass(get_class($entity));

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getBundleName() {
        return ($p1 = strpos($ns = $this->getNamespace(), '\\')) === false ? $ns :
                substr($ns, 0, ($p2 = strpos($ns, '\\', $p1 + 1)) === false ? strlen($ns) : $p2);
    }

    /**
     *
     * @return string
     */
    public function getBundleShortName() {
        return str_replace('\\', '', $this->getBundleName());
    }

    /**
     *
     * @return string
     */
    public function getNamespace() {
        return str_replace("Proxies\\__CG__\\", "", $this->reflector->getNamespaceName());
    }

    /**
     *
     * @return string
     */
    public function getShortName() {
        return $this->reflector->getShortName();
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->reflector->getName();
    }

    /**
     *
     * @return string
     */
    public function guessEntityNamespace() {
        return ($pos = strrpos($ns = $this->getNamespace(), '\\')) === false ? $ns : sprintf("%s\%s", substr($ns, 0, $pos), 'Entity');
    }

    /**
     *
     * @return string
     */
    public function guessEntityShortName() {
        return ($pos = strpos($short = $this->getShortName(), 'Controller')) === false ? $short :
                substr($short, 0, $pos);
    }

    /**
     *
     * @return string
     */
    public function guessEntityName() {
        return sprintf('%s\\%s', $this->guessEntityNamespace(), $this->guessEntityShortName());
    }

    /**
     *
     * @return string
     */
    public function guessRepositoryNamespace() {
        return ($p = strrpos($ns = $this->getNamespace(), '\\')) === false ? $ns : sprintf("%s\Repository", substr($ns, 0, $p));
    }

    /**
     *
     * @return string
     */
    public function guessRepositoryShortName() {
        return sprintf(
                '%sRepository', ($pos = strpos($s = $this->getShortName(), 'Controller')) === false ? $s : substr($s, 0, $pos)
        );
    }

    /**
     *
     * @return string
     */
    public function guessRepositoryName() {
        return sprintf('%s\\%s', $this->guessRepositoryNamespace(), $this->guessRepositoryShortName());
    }

    /**
     *
     * @return string
     */
    public function guessFormTypeNamespace() {
        return ($p = strrpos($ns = $this->getNamespace(), '\\')) === false ? $ns : sprintf("%s\Form\Type", substr($ns, 0, $p));
    }

    /**
     *
     * @return string
     */
    public function guessFormTypeShortName() {
        return sprintf(
                '%sFormType', ($pos = strpos($s = $this->getShortName(), 'Controller')) === false ? $s : substr($s, 0, $pos)
        );
    }

    /**
     *
     * @return string
     */
    public function guessFormTypeName() {
        return sprintf('%s\%s', $this->guessFormTypeNamespace(), $this->guessFormTypeShortName());
    }

    /**
     * 
     * @return type
     */
    public function getEntityPrefix() {

        $arr_path = explode('\\', $this->getNamespace());

        $res = implode('\\', array_slice($arr_path, 3)) . '\\';

        return $res == '\\' ? '' : $res;
    }

}
