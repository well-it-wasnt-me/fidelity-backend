<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Auth;

use App\Routing\JwtAuth;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use App\Domain\Auth\UserAuth;

/**
 * Class DocLoginSubmitAction
 * @package App\Action\Auth
 */
final class UserLoginSubmitAction
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var UserAuth
     */
    private UserAuth $userAuth;

    private JwtAuth $jwtAuth;

    /**
     * DocLoginSubmitAction constructor.
     * @param SessionInterface $session Sessiona Management
     * @param UserAuth $userAuth User Authentication
     */
    public function __construct(SessionInterface $session, UserAuth $userAuth, JwtAuth $jwtAuth)
    {
        $this->session = $session;
        $this->userAuth = $userAuth;
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * @param ServerRequestInterface $request Request
     * @param ResponseInterface $response Response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $username = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');
        $role = (string)($data['role'] ?? '');
// Pseudo example
// Check user credentials. You may use an application/domain service and the database here. $user = null;
        $userData = $this->userAuth->authenticate_paz($username, $password, $role);
        if ($userData) {
            set_language($userData['locale']);

            $token = $this->jwtAuth->createJwt(
                [
                    'user_id'       => str_pad($userData['user_id'], 10, "0", STR_PAD_LEFT),
                    'f_name'        => $userData['f_name'],
                    'l_name'        => $userData['l_name'],
                    'email'         => $userData['email'],
                    'account_status'=> $userData['account_status'],
                    'creation_date' => $userData['creation_date'],
                    'locale'        => $userData['locale'],
                    'uid'           => $userData['user_id'],
                    'phone_number'  => $userData['phone_number'],
                    'full_addr'     => $userData['full_addr']
                ]
            );

            // Redirect to protected page
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode(['status'=>'success', 'access_token'=>$token, 'expires_in' => $this->jwtAuth->getLifetime(), 'token_type' => 'Bearer'], JSON_THROW_ON_ERROR));
            return $response->withStatus(200);
        } else {

            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode(['status'=>'error', 'message'=> 'Invalid Login'], JSON_THROW_ON_ERROR));
            return $response->withStatus(401);
        }
    }
}
