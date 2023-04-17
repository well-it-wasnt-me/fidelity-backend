<?php

namespace App\Action\Auth;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Auth\UserAuth;

/**
 * Class DocLoginSubmitAction
 * @package App\Action\Auth
 */
final class RegisterSubmitAction
{

    /**
     * @var UserAuth
     */
    private UserAuth $userAuth;

    /**
     * DocLoginSubmitAction constructor.
     * @param SessionInterface $session Sessiona Management
     * @param UserAuth $userAuth User Authentication
     */
    public function __construct(UserAuth $userAuth)
    {
        $this->userAuth = $userAuth;
    }

    /**
     * @param ServerRequestInterface $request Request
     * @param ResponseInterface $response Response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        $userData = $this->userAuth->register($data);


        if ($userData) {

            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode(['status'=>'success'], JSON_THROW_ON_ERROR));
            return $response->withStatus(201);
        } else {
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, json_encode(['status'=>'error']));
        }
    }
}
