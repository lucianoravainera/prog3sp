<?php
namespace Middlewares\Authentication;

use Components\GenericResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Components\Token;
use Enum\UserRole;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public $roleArray;

    public function __construct($roleArray)
    {
        $this->roleArray = $roleArray;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaders()['token'] ?? "";
        $inputRole =  Token::obtenerRol($token) ?? "guest";
        $valid = in_array($inputRole, $this->roleArray);

        if (!$valid) {
            $response = new Response();
            $response->getBody()->write(GenericResponse::obtain(false, "Error de privilegios", $inputRole));
            return $response->withStatus(401);
        }


        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        $resp = new Response();
        $resp->getBody()->write($existingContent);

        return $resp;
    }
}
