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
final class AddTransactionAction
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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = $request->getAttribute('uid');
        $data = $request->getParsedBody();

        if (empty($userId) || $userId === 0) {
            return $this->responder->withJson($response, ['status' => 'error', 'extra' => $userId])->withStatus(401);
        }
        $cart = json_decode($data['cart'], true);
        $trx_id = $this->transactionRepository->addTransaction($userId, $cart, $data['amount']);

        return $this->responder->withJson($response, ['transaction_id' => $trx_id]);
    }
}
