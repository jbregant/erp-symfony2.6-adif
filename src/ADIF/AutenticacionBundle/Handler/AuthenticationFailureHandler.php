<?php

namespace ADIF\AutenticacionBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * AuthenticationFailureHandler
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface {

    /**
     *
     * @var type 
     */
    protected $router;

    /**
     *
     * @var type 
     */
    protected $loginRoute;

    /**
     * 
     * @param Router $router
     * @param type $loginRoute
     */
    public function __construct(Router $router, $loginRoute) {
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    /**
     * 
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        return new RedirectResponse($this->router->generate(
                        $this->loginRoute, // 
                        array('error' => '1')
                )
        );
    }

}
