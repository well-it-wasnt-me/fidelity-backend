<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Stripe;

use App\Domain\Points\Repository\PointsRepository;
use App\Domain\Transactions\Repository\TransactionRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Action.
 */
final class PaymentSheet
{

    private Responder $responder;
    private PointsRepository $pointsRepository;
    private TransactionRepository $transactionRepository;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     * @param PointsRepository $pointsRepository Point Sistem Repository
     * @param TransactionRepository $transactionRepository Transaction Repository
     */
    public function __construct(Responder $responder, PointsRepository $pointsRepository, TransactionRepository $transactionRepository)
    {
        $this->responder = $responder;
        $this->pointsRepository = $pointsRepository;
        $this->transactionRepository = $transactionRepository;
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

        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        $params = [
            'email' => $data['email'],
            'name'  => $data['name']
        ];

        try {
            $customer = $stripe->customers->create($params);

            $ephemeralKey = $stripe->ephemeralKeys->create(
                [
                    'customer' => $customer->id
                ],
                ['stripe_version' => '2022-11-15']
            );

            $paymentIntent = $stripe->paymentIntents->create(
                [
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'customer' => $customer->id,
                    'automatic_payment_methods' => [
                        'enabled' => true
                    ]
                ]
            );

            $resp = [
                'paymentIntent' => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customer' => $customer->id
            ];

            /**
             * @todo handle eventual error
             */
            $this->pointsRepository->addPoints($data['user_id'], $data['amount'], __('Shop'));
            $cart = json_decode($data['cart'], true);
            $this->transactionRepository->addTransaction($data['user_id'], $cart, $data['amount']);

        } catch (ApiErrorException $e) {
            return $this->responder->withJson($response, ['status' => 'error', 'message' => $e->getMessage()]);
        }

        return $this->responder->withJson($response, $resp);
    }
}
