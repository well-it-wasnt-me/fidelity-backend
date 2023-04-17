<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Prizes;

use App\Domain\Prizes\Repository\PrizesRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class WebClaimUpdateAction
{
    private PrizesRepository $prizesRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param PrizesRepository $prizeRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(PrizesRepository $prizesRepository, Responder $responder)
    {
        $this->prizesRepository = $prizesRepository;
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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $prizeClaim = $this->prizesRepository->updateClaim($data);

        return $this->responder->withJson($response, $prizeClaim);
    }
}
