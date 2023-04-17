<?php
namespace App\Middleware;
use App\Routing\JwtAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
/**
 * JWT Claim middleware.
 */
final class JwtClaimMiddleware implements MiddlewareInterface
{
    private JwtAuth $jwtAuth;
    public function __construct(JwtAuth $jwtAuth)
    {
        $this->jwtAuth = $jwtAuth;
    }
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
        $type = $authorization[0] ?? '';
        $credentials = $authorization[1] ?? '';
        if ($type !== 'Bearer') {
            return $handler->handle($request);
        }
        $token = $this->jwtAuth->validateToken($credentials);
        if ($token) {
// Append valid token
            $request = $request->withAttribute('token', $token);
// Append the user id as request attribute
            $request = $request->withAttribute('uid', $token->claims()->get('uid'));
// Add more claim values as attribute...
//$request = $request->withAttribute('locale', $token->claims()->get('locale'));
        }
        return $handler->handle($request);
    }
}