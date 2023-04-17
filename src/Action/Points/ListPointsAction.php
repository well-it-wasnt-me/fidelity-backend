<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Points;


use App\Domain\Points\Repository\PointsRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class ListPointsAction
{
    private PointsRepository $pointsRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param PointsRepository $pointsRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(PointsRepository $pointsRepository, Responder $responder)
    {
        $this->pointsRepository = $pointsRepository;
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
        $userId = $request->getAttribute('uid');
        $pointsList = $this->pointsRepository->listPoints($userId);

        return $this->responder->withJson($response, $pointsList);
    }
}
