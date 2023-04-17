<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Security;

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
final class HackAttemptAction
{

    private Monitor $monitor;

    /**
     * DocLoginSubmitAction constructor.
     * @param SessionInterface $session Sessiona Management
     * @param UserAuth $userAuth User Authentication
     */
    public function __construct(Monitor $monitor)
    {
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

            if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }


            $this->monitor->logHackAttempt($ip,$_SERVER['HTTP_USER_AGENT'], $data['url']);

            return $response->withStatus(200);

    }
}
