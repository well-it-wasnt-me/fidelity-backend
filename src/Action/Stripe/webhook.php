<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Stripe;

use App\Moebius\Payments;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Stripe\Stripe;

/**
 * Class DocLoginSubmitAction
 * @package App\Action\Auth
 */
final class webhook
{

    private $payments;

    public function __construct(Payments $payments)
    {
        $this->payments = $payments;
    }

    /**
     * @param ServerRequestInterface $request Request
     * @param ResponseInterface $response Response
     * @return bool|int|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {

            $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
            // This is your Stripe CLI webhook secret for testing your endpoint locally.
            $endpoint_secret = getenv('STRIPE_SECRET_KEY_ENDPOINT');

            $payload = @file_get_contents('php://input');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return $response->withStatus(400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return $response->withStatus(400);
        }

        switch ($event->type) {
            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
            case 'checkout.session.completed':
                $session = $event->data->object;
            case 'checkout.session.expired':
                $session = $event->data->object;
            case 'payment_intent.amount_capturable_updated':
                $paymentIntent = $event->data->object;
            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
            case 'payment_intent.created':
                $paymentIntent = $event->data->object;
            case 'payment_intent.partially_funded':
                $paymentIntent = $event->data->object;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
            case 'payment_intent.processing':
                $paymentIntent = $event->data->object;
            case 'payment_intent.requires_action':
                $paymentIntent = $event->data->object;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
            case 'subscription_schedule.aborted':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.canceled':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.completed':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.created':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.expiring':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.released':
                $subscriptionSchedule = $event->data->object;
            case 'subscription_schedule.updated':
                $subscriptionSchedule = $event->data->object;
            // ... handle other event types

            default:
                $elem['extra'] = ['status'=>'unknown', 'extra'=>$event->type];
        }

        $elem = ["payment_intent" => $paymentIntent, "subscription" => $subscriptionSchedule, 'session' => $session];

        if ($this->payments->addPayment($paymentIntent, json_encode($elem))) {
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write((string)json_encode($elem, JSON_THROW_ON_ERROR));
            return $response->withStatus(200);
        }

        return $response->withStatus(500);
    }
}
