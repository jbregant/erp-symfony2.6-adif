<?php

namespace ADIF\BaseBundle\Controller;

use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
//use Monolog\Formatter\LineFormatter;
//use Monolog\Handler\StreamHandler;
//use Symfony\Bridge\Monolog\Logger;

class ExceptionController extends BaseExceptionController {

//    private $logger;

    public function __construct(\Twig_Environment $twig, $debug) {
        parent::__construct($twig, $debug);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\Exception\FlattenException $exception
     * @param \Symfony\Component\HttpKernel\Log\DebugLoggerInterface $logger
     * @param type $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $_format = 'html') {
        return new Response($this->twig->display('::exception_full.html.twig', array('status_code' => $exception->getStatusCode())));
        
//        $this->logger = new Logger('ganancia');
//
//        $monologFormat = "%message%\n";
//        $dateFormat = "Y/m/d H:i:s";
//        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);
//
//        $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/error_' . date('Y_m_d') . '.log', Logger::INFO);
//        $streamHandler->setFormatter($monologLineFormat);
//
//        $this->logger->pushHandler($streamHandler);
//
//        $numeroError = rand(1, 999999);
//
//        $this->logger->info("------------------------------------------------------------");
//        $this->logger->info("--------------ERROR-" . $numeroError . "--------------------");
//        $this->logger->info("---------------FECHA:" . date('Y_m_d__H_i_s') . "-----------");
//        $this->logger->info("------------------------------------------------------------");
//        $this->logger->info($exception);
//        $this->logger->info("-------------FIN ERROR " . $numeroError . "-----------------");
//
//        return new Response($this->twig->display('::exception_full.html.twig', array('status_code' => $exception->getStatusCode(),
//                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
//                    'exception' => $exception,
//                    'numeroError' => $numeroError)));
    }

}
