<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Points;

use App\Domain\Points\Repository\PointsRepository;
use App\Domain\Points\Service\QRCodeCheker;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class QRCodeAction
{
    private PointsRepository $pointsRepository;

    private Responder $responder;

    private QRCodeCheker $QRCodeCheker;

    /**
     * The constructor.
     *
     * @param PointsRepository $pointsRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(QRCodeCheker $QRCodeCheker, PointsRepository $pointsRepository, Responder $responder)
    {
        $this->pointsRepository = $pointsRepository;
        $this->responder = $responder;
        $this->QRCodeCheker = $QRCodeCheker;
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
        $qrCode = $request->getParsedBody();
        if (empty($userId) || $userId === 0) {
            return $this->responder->withJson($response, ['status' => 'error', 'extra' => $userId])->withStatus(401);
        }

        if (empty($qrCode)) {
            return $this->responder->withJson($response, ['status' => 'error', 'message' => __("Missing Params")])->withStatus(401);
        }

        if (!$this->QRCodeCheker->checkCode($qrCode['token'])) {
            return $this->responder->withJson($response, ['status' => 'error', 'message' => __("Inivalid Token")])->withStatus(401);
        }

        if(!$this->pointsRepository->addPoints($userId, $qrCode['points_amount'], $qrCode['reason'])){
            return $this->responder->withJson($response, ['status' => 'error', 'message' => __("Error while adding points")])->withStatus(401);
        }

        return $this->responder->withJson($response, ['status'=>'success', 'message' => __("Points Added")]);
    }
}
