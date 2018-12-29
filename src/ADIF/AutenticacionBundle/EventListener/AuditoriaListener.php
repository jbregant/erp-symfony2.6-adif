<?php

namespace ADIF\AutenticacionBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Manuel Becerra
 * created 29/09/2015
 * 
 * Listener de los eventos "persist" ejecutados sobre 
 * aquellas clases que implementen BaseAuditable.
 */
class AuditoriaListener {

    /**
     * CLASE_BASE_AUDITABLE
     */
    const CLASE_BASE_AUDITABLE = 'ADIF\AutenticacionBundle\Entity\BaseAuditable';

    /**
     * CLASE_USUARIO
     */
    const CLASE_USUARIO = 'ADIF\AutenticacionBundle\Entity\Usuario';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {

        $this->_container = $container;
    }

    /**
     * Listener del evento "prePersist".
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return boolean
     */
    public function prePersist(LifecycleEventArgs $args) {
        if ($this->esAuditable($args->getEntity())) {
            $this->setUsuarioCreacion($args->getEntity());
        }
    }

    /**
     * Listener del evento "preUpdate".
     * 
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs  $args
     * @return boolean
     */
    public function preUpdate(PreUpdateEventArgs $args) {
        if ($this->esAuditable($args->getEntity())) {
            $this->setUsuarioUltimaModificacion($args->getEntity());
        }
    }

    /**
     * Retorna si la entidad recibida como parámetro es o no auditable.
     * 
     * @param type $entidad
     * @return type bool
     */
    private function esAuditable($entidad) {

        return is_a($entidad, self::CLASE_BASE_AUDITABLE) && !is_a($entidad, self::CLASE_USUARIO);
    }

    /**
     * 
     * @param type $entidad
     */
    private function setUsuarioCreacion($entidad) {

        if (null != $this->getUser()) {
            $entidad->setUsuarioCreacion($this->getUser());
        }
    }

    /**
     * 
     * @param type $entidad
     */
    private function setUsuarioUltimaModificacion($entidad) {

        if (null != $this->getUser()) {
            $entidad->setUsuarioUltimaModificacion($this->getUser());
        }
    }

    /**
     * 
     * @return type
     */
    private function getUser() {

        $usuario = null;

        $token = $this->_container->get('security.context')->getToken();

        if (null != $token) {

            if (is_a($token->getUser(), self::CLASE_USUARIO)) {

                $usuario = $token->getUser();
            }
        }

        return $usuario;
    }

}
