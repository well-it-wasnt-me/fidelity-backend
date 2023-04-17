<?php

/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Users;

use App\Domain\Users\Repository\UserRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class WebUsersUpdateAction
{
    private UserRepository $userRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param UserRepository $userRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(UserRepository $userRepository, Responder $responder)
    {
        $this->userRepository = $userRepository;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $update = $this->userRepository->updateUser($data);

        return $this->responder->withJson($response, $update);
    }
}
