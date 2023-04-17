<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Auth;

use App\Moebius\Monitor;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use App\Domain\Auth\UserAuth;

/**
 * Class DocLoginSubmitAction
 * @package App\Action\Auth
 */
final class AdminLoginSubmitAction
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var UserAuth
     */
    private UserAuth $userAuth;

    private Monitor $monitor;

    /**
     * DocLoginSubmitAction constructor.
     * @param SessionInterface $session Sessiona Management
     * @param UserAuth $userAuth User Authentication
     */
    public function __construct(SessionInterface $session, UserAuth $userAuth, Monitor $monitor)
    {
        $this->session = $session;
        $this->userAuth = $userAuth;
        $this->monitor = $monitor;
    }

    /**
     * @param ServerRequestInterface $request Request
     * @param ResponseInterface $response Response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $username = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');
        $role = (string)($data['role'] ?? '');
// Pseudo example
// Check user credentials. You may use an application/domain service and the database here. $user = null;
        $userData = $this->userAuth->authenticate($username, $password, $role);
        // Clear all flash messages
        $flash = $this->session->getFlash();
        $flash->clear();
        // Get RouteParser from request to generate the urls
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if ($userData) {
        // Login successfully
        // Clears all session data and regenerate session ID
            $this->session->destroy();
            $this->session->start();
            $this->session->regenerateId();

            $this->session->set('user_id', $userData['user_id']);
            $this->session->set('role', $userData['role']);
            $this->session->set('fname', $userData['f_name']);
            $this->session->set('lname', $userData['l_name']);
            $this->session->set('locale', $userData['locale']);
            $this->session->set('email', $userData['email']);
            $this->session->set('creation_date', $userData['creation_date']);
            $this->session->set('account_status', $userData['account_status']);
            $flash->add('success', 'Login successfully');

            set_language($userData['locale']);

            if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }


            $this->monitor->logLogin($ip,$_SERVER['HTTP_USER_AGENT'],$userData['user_id']);


            // Redirect to protected page
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode(['status'=>'success', 'user_data'=>$userData], JSON_THROW_ON_ERROR));
            return $response->withStatus(200);
        } else {
            $flash->add('error', 'Login failed!');
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode(['status'=>'error', 'message'=> 'Invalid Login', 'extra' => ['username' => $username, 'password' => $password]], JSON_THROW_ON_ERROR));
            return $response->withStatus(401);
        }
    }
}
