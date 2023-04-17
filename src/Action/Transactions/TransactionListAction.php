<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Transactions;

use App\Domain\Transactions\Repository\TransactionRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class TransactionListAction
{
    private TransactionRepository $transactionRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param TransactionRepository $transactionRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(TransactionRepository $transactionRepository, Responder $responder)
    {
        $this->transactionRepository = $transactionRepository;
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
        $userId = $request->getAttribute('uid');

        if (empty($userId) || $userId === 0) {
            return $this->responder->withJson($response, ['status' => 'error', 'extra' => $userId])->withStatus(401);
        }

        $list = $this->transactionRepository->history($userId, $args['limit']);



        return $this->responder->withJson($response, $list);
    }
}
