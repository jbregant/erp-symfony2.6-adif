<?php

namespace ADIF\AutenticacionBundle\EventListener;

use ADIF\AutenticacionBundle\Entity\Logger;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger as MonologLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Console\ConsoleEvents;

/**
 * @author Manuel Becerra
 * created 30/06/2014
 *
 * Listener de los eventos "persist", "update" y "remove" ejecutados sobre
 * aquellas clases que implementen BaseAuditable.
 */
class LoggingListener {

    /**
     * ACCION_CREACION
     */
    const ACCION_CREACION = "Creacion";

    /**
     * ACCION_ACTUALIZACION
     */
    const ACCION_ACTUALIZACION = "Actualizacion";

    /**
     * ACCION_ELIMINACION
     */
    const ACCION_ELIMINACION = "Eliminacion";

    /**
     * CLASE_BASE_AUDITABLE
     */
    const CLASE_BASE_AUDITABLE = 'ADIF\AutenticacionBundle\Entity\BaseAuditable';

    /**
     * CLASE_LOGGER
     */
    const CLASE_LOGGER = 'ADIF\AutenticacionBundle\Entity\Logger';

    /**
     * CLASE_USUARIO
     */
    const CLASE_USUARIO = 'ADIF\AutenticacionBundle\Entity\Usuario';

    /**
     * BASE_CONTROLLER
     */
    const BASE_CONTROLLER = '\\Controller\\BaseController';

    /**
     * CSV_SEPARATOR
     */
    const CSV_SEPARATOR = '||';

    /**
     * WHITESPACE
     */
    const WHITESPACE = ' ';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    /**
     *
     * @var type \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $_registry;

    /**
     *
     * @var MonologLogger
     */
    protected $_logger;

    /**
     * Constructor
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(Registry $registry, ContainerInterface $container) {
        $this->_registry = $registry;
        $this->_container = $container;

        $this->_logger = new MonologLogger('log_general');

        $monologFormat = "%message%\n";

        $dateFormat = "Y/m/d";

        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

        $streamHandler = new StreamHandler(
                $this->_container->get('kernel')->getRootDir()
                . '/logs/log_general_'
                . date('Y_m_d')
                . '.csv', MonologLogger::INFO
        );

        $streamHandler->setFormatter($monologLineFormat);

        $this->_logger->pushHandler($streamHandler);
    }

    /**
     * Listener del evento "prePersist".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return boolean
     */
    public function prePersist(LifecycleEventArgs $args) {
        if ($this->esAuditable($args->getEntity())) {
            $this->setSQLLogger($args->getEntity());
        }
    }

    /**
     * Listener del evento "postPersist".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return boolean
     */
    public function postPersist(LifecycleEventArgs $args) {

        $entidad = $args->getEntity();

        if ($this->esAuditable($entidad)) {

            foreach ($this->getQueries($entidad) as $query) {

                $explodeResult = explode($this::WHITESPACE, $query['sql']);

                $nombreTabla = $explodeResult[2];

                if ($this->getNombreTabla($entidad) == $nombreTabla) {

                    $parametrosQuery = $this->getParametrosQuery($entidad, $query);

                    $queryString = $this->agregarParametrosAQuery($query['sql'], $parametrosQuery);

                    $this->chequearLog($args, LoggingListener::ACCION_CREACION, $queryString, null);
                }
            }
        }

        return true;
    }

    /**
     * Listener del evento "preUpdate".
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs  $args
     * @return boolean
     */
    public function preUpdate(PreUpdateEventArgs $args) {

        if ($this->esAuditable($args->getEntity())) {

            $nombreTabla = $this->getNombreTabla($args->getEntity());

            $query = 'UPDATE' . $this::WHITESPACE .
                    $nombreTabla . $this::WHITESPACE .
                    'SET' . $this::WHITESPACE;

            $index = 0;

            foreach ($args->getEntityChangeSet() as $nombreVariable => $variable) {

                if ($args->hasChangedField($nombreVariable)) {

                    if (0 !== $index) {
                        $query .= "," . $this::WHITESPACE;
                    }

                    $valorViejo = $this->getTipoVariable($args->getEntity(), $args->getOldValue($nombreVariable));

                    $valorNuevo = $this->getTipoVariable($args->getEntity(), $args->getNewValue($nombreVariable));

                    $query .= $nombreVariable . $this::WHITESPACE . '=' .
                            $this::WHITESPACE . $valorNuevo . $this::WHITESPACE .
                            '(' . $valorViejo . ')';
                }

                $index++;
            }

            $query .= $this::WHITESPACE . 'WHERE' . $this::WHITESPACE .
                    'id' . $this::WHITESPACE . '=' . $this::WHITESPACE .
                    $args->getEntity()->getId();

            $this->_container->get('session')->set('update_query', $query);
        }
    }

    /**
     * Listener del evento "postUpdate".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args) {

        if ($this->esAuditable($args->getEntity())) {

            $query = $this->_container->get('session')->get('update_query');

            $this->chequearLog($args, LoggingListener::ACCION_ACTUALIZACION, $query, null);
        }
    }

    /**
     * Listener del evento "preSoftDelete".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args) {

        $this->preRemove($args);
    }

    /**
     * Listener del evento "preRemove".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return boolean
     */
    public function preRemove(LifecycleEventArgs $args) {

        if ($this->esAuditable($args->getEntity())) {

            $this->setSQLLogger($args->getEntity());

            $this->_container->get("session")->set('entity_remove', $args->getEntity());
            $this->_container->get("session")->set('entity_remove_id', $args->getEntity()->getId());
        }

        return true;
    }

    /**
     * Listener del evento "postSoftDelete".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args) {

        if ($this->esAuditable($args->getEntity())) {

            $session = $this->_container->get("session");

            $entidad = $session->get('entity_remove');

            if (!empty($entidad)) {

                $nombreTabla = $this->getNombreTabla($entidad);

                $queryString = 'DELETE FROM' . $this::WHITESPACE . $nombreTabla .
                        $this::WHITESPACE . 'WHERE' . $this::WHITESPACE .
                        "id" . $this::WHITESPACE . '=' . $this::WHITESPACE .
                        $session->get('entity_remove_id');

                $this->chequearLog($args, LoggingListener::ACCION_ELIMINACION, $queryString);

                $session->remove('entity_remove');
                $session->remove('entity_remove_id');
            }
        }
    }

    /**
     * Listener del evento "postRemove".
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return boolean
     */
    public function postRemove(LifecycleEventArgs $args) {

        if ($this->esAuditable($args->getEntity())) {

            $session = $this->_container->get("session");

            $entidad = $session->get('entity_remove');

            if (!empty($entidad)) {

                foreach ($this->getQueries($entidad) as $query) {

                    $parametrosQuery = $this->getParametrosQuery($entidad, $query);

                    $queryString = str_replace("?", $parametrosQuery, $query['sql']);

                    $this->chequearLog($args, LoggingListener::ACCION_ELIMINACION, $queryString);

                    $session->remove('entity_remove');
                    // $session->remove('entity_remove_id');
                }
            }
        }
    }

    /**
     * Chequea si el log está en condiciones de persistirse.
     *
     * @param type $args
     * @param type $accion
     * @param type $query
     * @param type $observacion
     * @return boolean
     */
    private function chequearLog($args, $accion, $query = null, $observacion = null) {

        if (is_a($args->getEntity(), LoggingListener::CLASE_LOGGER)) {
            return false;
        }

        $entidad = $args->getEntity();

        if ($this->esAuditable($entidad)) {
            $this->agregarLog($entidad, $accion, $query, $observacion);
        }
    }

    /**
     * Agrega el log en la base de datos.
     *
     * @param type $entidad
     * @param type $accion
     * @param type $query
     * @param type $observacion
     */
    private function agregarLog($entidad, $accion, $query = null, $observacion = null) {

        $token = $this->_container->get('security.context')->getToken();
        $usuario = null;

        if (null != $token) {
            $usuario = $token->getUser();
        }

        $logger = new Logger();

        $logger->setClaseEntidad(ClassUtils::getClass($entidad));
        $logger->setQuery($query);

        if (is_a($usuario, LoggingListener::CLASE_USUARIO)) {
            $logger->setUsuario($usuario);
        }

        $logger->setAccion($accion);
        $logger->setFecha(new \DateTime());
        $logger->setObservacion($observacion);

        if (LoggingListener::ACCION_ELIMINACION === $accion) {
            $logger->setIdEntidad($this->_container->get('session')->get('entity_remove_id'));
        } else {
            $logger->setIdEntidad($entidad->getId());
        }

//      $entityManager = $this->getEntityManagerByClass($logger);
//      $entityManager->persist($logger);
//      $entityManager->flush();

        $this->setLoggerInfo($logger);
    }

    /**
     * Setea el SQLLogger
     *
     * @param type $entidad
     */
    private function setSQLLogger($entidad) {

        $entityManager = $this->getEntityManagerByClass($entidad);

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(new DebugStack());
    }

    /**
     * Retorna el SQLLogger
     *
     * @param type $entidad
     * @return type
     */
    private function getSQLLogger($entidad) {

        $entityManager = $this->getEntityManagerByClass($entidad);

        return $entityManager->getConnection()->getConfiguration()->getSQLLogger();
    }

    /**
     * Retorna los parámetros de la query recibida como parámetro.
     *
     * @param type $entidad
     * @param type $query
     * @return string|null
     */
    private function getParametrosQuery($entidad, $query) {

        $parametrosQuery = "";
        $parametrosArray = $query['params'];

        $index = 0;

        $count = count($parametrosArray);

        if ($count > 0) {

            foreach ($parametrosArray as $item) {

                if (true === is_array($item)) {
                    $parametrosQuery .= 'Array';
                } //.
                else {
                    $parametrosQuery .= $this->getTipoVariable($entidad, $item);
                }

                if ($index != $count - 1) {
                    $parametrosQuery .= "," . $this::WHITESPACE;
                }

                $index++;
            }

            return $parametrosQuery;
        }

        return null;
    }

    /**
     * Retorna el nombre de la tabla relacionada a la entidad recibida como parámetro.
     *
     * @param type $entidad
     * @return type
     */
    private function getNombreTabla($entidad) {

        $guesser = $this->getGuesser($entidad);

        $entityManager = $this->getEntityManagerByClass($entidad);
        return $entityManager->getClassMetadata($guesser->getBundleShortName()
                        . ':' . $guesser->getEntityPrefix() . $guesser->getShortName())->getTableName();
    }

    /**
     * Retorna el tipo de variable del valor recibido como parámetro.
     *
     * @param type $entidad
     * @param $valor
     * @return string
     */
    private function getTipoVariable($entidad, $valor) {

        if (true ===
                is_array($valor)) {
            return 'Array';
        } //.
        else if (true === is_null($valor)) {
            return "NULL";
        } //.
        else if ($valor instanceof \DateTime) {
            return $valor->format('d-m-Y H:i:s');
        } //.
        else if (true === is_bool($valor)) {
            switch ($valor) {
                case '1' :
                    return "1";
                case '0':
                    return "0";
            }
        } else if (true === is_object($valor)) {
            if (method_exists(ClassUtils::getClass($entidad), 'getId')) {
                return $valor->getId();
            } //.
            else {
                return '?';
            }
        }

        return $valor;
    }

    /**
     * Retorna si la entidad recibida como parámetro es o no auditable.
     *
     * @param type $entidad
     * @return type bool
     */
    private function esAuditable($entidad) {
        return is_a($entidad, LoggingListener::CLASE_BASE_AUDITABLE);
    }

    /**
     * Agrega los parámetros recibidos como parámetro a la query.
     *
     * @param type $queryString
     * @param type $parametros
     */
    private function agregarParametrosAQuery($queryString, $parametros) {

        $array = explode(',', $parametros);

        foreach ($array as $parametro) {
            $queryString = preg_replace('/\?/', $parametro, $queryString, 1);
        }

        return $queryString;
    }

    /**
     * Retorna las queries almacenadas por el SQLLogger.
     *
     * @param type $entidad
     * @return type
     */ private function getQueries($entidad) {

        $queries = $this->
                        getSQLLogger($entidad)->queries;

        $tempArray = [];

        foreach ($queries as $query) {

            if (array_key_exists('sql', $query)) {

                if (false === strpos($query['sql'], 'START')) {
                    $tempArray[] = $query;
                }
            }
        }

        return $tempArray;
    }

    /**
     *
     * @param type $entidad
     * @return type
     */
    private function getGuesser($entidad) {
        return $this->_container
                        ->get('adif_autenticacion.entity_management_guesser')
                        ->initialize($entidad);
    }

    /**
     * Retorna el EntityManager relacionado a la entidad.
     *
     * @param type $entidad
     * @return type
     */
    private function getEntityManagerByClass($entidad) {
        return $this
                ->_registry->getManagerForClass(get_class($entidad));
    }

    /**
     *
     * @param Logger $logger
     */
    private function setLoggerInfo(Logger $logger) {

        $infoMessage = '';



        // Detecta si el usuario es registrado por consola y asigna el valor 0 al id de usuario
        if(ConsoleEvents::COMMAND == 'console.command'){
          $infoMessage .= '0'. self::CSV_SEPARATOR;
        }else{
            $usuario = $this->_container->get('security.context')->getToken()->getUser();
            $infoMessage .= $usuario->getId() . self::CSV_SEPARATOR;
        }


        $infoMessage .= $logger->getIdEntidad() . self::CSV_SEPARATOR;
        $infoMessage .= $logger->getClaseEntidad() . self::CSV_SEPARATOR;
        $infoMessage .= $logger->getQuery() . self::CSV_SEPARATOR;
        $infoMessage .= $logger->getObservacion() . self::CSV_SEPARATOR;
        $infoMessage .= $logger->getAccion() . self::CSV_SEPARATOR;
        $infoMessage .= $logger->getFecha()->format('d/m/Y H:i:s');

        $this->_logger->info($infoMessage);

    }

}
