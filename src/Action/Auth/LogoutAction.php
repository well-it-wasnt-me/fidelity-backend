<?php

namespace App\Action\Auth;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Odan\Session\SessionInterface;

final class LogoutAction
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
 // Logout user
        $this->session->destroy();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('public_doc_login');
        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
