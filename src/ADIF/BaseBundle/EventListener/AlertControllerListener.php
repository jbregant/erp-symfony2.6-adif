<?php

namespace ADIF\BaseBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FinishRequestEvent;

/**
 * AlertControllerListener
 *
 * @author Manuel Becerra
 * created 03/07/2014
 * 
 * Listener de las acciones "createAction", "updateAction" y "deleteAction" ejecutados sobre 
 * aquellos Controllers que implementan AlertControllerInterface.
 */
class AlertControllerListener {

    /**
     * CONTROLLER_INTERFACE_CLASS
     */
    const CONTROLLER_INTERFACE_CLASS = 'ADIF\BaseBundle\Controller\AlertControllerInterface';

    /**
     * ACCION_CREATE
     */
    const ACCION_CREATE = "createAction";

    /**
     * ACCION_UPDATE
     */
    const ACCION_UPDATE = "updateAction";

    /**
     * ACCION_DELETE
     */
    const ACCION_DELETE = "deleteAction";

    /**
     * ACCION_BASE_DELETE
     */
    const ACCION_BASE_DELETE = "baseDeleteAction";

    /**
     *
     * @var type 
     */
    protected $acciones = array(
        AlertControllerListener::ACCION_CREATE,
        AlertControllerListener::ACCION_UPDATE,
        AlertControllerListener::ACCION_DELETE,
        AlertControllerListener::ACCION_BASE_DELETE
    );

    /**
     * 
     * @param \Symfony\Component\HttpKernel\Event\FinishRequestEvent $event
     * @return type
     */
    public function onKernelFinishRequest(FinishRequestEvent $event) {

        $controllerAccion = $event->getRequest()->attributes->get('_controller');

        if (count(explode("::", $controllerAccion)) == 1) {
            return;
        }

        list($controllerName, $nombreAccion) = explode("::", $controllerAccion);

        if (is_a($controllerName, AlertControllerListener::CONTROLLER_INTERFACE_CLASS, true)) {

            if (in_array($nombreAccion, $this->acciones)) {

                $error = $event->getRequest()->attributes->get('form-error');

                // Si el formulario no tiene errores
                if (!$error) {

                    if ($nombreAccion == AlertControllerListener::ACCION_CREATE) {
                        $event->getRequest()->getSession()->getFlashBag()
                                ->add('success', 'El alta se realizó con éxito.');
                    } //.
                    else if ($nombreAccion == AlertControllerListener::ACCION_UPDATE) {
                        $event->getRequest()->getSession()->getFlashBag()
                                ->add('success', 'La actualización se realizó con éxito.');
                    } //.
                    else if ($nombreAccion == AlertControllerListener::ACCION_DELETE ||
                            $nombreAccion == AlertControllerListener::ACCION_BASE_DELETE) {
                        $event->getRequest()->getSession()->getFlashBag()
                                ->add('success', 'La eliminación se realizó con éxito.');
                    }
                }
            }
        }
    }

}
