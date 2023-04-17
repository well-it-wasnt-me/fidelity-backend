<?php

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

/**
 * Class UserTwigMiddleware
 * @package App\Middleware
 */
final class UserTwigMiddleware implements MiddlewareInterface
{

    /**
     * @var Twig
     */
    private Twig $twig;

    private SessionInterface $session;

    /**
     * UserTwigMiddleware constructor.
     * @param Twig $twig Twig Class
     * @param SessionInterface $session Session class
     */
    public function __construct(Twig $twig, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request The Request
     * @param RequestHandlerInterface $handler The Handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $userData = [
            'fname' => $this->session->get('fname'),
            'lname' => $this->session->get('lname'),
        ];

            $this->twig->getEnvironment()->addGlobal('userdata', $userData);


        return $handler->handle($request);
    }
}
