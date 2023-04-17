<?php
namespace App\Middleware;
use App\Routing\JwtAuth;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
/**
 * JWT Auth middleware.
 */
final class JwtAuthMiddleware implements MiddlewareInterface
{
    private JwtAuth $jwtAuth;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        JwtAuth                  $jwtAuth,
        ResponseFactoryInterface $responseFactory
    )
    {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';
        if (!$token || !$this->jwtAuth->validateToken($token)) {
            return $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');
        }
        return $handler->handle($request);
    }
}