<?php

use Odan\Session\Middleware\SessionMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    $app->add(TwigMiddleware::class);

    $app->add(SessionMiddleware::class);

    $app->add(\App\Middleware\JwtClaimMiddleware::class);

    $app->add(\App\Middleware\CorsMiddleware::class);

    $app->addRoutingMiddleware();

    $app->add(\App\Middleware\UserTwigMiddleware::class);

    $app->add(BasePathMiddleware::class);

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
};