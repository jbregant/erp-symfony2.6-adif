<?php

namespace ADIF\ContableBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Manuel Becerra
 * created 02/12/2015
 * 
 * Listener de los eventos "persist" ejecutados sobre 
 * aquellas clases que sean un Comprobante.
 */
class ComprobanteListener {

    /**
     * CLASE_COMPROBANTE
     */
    const CLASE_COMPROBANTE = 'ADIF\ContableBundle\Entity\Comprobante';

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
        if ($this->esComprobante($args->getEntity())) {
            $this->checkEjercicioComprobante($args->getEntity());
        }
    }

    /**
     * Retorna si la entidad recibida como parámetro es o no un Comprobante.
     * 
     * @param type $entidad
     * @return type bool
     */
    private function esComprobante($entidad) {

        return is_a($entidad, self::CLASE_COMPROBANTE);
    }

    /**
     * 
     * @param type $entidad
     */
    private function checkEjercicioComprobante($entidad) {

        // Si el comprobante tiene la fecha seteada:
        if ($entidad->getFechaComprobante() != null) {

            $session = $this->_container->get("session");

            $ejercicioContableEnCurso = $session->get('ejercicio_contable');

            $ejercicioComprobante = $entidad->getFechaComprobante()->format('Y');

            if ($ejercicioContableEnCurso && $ejercicioContableEnCurso != $ejercicioComprobante) {

                $mensaje = 'El ejercicio del comprobante "' . $ejercicioComprobante
                        . '" no coincide con el ejercicio de trabajo seleccionado "' . $ejercicioContableEnCurso . '".';

                $session->getFlashBag()->add('warning', $mensaje);
            }
        }
    }

}
